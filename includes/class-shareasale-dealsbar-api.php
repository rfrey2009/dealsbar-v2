<?php
class ShareASale_Dealsbar_API {

	private $api_version = 2.1;
	private $api_token;
	private $api_secret;
	private $affiliate_id;
	private $headers;
	private $timestamp;
	private $action;
	private $query;
	private $last_query;
	private $error_msg;
	private $response;

	public function __construct( $api_token, $api_secret, $affiliate_id ) {
		$this->api_token    = $api_token;
		$this->api_secret   = $api_secret;
		$this->affiliate_id = $affiliate_id;		
		return $this;
	}

	private function build_url( $params = array() ){
		$protocol    = 'https://';
		$hostname    = 'api.shareasale.com/';
		$handler     = 'x.cfm';
		$params      = array_merge( [
									'action'       =>$this->action,
									'affiliate_id' => $this->affiliate_id,
									'token'        =>$this->api_token,
									'version'      =>$this->api_version
									],
								$params );

		$query_string = '?' . http_build_query( $params );
		$url = $protocol . $hostname . $handler . $query_string;		
		return $url;
	}

	private function authenticate(){
		//can't authenticate without an action verb set already...
		if( !$this->action ) 
			return FALSE;

		$this->timestamp  = gmdate( DATE_RFC1123 );
		//build auth headers
		$sig      = $this->api_token . ':' . $this->timestamp . ':' . $this->action . ':' . $this->api_secret;
		$sig_hash = hash( 'sha256', $sig );
		$this->headers = array( "x-ShareASale-Date: {$this->timestamp}", "x-ShareASale-Authentication: {$sig_hash}" );
	}		

	public function coupon_deals( $params = array() ){
		$this->action = 'couponDeals';
		$this->query  = $this->build_url( $params );		
		return $this;
	}

	public function token_count(){
		$this->action = 'api_tokencount';
		$this->query  = $this->build_url();		
		return $this;
	}

	public function exec(){
		//build authentication headers before making API request
		if( !$this->authenticate() ){
			$this->error_msg = 'Could not authenticate. No API action value.';
			return FALSE;
		}

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $this->query );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->headers );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE ); //set to TRUE in production
		//make the API request
		$response = curl_exec( $ch );
		curl_close( $ch ); 
		//set last_query property and clear out current query property
		$this->last_query = $this->query;
		$this->query      = '';
		if ( stripos( $response, "Error" ) ) {
			// error occurred... store it and return FALSE
			$this->error_msg = trim( $response );
			return FALSE;
		}
		//else return json object off the response
		$this->response = trim( $response );
		return TRUE;
	}

	//getters
	public function get_affiliate_id(){
		return $this->affiliate_id;
	}

	public function get_last_query(){
		return $this->last_query;
	}

	public function get_response(){
		return $this->response;
	}

	public function get_error_msg(){
		return $this->error_msg;
	}

	//setters
}
?>