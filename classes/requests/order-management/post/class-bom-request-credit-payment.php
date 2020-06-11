<?php
/**
 * Credit payment request class
 *
 * @package Billmate_Order_Management/Classes/Post/Requests
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Credit payment request class
 */
class BOM_Request_Credit_Payment extends BOM_Request {

	/**
	 * Makes the request.
	 *
	 * @param string $bco_transaction_id Billmate transaction id.
	 * @param array  $refund_data Refund data.
	 * @return array
	 */
	public function request( $bco_transaction_id, $refund_data ) {
		$request_url  = $this->base_url;
		$request_args = apply_filters( 'bom_credit_payment_args', $this->get_request_args( $bco_transaction_id, $refund_data ) );

		$response = wp_remote_request( $request_url, $request_args );
		$code     = wp_remote_retrieve_response_code( $response );

		// Log the request.
		$log = BOM_Logger::format_log( $bco_transaction_id, 'POST', 'BOM credit payment', $request_args, json_decode( wp_remote_retrieve_body( $response ), true ), $code );
		BOM_Logger::log( $log );

		$formated_response = $this->process_response( $response, $request_args, $request_url );
		return $formated_response;
	}

	/**
	 * Gets the request body.
	 *
	 * @param string $bco_transaction_id Billmate transaction id.
	 * @param array  $refund_data Refund data.
	 * @return array
	 */
	public function get_body( $bco_transaction_id, $refund_data ) {
		$data         = $this->get_request_data( $bco_transaction_id, $refund_data );
		$request_body = array(
			'credentials' => array(
				'id'   => $this->id,
				'hash' => hash_hmac( 'sha512', wp_json_encode( $data ), $this->secret ),
				'test' => $this->test,
			),
			'data'        => $data,
			'function'    => 'creditPayment',
		);
		return $request_body;
	}

	/**
	 * Gets the request args for the API call.
	 *
	 * @param string $bco_transaction_id Billmate transaction id.
	 * @param array  $refund_data Refund data.
	 * @return array
	 */
	public function get_request_args( $bco_transaction_id, $refund_data ) {
		return array(
			'headers' => $this->get_headers(),
			'method'  => 'POST',
			'body'    => wp_json_encode( $this->get_body( $bco_transaction_id, $refund_data ) ),
		);
	}

	/**
	 * Get needed data for the request.
	 *
	 * @param string $bco_transaction_id Billmate transaction id.
	 * @param array  $refund_data Refund data.
	 * @return array
	 */
	public function get_request_data( $bco_transaction_id, $refund_data ) {
		$data = array(
			'PaymentData' => array(
				'number'     => $bco_transaction_id,
				'partcredit' => $refund_data['part_credit'],
			),
			'Articles'    => ( 'true' === $refund_data['part_credit'] ) ? $refund_data['articles_data'] : '', // Only need articles if refund is partial.
			'Cart'        => array(
				'Handling' => $refund_data['handling_data'],
				'Shipping' => $refund_data['shipping_data'],
				'Total'    => $refund_data['total_data'],
			),
		);
		return $data;
	}
}
