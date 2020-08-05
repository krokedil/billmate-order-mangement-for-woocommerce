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
		$bco_transaction_id = get_post_meta( $order_id, '_billmate_transaction_id', true );
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

		// Check if we have a transaction id.
		$bco_transaction_id = get_post_meta( $order_id, '_billmate_transaction_id', true );
		if ( empty( $bco_transaction_id ) ) {
			$order->add_order_note( __( 'Billmate Checkout reservation could not be activated. Missing Billmate transaction id.', 'billmate-order-managment-for-woocommerce' ) );
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

		// Check Billmate settings to see if we have the ordermanagement enabled.
		$billmate_settings = get_option( 'woocommerce_bco_settings' );
		$order_management  = 'yes' === $billmate_settings['order_management'] ? true : false;
		if ( ! $order_management ) {
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

		if ( 'paid' === $bco_status || 'created' === $bco_status ) {
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
		}
		$order->add_order_note( __( 'Billmate Checkout order could not be refunded.', 'billmate-checkout-for-woocommerce' ) );
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