<?php

class SimpleCurl implements HttpClient { /* it would also be possible to implement a FtpClient or any other interface, but currently YAGNI */
	
	protected $handle;
	protected $httpHeader = array();
	
	protected $response;
	protected $error;
	
	public function __construct($url = null) {
		if ($url != null) {
			$this->validateUrl($url);
			$this->handle = curl_init($url);
		} else {
			$this->handle = curl_init();
		}
	}
	
	// -- implemented functions --
	
	public function setUrl($url) {
		$this->validateUrl($url);
		$this->setCurlOption(CURLOPT_URL, $url);
	}
	
	public function execute() {
		$this->response = curl_exec($this->handle);
		return $this->response;
	}
	
	public function setHttpHeader($key, $value) {
		$this->httpHeader[$key] = $value;
		$this->setCurlOption(CURL_HTTPHEADER, $this->httpHeader);
	}
	
	// -- curl specific functions -- 
	
	public function setCurlOption($option, $value) {
		curl_setopt($this->handle, $option, $value);
	}
	
	public function setCurlOptionArray($optionValueArray) {
		curl_setopt_array($this->handle, $optionValueArray);
	}
	
	public function getError() {
		if ($this->error === null) {
			// TODO: validate, whether error could be a object with msg and nr; maybe even something specific like "CurlError"
			$this->_setErrors(curl_error($this->handle));
		}
		return $this->error;
	}
	
	public function close()
	{
		curl_close($this->handle);
	}
	
	// -- magic functions --
	
	public function __clone() {
		$this->handle = curl_copy_handle($this->handle);
	}
	
	public function __destruct() {
		$this->close();
	}
	
	// -- package functions (in java they would be protected) --
	// maybe I should make them protected and let the MultiCurl extend the SimpleCurl
	
	/**
	 * @return the internal curl handle
	 */
	public function _getHandle() {
		return $this->handle;
	}
	
	public function _setResponse($response) {
		$this->response = $response;
	}
	
	public function _setErrors($error) {
		$this->error = $error;
	}
	
	// -- internal functions --
	
	/**
	 * @throws Exception if invalid
	 */
	protected function validateUrl($url)
	{
		//TODO or nice to have
	}
}
