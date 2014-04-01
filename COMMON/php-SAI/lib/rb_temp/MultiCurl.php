<?php

class MultiCurl implements HttpClient {
	
	protected $multiHandle;
	protected $simpleCurlCollection = array();
	
	public function __construct ($url) {
		$this->multiHandle = curl_multi_init();
		
		// add primary url object, so this class could also work as an HttpClient
		$curl = new SimpleCurl($url);
		$this->addSimpleCurl($curl);
	}
	
	/**
	 * set url on primary SimpleCurl object
	 */
	public function setUrl($url) {
		$this->simpleCurlCollection[0]->setUrl($url);
	}
	
	/**
	 * set http header on primary SimpleCurl object
	 */
	public function setHttpHeader($headerKey, $headerValue) {
		$this->simpleCurlCollection[0]->setHttpHeader($headerKey, $headerValue);
	}
	
	public function addSimpleCurl(SimpleCurl $curl) {
		$curl->setCurlOption(CURLOPT_RETURNTRANSFER, true);
		$this->simpleCurlCollection[] = $url;
		curl_multi_add_handle($this->multiHandle, $url->_getHandle());
	}
	
	/**
	 * execute all handles
	 */
	public function execute() {
		$this->_execute();
		
		foreach($this->simpleCurlCollection AS $simpleCurl) {
			$simpleCurl->_setResponse(curl_multi_getcontent($simpleCurl->_getHandle()));
			//TODO: $simpleCurl->_setError(...); //is this working like that?
		}
	}
	
	private function _execute() {
		$active = null;
		do {
			$mrc = curl_multi_exec($this->multiHandle, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);

		while ($active && $mrc == CURLM_OK) {
			if (curl_multi_select($this->multiHandle) != -1) {
				do {
					$mrc = curl_multi_exec($this->multiHandle, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
	}
	
	public function close() {
		foreach($this->simpleCurlCollection AS $simpleCurl) {
			curl_multi_remove_handle($this->multiHandle, $simpleCurl->_getHandle());
		}
		curl_multi_close($multiHandle);
	}
	
	public function __destruct() {
		$this->close();
	}
}
