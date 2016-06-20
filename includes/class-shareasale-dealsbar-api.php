<?php namespace ShareASale;
//ShareASale API Adapter by ryan@ryanfrey.com
//i.e. $adapter = (new API($apiToken, $apiSecret, $merchantID))->voidTrans($orderNumber, $date)->exec();
class API{

	private $apiVersion = 2.6;
	private $apiToken;
	private $apiSecret;
	private $merchantID;
	private $headers;
	private $timestamp;
	private $action;
	private $query;
	private $lastQuery;
	private $errorMsg;
	private $response;

	public function __construct($apiToken, $apiSecret, $merchantID) {
		$this->apiToken   = $apiToken;
		$this->apiSecret  = $apiSecret;
		$this->merchantID = $merchantID;
		$this->timestamp  = gmdate(DATE_RFC1123); //maybe move this to authenticate() so it's set at request time and not instantiation?

		return $this;
	}

	private function buildURL($params = array()){
		$protocol    = 'https://';
		$hostname    = 'api.shareasale.com/';
		$handler     = 'w.cfm';
		$params      = array_merge(['action'=>$this->action, 'merchantID'=>$this->merchantID, 'token'=>$this->apiToken, 'version'=>$this->apiVersion], $params);
		$queryString = '?' . http_build_query($params);
		$url = $protocol . $hostname . $handler . $queryString;		
		return $url;
	}

	private function authenticate(){
		//can't authenticate without an action verb set already...
		if(!$this->action) 
			return FALSE;
		//build auth headers
		$sig = $this->apiToken.':'.$this->timestamp.':'.$this->action.':'.$this->apiSecret;
		$sigHash = hash('sha256', $sig);
		$this->headers = array("x-ShareASale-Date: {$this->timestamp}","x-ShareASale-Authentication: {$sigHash}");
	}

	public function editTrans($orderNumber, $date, $newAmount) {
		$this->action = 'edit';
		$params       = ['ordernumber'=>$orderNumber, 'date'=>$date, 'newamount'=>$newAmount];
		$this->query  = $this->buildURL($params);		
		return $this;
	}

	public function voidTrans($orderNumber, $date, $reason = '') {
		$this->action = 'void';
		$params       = ['ordernumber'=>$orderNumber, 'date'=>$date, 'reason'=>$reason];
		$this->query  = $this->buildURL($params);		
		return $this;
	}

	public function tokenCount(){
		$this->action = 'apitokencount';
		$params       = []; //no add'l params for an apitokencount request
		$this->query  = $this->buildURL();		
		return $this;
	}

	public function exec(){
		//build authentication headers before making API request
		$this->authenticate();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->query);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //set to TRUE in production
		//make the API request
		$response = curl_exec($ch);
		curl_close($ch); 
		//set lastQuery property and clear out current query property
		$this->lastQuery = $this->query;
		$this->query     = '';
		if (stripos($response, "Error")) {
			// error occurred... store it and return FALSE
			$this->errorMsg = trim($response);
			return FALSE;
		}
		//else return json object off the response
		$this->response = trim($response);
		return TRUE;
	}

	//getters
	public function getMerchantId(){
		return $this->merchantID;
	}

	//getters and setters
	public function getLastQuery(){
		return $this->lastQuery;
	}

	//getters and setters
	public function getResponse(){
		return $this->response;
	}

	public function getErrorMsg(){
		return $this->errorMsg;
	}
	//setters

}

?>