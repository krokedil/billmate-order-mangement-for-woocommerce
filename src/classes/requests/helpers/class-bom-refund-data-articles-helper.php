<?php
/**
 * Articles helper.
 *
 * @package Billmate_Order_Management/Classes/Helpers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Refund order Articles helper class.
 */
class BOM_Refund_Data_Articles_Helper {

	/**
	 * Get refund order item article number.
	 *
	 * Returns SKU or product ID.
	 *
	 * @param object $refund_order_item Product object.
	 * @return string $article_number Order item article number.
	 */
	public static function get_article_number( $refund_order_item ) {
		$product = $refund_order_item->get_product();
		if ( $product->get_sku() ) {
			$article_number = $product->get_sku();
		} else {
			$article_number = $product->get_id();
		}

		return substr( (string) $article_number, 0, 255 );
	}

	/**
	 * Get refund order item title.
	 *
	 * @param array $refund_order_item order item.
	 * @return string $item_title order item title.
	 */
	public static function get_title( $refund_order_item ) {
		$item_title = $refund_order_item->get_name();

		return strip_tags( $item_title ); //phpcs:ignore
	}

	/**
	 * Get refund order item quantity
	 *
	 * @param array $refund_order_item order item.
	 * @return int $item_quantity order item quantity.
	 */
	public static function get_quantity( $refund_order_item ) {
		return abs( $refund_order_item->get_quantity() );
	}

	/**
	 * Get refund order item article price excluding tax
	 *
	 * @param WC_Order_Item $original_order_item original order item.
	 * @return int $item_price Item price.
	 */
	public static function get_article_price( $original_order_item ) {
		$item_subtotal = $original_order_item->get_subtotal() * 100;
		return round( $item_subtotal );
	}

	/**
	 * Get refund order row total articles price excluding tax.
	 *
	 * @param array $refund_order_item order item.
	 * @return int $item_price Item price.
	 */
	public static function get_without_tax( $refund_order_item ) {
		return round( abs( $refund_order_item->get_total() ) * 100 );
	}

	/**
	 * Get refund order item article tax rate.
	 *
	 * @param array $refund_order_item order item.
	 * @return int $item_price Item price.
	 */
	public static function get_tax_rate( $refund_order_item ) {
		$tax_rates          = WC_Tax::get_rates( $refund_order_item->get_tax_class() );
		$tax_rate           = reset( $tax_rates );
		$formatted_tax_rate = $tax_rate['rate'];
		return round( $formatted_tax_rate );
	}

}
