<?php
/**
 *
 * Test_BOM_Refund_Data_Articles_Helper class
 *
 * @package category
 */

/**
 * Test_BOM_Refund_Data_Articles_Helper class
 */
class Test_BOM_Refund_Data_Articles_Helper extends AKrokedil_Unit_Test_Case {

	/**
	 * Tax rate ids.
	 *
	 * @var array
	 */
	public $tax_rate_ids = array();
	/**
	 * Tax rate ids.
	 *
	 * @var array
	 */
	public $tax_classes = array();
	/**
	 * Orders.
	 *
	 * @var array
	 */
	public $orders = array();

	/**
	 * Test BOM_Refund_Data_Articles_Helper::get_article_number
	 *
	 * @return void
	 */
	public function test_get_article_number_with_sku() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$product = $item->get_product();
			$product->set_sku( 'SKU123' );
			$product->save();
			$article_number = ( new BOM_Refund_Data_Articles_Helper() )->get_article_number( $item );
			$this->assertEquals( 'SKU123', $article_number );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Articles_Helper::get_article_number
	 *
	 * @return void
	 */
	public function test_get_article_number_without_sku() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$product        = $item->get_product();
			$article_number = ( new BOM_Refund_Data_Articles_Helper() )->get_article_number( $item );
			$this->assertEquals( $product->get_id(), $article_number );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Articles_Helper::get_title
	 *
	 * @return void
	 */
	public function test_get_title() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$title = ( new BOM_Refund_Data_Articles_Helper() )->get_title( $item );
			$this->assertEquals( 'Default product name', $title );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Articles_Helper::get_quantity
	 *
	 * @return void
	 */
	public function test_get_quantity() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$quantity = ( new BOM_Refund_Data_Articles_Helper() )->get_quantity( $item );
			$this->assertEquals( 1, $quantity );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Articles_Helper::get_article_price
	 *
	 * @return void
	 */
	public function test_get_article_price() {
		// Create tax rates.
		$this->tax_rate_ids[] = $this->create_tax_rate( '25' );
		$this->tax_rate_ids[] = $this->create_tax_rate( '12' );
		$this->tax_rate_ids[] = $this->create_tax_rate( '6' );

		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_article_price( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_article_price( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_article_price( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_article_price( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_article_price( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_article_price( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Articles_Helper::get_without_tax
	 *
	 * @return void
	 */
	public function test_get_without_tax() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_without_tax( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_without_tax( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_without_tax( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_without_tax( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_without_tax( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 5000, ( new BOM_Refund_Data_Articles_Helper() )->get_without_tax( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Articles_Helper::get_tax_rate
	 *
	 * @return void
	 */
	public function test_get_tax_rate() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 25, ( new BOM_Refund_Data_Articles_Helper() )->get_tax_rate( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 12, ( new BOM_Refund_Data_Articles_Helper() )->get_tax_rate( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 6, ( new BOM_Refund_Data_Articles_Helper() )->get_tax_rate( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '0' );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 0, ( new BOM_Refund_Data_Articles_Helper() )->get_tax_rate( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 25, ( new BOM_Refund_Data_Articles_Helper() )->get_tax_rate( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 12, ( new BOM_Refund_Data_Articles_Helper() )->get_tax_rate( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 6, ( new BOM_Refund_Data_Articles_Helper() )->get_tax_rate( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '0', false );
		$this->create_refund_order( $this->order );
		foreach ( $this->refund_order->get_items() as $item ) {
			$this->assertEquals( 0, ( new BOM_Refund_Data_Articles_Helper() )->get_tax_rate( $item ) );
		}
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Creates data for tests.
	 *
	 * @return void
	 */
	public function create() {
		update_option( 'woocommerce_calc_taxes', 'yes' );
		update_option( 'woocommerce_prices_include_tax', 'yes' );

		// Create tax rates.
		$this->tax_rate_ids[] = $this->create_tax_rate( '25' );
		$this->tax_rate_ids[] = $this->create_tax_rate( '12' );
		$this->tax_rate_ids[] = $this->create_tax_rate( '6' );
		$this->product        = ( new Krokedil_Simple_Product() )->create();
	}

	/**
	 * Updates data for tests.
	 *
	 * @return void
	 */
	public function update() {
		return;
	}

	/**
	 * Gets data for tests.
	 *
	 * @return void
	 */
	public function view() {
		return;
	}


	/**
	 * Resets needed data for tests.
	 *
	 * @return void
	 */
	public function delete() {
		global $wpdb;
		$wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'woocommerce_tax_rates' );// phpcs:ignore
		$wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'wc_tax_rate_classes' );// phpcs:ignore
		$wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'woocommerce_order_items' );// phpcs:ignore
		$wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'woocommerce_order_itemmeta' );// phpcs:ignore
		$this->order        = null;
		$this->refund_order = null;
		$this->product      = null;
		$this->tax_rate_ids = array();
	}

	/**
	 * Create order.
	 *
	 * @param string  $tax_rate tax rate.
	 * @param boolean $inc_tax inclusive tax.
	 * @return void
	 */
	public function create_order( $tax_rate, $inc_tax = true ) {
		$this->product->set_tax_class( $tax_rate . 'percent' );
		$this->product->save();
		if ( $inc_tax ) {
			update_option( 'woocommerce_prices_include_tax', 'yes' );
		} else {
			update_option( 'woocommerce_prices_include_tax', 'no' );
		}

		$order = wc_create_order();
		$order->add_product( $this->product );
		$order->calculate_totals();
		$order->save();
		$this->order = $order;
	}

	/**
	 * Create refund order.
	 *
	 * @param WC_Order $order The WooCommerce order.
	 * @return WC_Order_Refund $refund_order The WooCommerce refund order.
	 */
	public function create_refund_order( $order ) {
		$products     = $order->get_items( 'line_item' );
		$product_id   = array_keys( $products )[0];
		$refund_order = wc_create_refund(
			array(
				'amount'     => $order->get_total(),
				'order_id'   => $order->get_id(),
				'line_items' => array(
					$product_id => array(
						'qty'          => 1,
						'refund_total' => 50,
					),
				),

			)
		);
		$this->refund_order = $refund_order;
	}

	/**
	 * Helper to create tax rates and class.
	 *
	 * @param string $rate The tax rate.
	 * @return int
	 */
	public function create_tax_rate( $rate ) {
		// Create the tax class.

		$this->tax_classes[] = WC_Tax::create_tax_class( "${rate}percent", "${rate}percent" );

		// Set tax data.
		$tax_data = array(
			'tax_rate_country'  => '',
			'tax_rate_state'    => '',
			'tax_rate'          => $rate,
			'tax_rate_name'     => "Vat $rate",
			'tax_rate_priority' => 1,
			'tax_rate_compound' => 0,
			'tax_rate_shipping' => 1,
			'tax_rate_order'    => 1,
			'tax_rate_class'    => "${rate}percent",
		);
		return WC_Tax::_insert_tax_rate( $tax_data );
	}
}
