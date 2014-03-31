<?php

interface HttpClient {
	
	//TODO: get them all: http://en.wikipedia.org/wiki/List_of_HTTP_header_fields	
	const HTTP_HEADER_ACCEPT = 'Accept: ';
	const HTTP_HEADER_ACCEPT_CHARSET = 'Accept-Charset: ';
	const HTTP_HEADER_ACCEPT_ENCODING = 'Accept-Encoding: ';
	const HTTP_HEADER_ACCEPT_LANGUAGE = 'Accept-Language: ';
	
	public function setUrl($url);
	
	public function setHttpHeader($headerKey, $headerValue);
	
	public function execute();
}
