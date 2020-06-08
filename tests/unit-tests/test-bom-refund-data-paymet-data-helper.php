<?php // phpcs:ignore
/**
 *
 * Test_BOM_Refund_Data_Payment_Data_Helper class
 *
 * @package category
 */

/**
 * Test_BOM_Refund_Data_Payment_Data_Helper class
 */
class Test_BOM_Refund_Data_Payment_Data_Helper extends AKrokedil_Unit_Test_Case {
	/**
	 * Test BOM_Refund_Data_Payment_Data_Helper::get_refund_type
	 *
	 * @return void
	 */
	public function test_get_refund_type_partial() {
		$this->create_refund_order( $this->order );
		$this->assertEquals( 'true', BOM_Refund_Data_Payment_Data_Helper::get_refund_type( $this->order, $this->refund_order ) );
	}

	/**
	 * Test BOM_Refund_Data_Payment_Data_Helper::get_refund_type
	 *
	 * @return void
	 */
	public function test_get_refund_type_non_partial() {
		$this->create_refund_order( $this->order, false );
		$this->assertEquals( 'false', BOM_Refund_Data_Payment_Data_Helper::get_refund_type( $this->order, $this->refund_order ) );
	}

	/**
	 * Creates data for tests.
	 *
	 * @return void
	 */
	public function create() {
		$this->product = ( new Krokedil_Simple_Product() )->create();
		$order         = ( new Krokedil_Order() )->create();
		$order->add_product( $this->product );
		$order->calculate_totals();
		$order->save();
		$this->order = $order;
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
		wp_delete_post( $this->order->get_id() );
		$this->order = null;
	}

	/**
	 * Create refund order.
	 *
	 * @param WC_Order $order The WooCommerce order.
	 * @param bool     $partial partial refund or not.
	 * @return void
	 */
	public function create_refund_order( $order, $partial = true ) {
		$refund_order       = wc_create_refund(
			array(
				'amount'   => ( $partial ) ? 50 : 100,
				'order_id' => $order->get_id(),
			)
		);
		$this->refund_order = $refund_order;
	}
}
