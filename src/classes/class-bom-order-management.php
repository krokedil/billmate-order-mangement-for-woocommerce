<?php
/**
 * Order management class file.
 *
 * @package Billmate_Order_Management/Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Order management class.
 */
class BOM_Order_Management {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_order_status_cancelled', array( $this, 'cancel_reservation' ) );
		add_action( 'woocommerce_order_status_completed', array( $this, 'activate_reservation' ) );

		// Refund an order.
		add_filter( 'wc_billmate_checkout_process_refund', array( $this, 'refund_billmate_order' ), 10, 4 );

		// Update an order.
		add_action( 'woocommerce_saved_order_items', array( $this, 'update_bco_order_items' ), 10, 2 );
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'update_bco_order_address' ), 45, 2 );
	}

	/**
	 * Cancels the order with the payment provider.
	 *
	 * @param string $order_id The WooCommerce order id.
	 * @return void
	 */
	public function cancel_reservation( $order_id ) {
		$order = wc_get_order( $order_id );
		// If this order wasn't created using bco or billmate_checkout payment method, bail.
		if ( ! in_array( $order->get_payment_method(), array( 'bco', 'billmate_checkout', 'billmate_partpayment', 'billmate_cardpay', 'billmate_invoice' ), true ) ) {
			return;
		}

		// If the order has not been paid for, bail.
		if ( empty( $order->get_date_paid() ) ) {
			return;
		}

		// Check Billmate settings to see if we have the ordermanagement enabled.
		$billmate_settings = get_option( 'woocommerce_bco_settings' );
		$auto_cancel       = 'yes' === $billmate_settings['auto_cancel'] ? true : false;
		if ( ! $auto_cancel ) {
			return;
		}

		$subscription = $this->check_if_subscription( $order );

		// Check if we have a transaction id.
		if ( 'bco' === $order->get_payment_method() ) {
			$bco_transaction_id = get_post_meta( $order_id, '_billmate_transaction_id', true );
		} else {
			// Old Billmate plugin.
			$bco_transaction_id = get_post_meta( $order_id, 'billmate_invoice_id', true );
		}
		if ( empty( $bco_transaction_id ) ) {
			$order->add_order_note( __( 'Billmate Checkout reservation could not be cancelled. Missing Billmate transaction id.', 'billmate-order-managment-for-woocommerce' ) );
			$order->set_status( 'on-hold' );
			return;
		}

		// If this reservation was already cancelled, do nothing.
		if ( get_post_meta( $order_id, '_billmate_reservation_cancelled', true ) ) {
			$order->add_order_note( __( 'Could not cancel Billmate Checkout reservation, Billmate Checkout reservation is already cancelled.', 'billmate-order-managment-for-woocommerce' ) );
			return;
		}

		// Cancel order.
		$billmate_order = BOM_WC()->api->request_cancel_payment( $bco_transaction_id );

		// Check if we were successful.
		if ( is_wp_error( $billmate_order ) ) {
			// If error save error message.
			$code          = $billmate_order->get_error_code();
			$message       = $billmate_order->get_error_message();
			$text          = __( 'Billmate API Error on Billmate cancel order: ', 'billmate-checkout-for-woocommerce' ) . '%s %s';
			$formated_text = sprintf( $text, $code, $message );
			$order->add_order_note( $formated_text );
			$order->set_status( 'on-hold' );
		} else {
			// Add time stamp, used to prevent duplicate activations for the same order.
			update_post_meta( $order_id, '_billmate_reservation_cancelled', current_time( 'mysql' ) );
			$order->add_order_note( __( 'Billmate reservation was successfully cancelled.', 'billmate-checkout-for-woocommerce' ) );
		}
	}

	/**
	 * Activate the order with the payment provider.
	 *
	 * @param string $order_id The WooCommerce order id.
	 * @return void
	 */
	public function activate_reservation( $order_id ) {
		$order = wc_get_order( $order_id );
		// If this order wasn't created using bco or billmate_checkout payment method, bail.
		if ( ! in_array( $order->get_payment_method(), array( 'bco', 'billmate_checkout', 'billmate_invoice', 'billmate_partpayment' ), true ) ) {
			return;
		}

		// Check if the order has been paid.
		if ( empty( $order->get_date_paid() ) ) {
			return;
		}

		// Don't try to activate direct payment method orders.
		// 16=Bank, 24=Card/Bank, 32=Cash (Receipt), 1024=Swish.
		// Only check this for bco payment method. Not the old billmate_checkout.
		if ( 'bco' === $order->get_payment_method() && in_array( get_post_meta( $order_id, '_billmate_payment_method_id', true ), array( '16', '24', '32', '1024' ), true ) ) {
			return;
		}

		// Don't try to activate direct payment method orders.
		// Only check this for the old billmate_checkout payment method.
		if ( 'billmate_checkout' === $order->get_payment_method() && in_array( $order->get_payment_method_title(), array( 'Billmate Checkout (Direktbetalning)', 'Billmate Checkout (Swish)' ), true ) ) {
			return;
		}

		// Check Billmate settings to see if we have the ordermanagement enabled.
		$billmate_settings = get_option( 'woocommerce_bco_settings' );
		$auto_capture      = 'yes' === $billmate_settings['auto_capture'] ? true : false;
		if ( ! $auto_capture ) {
			return;
		}

		$subscription = $this->check_if_subscription( $order );
		// If this is a free subscription then stop here.
		if ( $subscription && 0 >= $order->get_total() ) {
			return;
		}

		// Check if we have a transaction id.
		if ( 'bco' === $order->get_payment_method() ) {
			$bco_transaction_id = get_post_meta( $order_id, '_billmate_transaction_id', true );
		} else {
			// Old Billmate plugin.
			$bco_transaction_id = get_post_meta( $order_id, 'billmate_invoice_id', true );
		}

		if ( empty( $bco_transaction_id ) ) {
			$order->add_order_note( __( 'Billmate Checkout reservation could not be activated. Missing Billmate transaction id.', 'billmate-order-managment-for-woocommerce' ) );
			$order->set_status( 'on-hold' );
			return;
		}

		// If this reservation was already activated, do nothing.
		if ( get_post_meta( $order_id, '_billmate_reservation_activated', true ) ) {
			$order->add_order_note( __( 'Could not activate Billmate Checkout reservation, Billmate Checkout reservation is already activated.', 'billmate-order-management-for-woocommerce' ) );
			return;
		}

		// Activate order.
		$billmate_order = BOM_WC()->api->request_activate_payment( $bco_transaction_id );

		// Check if we were successful.
		if ( is_wp_error( $billmate_order ) ) {
			// If error save error message.
			$code          = $billmate_order->get_error_code();
			$message       = $billmate_order->get_error_message();
			$text          = __( 'Billmate API Error on Billmate activate order: ', 'billmate-order-management-for-woocommerce' ) . '%s %s';
			$formated_text = sprintf( $text, $code, $message );
			$order->add_order_note( $formated_text );
			$order->set_status( 'on-hold' );
		} else {
			// Add time stamp, used to prevent duplicate activations for the same order.
			update_post_meta( $order_id, '_billmate_reservation_activated', current_time( 'mysql' ) );
			$order->add_order_note( __( 'Billmate reservation was successfully activated.', 'billmate-order-management-for-woocommerce' ) );
		}

	}

	/**
	 * WooCommerce Refund.
	 *
	 * @param bool   $result Refund attempt result.
	 * @param string $order_id The WooCommerce order ID.
	 * @param float  $amount The amount to be refunded.
	 * @param string $reason The reason given for the refund.
	 * @return boolean
	 */
	public function refund_billmate_order( $result, $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );
		// If this order wasn't created using aco payment method, bail.
		if ( 'bco' !== $order->get_payment_method() ) {
			return false;
		}

		// Check if we have a transaction id.
		$bco_transaction_id = get_post_meta( $order_id, '_billmate_transaction_id', true );
		if ( empty( $bco_transaction_id ) ) {
			$order->add_order_note( __( 'Billmate Checkout order could not be refunded. Missing Billmate transaction id.', 'billmate-checkout-for-woocommerce' ) );
			$order->set_status( 'on-hold' );
			return false;
		}

		$subscription = $this->check_if_subscription( $order );

		// Get the Billmate order.
		$billmate_order_tmp = BOM_WC()->api->request_get_payment( $bco_transaction_id );
		if ( is_wp_error( $billmate_order_tmp ) ) {
			// If error save error message.
			$code          = $billmate_order_tmp->get_error_code();
			$message       = $billmate_order_tmp->get_error_message();
			$text          = __( 'Billmate API Error on get billmate order before refund: ', 'billmate-checkout-for-woocommerce' ) . '%s %s';
			$formated_text = sprintf( $text, $code, $message );
			$order->add_order_note( $formated_text );
			return false;
		}

		$bco_status = strtolower( $billmate_order_tmp['data']['PaymentData']['status'] );

		if ( 'paid' === $bco_status || 'created' === $bco_status || 'partpayment' === $bco_status ) {
			$refund_order_id = BOM_Refund_Data_Helper::get_refunded_order( $order_id );
			$refund_data     = BOM_Refund_Data_Helper::create_refund_data( $order_id, $refund_order_id, $amount, $reason );
			$billmate_order  = BOM_WC()->api->request_credit_payment( $bco_transaction_id, $refund_data );
			if ( is_wp_error( $billmate_order ) ) {
				// If error save error message and return false.
				$code          = $billmate_order->get_error_code();
				$message       = $billmate_order->get_error_message();
				$text          = __( 'Billmate API Error on Billmate refund: ', 'billmate-checkout-for-woocommerce' ) . '%s %s';
				$formated_text = sprintf( $text, $code, $message );
				$order->add_order_note( $formated_text );
				return false;
			}
			$order->add_order_note( __( 'Billmate Checkout order was successfully refunded.', 'billmate-checkout-for-woocommerce' ) );
			return true;
		} else {
			// Translators: Billmate order status.
			$note = sprintf( __( 'Billmate Checkout order could not be refunded because order has status <em>%s</em> in Billmate Online.', 'billmate-checkout-for-woocommerce' ), sanitize_key( $bco_status ) );
			$order->add_order_note( $note );
			return false;
		}

	}

	/**
	 * Checks if the order is a subscription order or not
	 *
	 * @param object $order WC_Order object.
	 * @return boolean
	 */
	public function check_if_subscription( $order ) {
		if ( class_exists( 'WC_Subscriptions_Order' ) && wcs_order_contains_renewal( $order ) ) {
			return true;
		}
		if ( class_exists( 'WC_Subscriptions_Order' ) && wcs_order_contains_subscription( $order ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Updates Billmate order items.
	 *
	 * @param int $order_id Order ID.
	 */
	public function update_bco_order_items( $order_id ) {
		if ( ! is_ajax() ) {
			return;
		}

		$order = wc_get_order( $order_id );

		// Check if the order has been paid.
		if ( null === $order->get_date_paid() ) {
			return;
		}

		// Not going to do this for non-BCO.
		if ( 'bco' !== $order->get_payment_method() ) {
			return;
		}

		// Changes only possible if order is set to On Hold.
		if ( 'on-hold' !== $order->get_status() ) {
			return;
		}

		// Check Billmate settings to see if we have the ordermanagement enabled.
		$billmate_settings = get_option( 'woocommerce_bco_settings' );
		$auto_update       = 'yes' === $billmate_settings['auto_update'] ? true : false;

		if ( ! $auto_update ) {
			return;
		}

		// Retrieve transaction id from order post meta.
		$bco_transaction_id = get_post_meta( $order_id, '_billmate_transaction_id', true );
		// Retrieve Billmate order.
		$billmate_order = BCO_WC()->api->request_get_payment( $bco_transaction_id );

		if ( is_wp_error( $billmate_order ) ) {
			$order->add_order_note( 'Billmate order could not be updated due to an error.' );

			return;
		}
		$not_allowed_statuses = array( 'Paid', 'Pending', 'Factoring', 'PartPayment', 'Handling' );
		if ( ! in_array( $billmate_order['data']['PaymentData']['status'], $not_allowed_statuses, true ) ) {
			$response = BOM_WC()->api->request_update_payment( $order_id );
			if ( ! is_wp_error( $response ) ) {
				$order->add_order_note( 'Billmate order updated.' );
			} else {
				$order_note = 'Could not update Billmate order lines.';
				if ( '' !== $response->get_error_message() ) {
					$order_note .= ' ' . $response->get_error_message() . '.';
				}
				$order->add_order_note( $order_note );
			}
		} else {
			$order->add_order_note( __( 'Order can not be updated in Billmate Online because the current Billmate order status does not allow this.', 'billmate-checkout-for-woocommerce' ) );
		}
	}

	/**
	 * Updates Billmate order address.
	 *
	 * @param int $order_id Order ID.
	 */
	public function update_bco_order_address( $order_id ) {
		$order = wc_get_order( $order_id );

		// Check if the order has been paid.
		if ( null === $order->get_date_paid() ) {
			return;
		}

		// Check if the order has been completed.
		if ( $order->get_date_completed() ) {
			return;
		}

		// Not going to do this for non-BCO.
		if ( 'bco' !== $order->get_payment_method() ) {
			return;
		}

		// Changes only possible if order is set to On Hold.
		if ( 'on-hold' !== $order->get_status() ) {
			return;
		}

		// Check Billmate settings to see if we have the ordermanagement enabled.
		$billmate_settings = get_option( 'woocommerce_bco_settings' );
		$auto_update       = 'yes' === $billmate_settings['auto_update'] ? true : false;

		if ( ! $auto_update ) {
			return;
		}

		// Retrieve transaction id from order post meta.
		$bco_transaction_id = get_post_meta( $order_id, '_billmate_transaction_id', true );
		// Retrieve Billmate order.
		$billmate_order = BCO_WC()->api->request_get_payment( $bco_transaction_id );

		if ( is_wp_error( $billmate_order ) ) {
			$order->add_order_note( 'Billmate order could not be updated due to an error.' );
			return;
		}
		$not_allowed_statuses = array( 'Paid', 'Pending', 'Factoring', 'PartPayment', 'Handling' );
		if ( ! in_array( $billmate_order['data']['PaymentData']['status'], $not_allowed_statuses, true ) ) {
			$response = BOM_WC()->api->request_update_payment( $order_id );
			if ( ! is_wp_error( $response ) ) {
				$order->add_order_note( 'Customer address updated in Billmate order.' );
			} else {
				$order_note = 'Could not update address in Billmate order.';
				if ( '' !== $response->get_error_message() ) {
					$order_note .= ' ' . $response->get_error_message() . '.';
				}
				$order->add_order_note( $order_note );
			}
		} else {
			$order->add_order_note( __( 'Order can not be updated in Billmate Online because the current Billmate order status does not allow this.', 'billmate-checkout-for-woocommerce' ) );
		}
	}
}
