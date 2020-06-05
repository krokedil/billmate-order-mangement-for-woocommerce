<?php
/**
 * API Class file.
 *
 * @package Billmate_Checkout/Classes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACO_API class.
 *
 * Class that has functions for the Billmate communication.
 */
class BOM_API {

	/**
	 * Activate Billmate Payment.
	 *
	 * @param string $bco_order_number The Billmate order number.
	 * @return mixed
	 */
	public function request_activate_payment( $bco_order_number = '' ) {
		$request  = new BOM_Request_Activate_Payment();
		$response = $request->request( $bco_order_number );

		return $this->check_for_api_error( $response );
	}

	/**
	 * Cancel Billmate Payment.
	 *
	 * @param string $bco_order_number The Billmate order number.
	 * @return mixed
	 */
	public function request_cancel_payment( $bco_order_number = '' ) {
		$request  = new BOM_Request_Cancel_Payment();
		$response = $request->request( $bco_order_number );

		return $this->check_for_api_error( $response );
	}

	/**
	 * Checks for WP Errors and returns either the response as array or a false.
	 *
	 * @param array $response The response from the request.
	 * @return mixed
	 */
	private function check_for_api_error( $response ) {
		if ( is_wp_error( $response ) ) {
			bco_extract_error_message( $response );
			return false;
		}
		return $response;
	}
}
