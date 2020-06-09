<?php // phpcs:ignore
/**
 *
 * Test_BOM_Refund_Data_Cart_Helper class
 *
 * @package category
 */

/**
 * Test_BOM_Refund_Data_Cart_Helper class
 */
class Test_BOM_Refund_Data_Cart_Helper extends AKrokedil_Unit_Test_Case {

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

	// Handling.
	/**
	 * Test BOM_Refund_Data_Cart_Helper::get_handling_without_tax
	 *
	 * @return void
	 */
	public function test_get_handling_without_tax() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 8000, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 8929, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 9434, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Cart_Helper::get_handling_tax_rate
	 *
	 * @return void
	 */
	public function test_get_handling_tax_rate() {

		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 25, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 12, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 6, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '0' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 0, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 25, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 12, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 6, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '0', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 0, ( new BOM_Refund_Data_Cart_Helper() )->get_handling_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	// Shipping.
	/**
	 * Test BOM_Refund_Data_Cart_Helper::get_shipping_without_tax
	 *
	 * @return void
	 */
	public function test_get_shipping_without_tax() {
		$this->create_order( '25' );
		$this->create_shipping( 25 );
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 4000, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_shipping( 12 );
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 4000, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_shipping( 6 );
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 4000, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_shipping();
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 4000, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_shipping();
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 4000, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_shipping();
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 4000, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Cart_Helper::get_shipping_tax_rate
	 *
	 * @return void
	 */
	public function test_get_shipping_tax_rate() {
		$this->create_order( '25' );
		$this->create_shipping( 25 );
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 25, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_shipping( 12 );
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 12, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_shipping( 6 );
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 6, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_shipping();
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 25, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_shipping();
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 12, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_shipping();
		$this->create_refund_order( $this->order, true );
		$this->assertEquals( 6, ( new BOM_Refund_Data_Cart_Helper() )->get_shipping_tax_rate( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

	}

	// Total.
	/**
	 * Test BOM_Refund_Data_Cart_Helper::get_total_without_tax
	 *
	 * @return void
	 */
	public function test_get_total_without_tax() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 8000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 8929, ( new BOM_Refund_Data_Cart_Helper() )->get_total_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 9434, ( new BOM_Refund_Data_Cart_Helper() )->get_total_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_without_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Cart_Helper::get_total_tax
	 *
	 * @return void
	 */
	public function test_get_total_tax() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 2000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 1071, ( new BOM_Refund_Data_Cart_Helper() )->get_total_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 566, ( new BOM_Refund_Data_Cart_Helper() )->get_total_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '0' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 0, ( new BOM_Refund_Data_Cart_Helper() )->get_total_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 2500, ( new BOM_Refund_Data_Cart_Helper() )->get_total_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 1200, ( new BOM_Refund_Data_Cart_Helper() )->get_total_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 600, ( new BOM_Refund_Data_Cart_Helper() )->get_total_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '0', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 0, ( new BOM_Refund_Data_Cart_Helper() )->get_total_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;
	}

	/**
	 * Test BOM_Refund_Data_Cart_Helper::get_total_with_tax
	 *
	 * @return void
	 */
	public function test_get_total_with_tax() {
		$this->create_order( '25' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_with_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_with_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_with_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '0' );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_with_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '25', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 12500, ( new BOM_Refund_Data_Cart_Helper() )->get_total_with_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '12', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 11200, ( new BOM_Refund_Data_Cart_Helper() )->get_total_with_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '6', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10600, ( new BOM_Refund_Data_Cart_Helper() )->get_total_with_tax( $this->refund_order ) );
		wp_delete_post( $this->order->get_id() );
		wp_delete_post( $this->refund_order->get_id() );
		$this->order        = null;
		$this->refund_order = null;

		$this->create_order( '0', false );
		$this->create_refund_order( $this->order );
		$this->assertEquals( 10000, ( new BOM_Refund_Data_Cart_Helper() )->get_total_with_tax( $this->refund_order ) );
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
	 * @param bool     $shipping_refund If the refund is shipping.
	 * @return void
	 */
	public function create_refund_order( $order, $shipping_refund = false ) {
		$refund_order = array();
		if ( $shipping_refund ) {
			$shipping    = $order->get_items( 'shipping' );
			$shipping_id = array_keys( $shipping )[0];

			$refund_order = wc_create_refund(
				array(
					'amount'     => $order->get_total(),
					'order_id'   => $order->get_id(),
					'line_items' => array(
						$shipping_id => array(
							'qty'          => 1,
							'refund_total' => $shipping[ $shipping_id ]->get_total(),
							'refund_tax'   => $shipping[ $shipping_id ]->get_total_tax(),
						),
					),

				)
			);
		} else {
			$products   = $order->get_items( 'line_item' );
			$product_id = array_keys( $products )[0];

			$refund_order = wc_create_refund(
				array(
					'amount'     => $order->get_total(),
					'order_id'   => $order->get_id(),
					'line_items' => array(
						$product_id => array(
							'qty'          => 1,
							'refund_total' => $products[ $product_id ]->get_total(),
							'refund_tax'   => $products[ $product_id ]->get_total_tax(),
						),
					),

				)
			);
		}

		$this->refund_order = $refund_order;
	}


	/**
	 * Create shipping.
	 *
	 * @param int $tax total tax amount.
	 * @return void
	 */
	public function create_shipping( $tax = 0 ) {
		$data                = array(
			'total' => 40,
		);
		$order_item_shipping = ( new Krokedil_Order_Item_Shipping( $data ) )->get_order_items_shipping();
		$order_item_shipping->set_props( array( 'shipping_tax' => $tax ) );

		$this->order->add_item( $order_item_shipping );
		$this->order->calculate_totals();
		$this->order->save();
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
