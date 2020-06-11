<?php // phpcs:ignore
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Creates Billmate refund data.
 *
 * @class    BOM_Refund_Data_Helper
 * @package  Avarda_Order_Management/Classes/Requests/Helpers
 * @category Class
 * @author   Krokedil <info@krokedil.se>
 */
class BOM_Refund_Data_Helper {
	/**
	 * Creates refund data
	 *
	 * @param int    $order_id Order id.
	 * @param int    $refund_order_id Refund order id.
	 * @param int    $amount Refund amount.
	 * @param string $reason Refund reason.
	 * @return array
	 */
	public static function create_refund_data( $order_id, $refund_order_id, $amount, $reason ) {
		$data = array();
		if ( '' === $reason ) {
			$reason = '';
		} else {
			$reason = ' (' . $reason . ')';
		}
		if ( null !== $refund_order_id ) {
			// Get refund order data.
			$refund_order      = wc_get_order( $refund_order_id );
			$refunded_items    = $refund_order->get_items();
			$refunded_shipping = $refund_order->get_items( 'shipping' );
			$refunded_fees     = $refund_order->get_items( 'fee' );

			// Set needed variables for refunds.
			$refund_data = array();

			$order = wc_get_order( $order_id );
			// Refund payment data.
			$refund_data['part_credit'] = self::get_refund_payment_data( $order, $refund_order );

			// Item refund.
			if ( $refunded_items ) {
				foreach ( $refunded_items as $item ) {
					$refund_data['articles_data'] = self::get_refund_articles_data( $item );
				}
			}

			// Fee item refund.
			if ( $refunded_fees ) {
				foreach ( $refunded_fees as $fee ) {
					$refund_data['articles_data'] = self::get_refund_articles_data( $fee );
				}
			}

			// Handling.
			$refund_data['handling_data'] = self::get_refund_handling_data( $refund_order );

			// Shipping item refund.
			$refund_data['shipping_data'] = self::get_refund_shipping_data( $refund_order );

			// Total.
			$refund_data['total_data'] = self::get_refund_total_data( $refund_order );
		}

		return $refund_data;

	}

	/**
	 * Gets refunded order
	 *
	 * @param int $order_id Order id.
	 * @return string
	 */
	public static function get_refunded_order( $order_id ) {
		$query_args      = array(
			'fields'         => 'id=>parent',
			'post_type'      => 'shop_order_refund',
			'post_status'    => 'any',
			'posts_per_page' => -1,
		);
		$refunds         = get_posts( $query_args );
		$refund_order_id = array_search( $order_id, $refunds ); // phpcs:ignore
		if ( is_array( $refund_order_id ) ) {
			foreach ( $refund_order_id as $key => $value ) {
				if ( ! get_post_meta( $value, '_krokedil_refunded' ) ) {
					$refund_order_id = $value;
					break;
				}
			}
		}
		return $refund_order_id;
	}

	/**
	 * Get refund payment data.
	 *
	 * @param WC_Order        $order The WooCommerce order.
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return array
	 */
	private static function get_refund_payment_data( $order, $refund_order ) {
		return BOM_Refund_Data_Payment_Data_Helper::get_refund_type( $order, $refund_order );
	}

	/**
	 * Get refund articles.
	 *
	 * @param WC_Order_Item $order_item WooCommerce Order Item.
	 * @return array
	 */
	private static function get_refund_articles_data( $order_item ) {
		return array(
			'artnr'      => BOM_Refund_Data_Articles_Helper::get_article_number( $order_item ),
			'title'      => BOM_Refund_Data_Articles_Helper::get_title( $order_item ),
			'quantity'   => BOM_Refund_Data_Articles_Helper::get_quantity( $order_item ),
			'aprice'     => BOM_Refund_Data_Articles_Helper::get_article_price( $order_item ),
			'withouttax' => BOM_Refund_Data_Articles_Helper::get_without_tax( $order_item ),
			'taxrate'    => BOM_Refund_Data_Articles_Helper::get_tax_rate( $order_item ),
		);
	}

	/**
	 * Get refund handling.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return array
	 */
	private static function get_refund_handling_data( $refund_order ) {
		return array(
			'withouttax' => 0,
			'taxrate'    => BOM_Refund_Data_Cart_Helper::get_handling_tax_rate( $refund_order ),
		);
	}

	/**
	 * Get refund shipping.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return array
	 */
	private static function get_refund_shipping_data( $refund_order ) {
		return array(
			'withouttax' => BOM_Refund_Data_Cart_Helper::get_shipping_without_tax( $refund_order ),
			'taxrate'    => BOM_Refund_Data_Cart_Helper::get_shipping_tax_rate( $refund_order ),
		);
	}

	/**
	 * Get refund total.
	 *
	 * @param WC_Refund_Order $refund_order The WooCommerce refund order.
	 * @return array
	 */
	private static function get_refund_total_data( $refund_order ) {
		return array(
			'withouttax' => BOM_Refund_Data_Cart_Helper::get_total_without_tax( $refund_order ),
			'tax'        => BOM_Refund_Data_Cart_Helper::get_total_tax( $refund_order ),
			'rounding'   => 0,
			'withtax'    => BOM_Refund_Data_Cart_Helper::get_total_with_tax( $refund_order ),
		);
	}
}
