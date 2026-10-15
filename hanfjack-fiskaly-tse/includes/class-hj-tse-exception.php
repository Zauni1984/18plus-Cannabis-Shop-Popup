<?php
defined( 'ABSPATH' ) || exit;

class HJ_TSE_Exception extends Exception {
	public string $api_code = '';
	public int $http_status = 0;

	public function __construct( string $message, string $api_code = '', int $http_status = 0 ) {
		parent::__construct( $message );
		$this->api_code    = $api_code;
		$this->http_status = $http_status;
	}
}
