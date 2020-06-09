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
	}

	/**
	 * Cancels the order with the payment provider.
	 *
	 * @param string $order_id The WooCommerce order id.
	 * @return void
	 */
	public function cancel_reservation( $order_id ) {
		$order = wc_get_order( $order_id );
		// If this order wasn't created using aco payment method, bail.
		if ( 'bco' !== $order->get_payment_method() ) {
			return;
		}

		// Check Billmate settings to see if we have the ordermanagement enabled.
		$billmate_settings = get_option( 'woocommerce_bco_settings' );
		$order_management  = 'yes' === $billmate_settings['order_management'] ? true : false;
		if ( ! $order_management ) {
			return;
		}

		$subscription = $this->check_if_subscription( $order );

		// Check if we have a order number.
		$order_number = get_post_meta( $order_id, '_billmate_transaction_id', true );
		if ( empty( $purchase_id ) ) {
			$order->add_order_note( __( 'Billmate Order_Management reservation could not be cancelled. Missing Billmate order number.', 'billmate-order-managment-for-woocommerce' ) );
			$order->set_status( 'on-hold' );
			return;
		}

		// If this reservation was already cancelled, do nothing.
		if ( get_post_meta( $order_id, '_billmate_reservation_cancelled', true ) ) {
			$order->add_order_note( __( 'Could not cancel Billmate Checkout reservation, Billmate Checkout reservation is already cancelled.', 'billmate-order-managment-for-woocommerce' ) );
			return;
		}

		// Cancel order.
		$billmate_order = BOM_WC()->api->request_cancel_payment( $order_number );

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
		// If this order wasn't created using aco payment method, bail.
		if ( 'bco' !== $order->get_payment_method() ) {
			return;
		}

		// Check Billmate settings to see if we have the ordermanagement enabled.
		$billmate_settings = get_option( 'woocommerce_bco_settings' );
		$order_management  = 'yes' === $billmate_settings['order_management'] ? true : false;
		if ( ! $order_management ) {
			return;
		}

		$subscription = $this->check_if_subscription( $order );
		// If this is a free subscription then stop here.
		if ( $subscription && 0 >= $order->get_total() ) {
			return;
		}

		// Check if we have a order number.
		$order_number = get_post_meta( $order_id, '_billmate_transaction_id', true );
		if ( empty( $order_number ) ) {
			$order->add_order_note( __( 'Billmate Checkout reservation could not be activated. Missing Billmate order number.', 'billmate-order-managment-for-woocommerce' ) );
			$order->set_status( 'on-hold' );
			return;
		}

		// If this reservation was already activated, do nothing.
		if ( get_post_meta( $order_id, '_billmate_reservation_activated', true ) ) {
			$order->add_order_note( __( 'Could not activate Billmate Checkout reservation, Billmate Checkout reservation is already activated.', 'billmate-order-management-for-woocommerce' ) );
			$order->set_status( 'on-hold' );
			return;
		}

		// Activate order.
		$billmate_order = BOM_WC()->api->request_activate_payment( $order_number );
		error_log( 'billmate BOM ordER ' . var_export( $billmate_order, true ) );
		// Check if we were successful.
		if ( is_wp_error( $billmate_order ) ) { // handle error.
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
	 * @param string $order_id The WooCommerce order ID.
	 * @param float  $amount The amount to be refunded.
	 * @param string $reason The reason given for the refund.
	 * @return boolean
	 */
	public function refund_payment( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );
		// If this order wasn't created using aco payment method, bail.
		if ( 'aco' !== $order->get_payment_method() ) {
			return;
		}

		// Check Avarda settings to see if we have the ordermanagement enabled.
		$avarda_settings  = get_option( 'woocommerce_aco_settings' );
		$order_management = 'yes' === $avarda_settings['order_management'] ? true : false;
		if ( ! $order_management ) {
			return;
		}

		// Check if we have a purchase id.
		$purchase_id = get_post_meta( $order_id, '_wc_avarda_purchase_id', true );
		if ( empty( $purchase_id ) ) {
			$order->add_order_note( __( 'Avarda Checkout order could not be refunded. Missing Avarda purchase id.', 'avarda-checkout-for-woocommerce' ) );
			$order->set_status( 'on-hold' );
			return;
		}

		$subscription = $this->check_if_subscription( $order );

		// Get the Avarda order.
		// TODO: Should we do different request if order is subcription?
		$avarda_order_tmp = ( $subscription ) ? ACO_WC()->api->request_get_payment( $purchase_id, true ) : ACO_WC()->api->request_get_payment( $purchase_id, true );
		if ( is_wp_error( $avarda_order_tmp ) ) {
			// If error save error message.
			$code          = $avarda_order_tmp->get_error_code();
			$message       = $avarda_order_tmp->get_error_message();
			$text          = __( 'Avarda API Error on get avarda order before refund: ', 'avarda-checkout-for-woocommerce' ) . '%s %s';
			$formated_text = sprintf( $text, $code, $message );
			$order->add_order_note( $formated_text );
			return false;
		}

		// Check if B2C or B2B.
		$aco_state = '';
		if ( 'B2C' === $avarda_order_tmp['mode'] ) {
			$aco_state = $avarda_order_tmp['b2C']['step']['current'];
		} elseif ( 'B2B' === $avarda_order_tmp['mode'] ) {
			$aco_state = $avarda_order_tmp['b2B']['step']['current'];
		}

		if ( 'Completed' === $aco_state ) {
			$refund_order_id = ACO_Helper_Create_Refund_Data::get_refunded_order( $order_id );
			$refunded_items  = ACO_Helper_Create_Refund_Data::create_refund_data( $order_id, $refund_order_id, $amount, $reason );
			$avarda_order    = ACO_WC()->api->request_return_order( $order_id, $refunded_items );
			if ( is_wp_error( $avarda_order ) ) {
				// If error save error message and return false.
				$code          = $avarda_order->get_error_code();
				$message       = $avarda_order->get_error_message();
				$text          = __( 'Avarda API Error on Avarda refund: ', 'avarda-checkout-for-woocommerce' ) . '%s %s';
				$formated_text = sprintf( $text, $code, $message );
				$order->add_order_note( $formated_text );
				return false;
			}
			$order->add_order_note( __( 'Avarda Checkout order was successfully refunded.', 'avarda-checkout-for-woocommerce' ) );
			return true;
		}
		$order->add_order_note( __( 'Avarda Checkout order could not be refunded.', 'avarda-checkout-for-woocommerce' ) );
		return false;

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
}
