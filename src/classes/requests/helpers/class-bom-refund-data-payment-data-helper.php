<?php
/**
 * Refund Payment data helper.
 *
 * @package Billmate_Order_Management/Classes/Helpers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Refund Payment data helper class.
 */
class BOM_Refund_Data_Payment_Data_Helper {

	/**
	 * Get refund type.
	 *
	 * @param WC_Order        $order The WooCommerce order.
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return string $refund_type Full or Partial refund.
	 */
	public static function get_refund_type( $order, $refund_order ) {
		$order_total        = round( $order->get_total() * 100 );
		$refund_order_total = round( abs( $refund_order->get_total() ) * 100 );

		if ( $order_total === $refund_order_total ) {
			// Full refund.
			return 'false';
		} else {
			// Partial refund.
			return 'true';
		}

	}
}
