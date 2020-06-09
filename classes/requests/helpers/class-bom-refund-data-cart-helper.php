<?php
/**
 * Refund order cart helper.
 *
 * @package Billmate_Order_Management/Classes/Helpers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Refund order Cart helper class.
 */
class BOM_Refund_Data_Cart_Helper {
	/**
	 * Get order handling without tax.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return int $handling_without_tax handling excl tax.
	 */
	public static function get_handling_without_tax( $refund_order ) {
		return round( ( abs( $refund_order->get_total() ) - abs( $refund_order->get_total_tax() ) ) * 100 );
	}

	/**
	 * Get order handling tax rate.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return int $handling_tax_rate handling tax rate.
	 */
	public static function get_handling_tax_rate( $refund_order ) {
		$tax_rate = ( abs( $refund_order->get_total_tax() ) > 0 ) ? abs( $refund_order->get_total_tax() ) / ( abs( $refund_order->get_total() ) - abs( $refund_order->get_total_tax() ) ) * 100 : 0;
		return round( $tax_rate );
	}

	/**
	 * Get order shipping without tax.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return int $shipping_without_tax shipping excl tax.
	 */
	public static function get_shipping_without_tax( $refund_order ) {
		return round( abs( $refund_order->get_shipping_total() ) * 100 );
	}

	/**
	 * Get order shipping tax rate.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return int $shipping_tax_rate shipping tax rate.
	 */
	public static function get_shipping_tax_rate( $refund_order ) {
		$tax_rate = ( abs( $refund_order->get_shipping_tax() ) > 0 ) ? abs( $refund_order->get_shipping_tax() ) / abs( $refund_order->get_shipping_total() ) * 100 : 0;
		return round( $tax_rate );
	}

	/**
	 * Get order total excluding tax.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return int $total_without_tax order total excl tax.
	 */
	public static function get_total_without_tax( $refund_order ) {
		return round( ( abs( $refund_order->get_total() ) - abs( $refund_order->get_total_tax() ) ) * 100 );
	}

	/**
	 * Get order total tax.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return int $total_tax order total tax.
	 */
	public static function get_total_tax( $refund_order ) {
		return round( abs( $refund_order->get_total_tax() ) * 100 );
	}

	/**
	 * Get order total inclusive tax.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return int $total_with_tax order total incl tax.
	 */
	public static function get_total_with_tax( $refund_order ) {
		return round( abs( $refund_order->get_total() ) * 100 );
	}
}
