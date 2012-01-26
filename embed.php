<?php
/**
	Wordpress Oembed API 
	====================

	A Wordpress Library for getting oembed data in a OOP way from an oembed endpoint.
	This is NOT a plugin to deal with oembed , it is a library to be used within plugins

	Currently only supports API's that speack JSON 

	@version 0.0.1
	@author DotMH (http://dotmh.com)
	@license MIT

	Copyright (c) <2012> <DotMH>

	Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE

**/

/**
	@todo support xml responses

	@param mixed $params - Allows loading of the oembed request parameters (for list , plus requirments visit http://oembed.com/)
	@param bool $autoload - Whether to attempt to get the data stright away or not , this only works if all the required params where sent using $params

	@var string $endpoint (protected) This holds the oembed API endpoint to get the data from
	@var array $data (protected) holds all the data that the API response with
	@var string $url (protected) holds the url you wish to embed 
	@var string $height (protected) holds the height that you want to embed the resource at
	@var string $width (protected) hold the width that you want to embed the resource at
	@var array $other (protected) holds other parameters to pass with the request to the endpoint that arent specified by the oembed spec
	@var array $required (protected) holds a list of required parameters that must be passed bu the oembed spec

	@return null
**/

class dotmh_embed
{
	protected $endpoint;

	protected $data;

	protected $url;

	protected $height;

	protected $width;

	protected $other = array();

	private $required = array('url');

	public function __construct( $params = null , $autoload = FALSE )
	{
		if ( !is_null($params) ) {
			foreach( $params as $param => $value ) {
				$this->__set($param , $value);
			}
		}

		if ( $autoload ) {
			$this->load();
		}
	}

	/**
		Load the Oembed data from the endpoint using the request vars

		It will throw an exception on error , so that you can manage errors how you see fit the error will ether include 
		$code int The http error code i.e. 404.
		$message string the http message i.e. page not found

		or the wordpress error information

		@param null
		@return null
	**/
	public function load() 
	{
		$response = wp_remote_get($this->build_url());

		if ( is_wp_error($response) ) {
			throw new Exception($response->get_error_message() , $response->get_error_code());
		}
		else {
			if ( $response['response']['code'] != 200 ) {
				/*
					 Stop : The response from the server was invalid
					 --
					 Throw an exception so that the libraries user can handle it as they need
					 
					 Code 	:	is set to the http response code
					 Message: 	is the http message
				*/
				throw new Exception($response['response']['message'] , $response['response']['code']);
			}

			// All Good we have something from the server

			$data = json_decode( $response['body'] , true);

			// Check something came back that we can use after we smashed it through the decoder
			if ( is_null($data) || $data === FALSE || count($data) < 1 ) {
				throw new Exception("This is not the response you are looking for", 2);
			}

			// All is well , the world is a better place 
			$this->data = $data;
		}

	}
	/**
		Builds the request url , currently the format is set to json , this is due to the way the response is processed. 

		@todo support the format var

		@params null
		@return string the complete request
	**/
	public function build_url() {

		$this->is_valid();

		$parts = array();
		array_push($parts, $this->httpify($this->endpoint));
		array_push($parts, '?');
		array_push($parts, 'url='.urlencode($this->httpify($this->url)).'&');

		if ( !empty($this->maxheight) ) array_push($parts, 'maxheight='.$this->maxheight.'&');
		if ( !empty($this->maxwidth) )array_push($parts, 'maxwidth='.$this->maxwidth.'&');

		array_push($parts, $this->build_other_params());
		array_push($parts, 'format=json');

		return implode('', $parts);
	}

	/**
		Check that the request will be valid and all required fields are complete 

		Throws exception if the request is invalid.
	**/
	public function is_valid() {
		foreach ( $this->required as $param ) {
			if ( empty($this->{$param} ) ) {
				throw new Exception("{$param} is a required field by the oembed specification here http://www.oembed.com/");
			}
		}
	}

	/**
		Outputs the response as an assoc array

		@return array The response data
	**/
	public function __toArray() {
		return $this->data;
	}

	/**
		Add the protocol on the url if its not present , this also checks to see if the request was made using ssl and if so adds https rather than http

		@returns string the url + the protocol
	**/
	protected function httpify( $url ) {

		if ( ! preg_match('/^http|https/', $url) ) {
			$prefix = empty($_SERVER['HTTPS'] ) ? 'http://' : 'https://';
			return $prefix.$url;
		}
		return $url;
	}

	/**
		Takes any other parameters you want to pass in the request and converts them to a query string 

		@return string the completed query string.
	**/
	protected function build_other_params() {

		$string = '';

		if ( count($this->other) > 0 ) {
			$loop_counter = 0;
			foreach ($this->other as $key => $value) {
				$string .= ($loop_counter < 0 ? '&' : '').$key.'='.$value;
				$loop_counter++;
			}
		}

		return $string;
	}

	/**
		Magic get Function see 
		@link http://www.php.net/manual/en/language.oop5.overloading.php#object.get
	**/
	public function __get($name) {
		
		$name = strtolower(preg_replace('/([A-Z])/', ':$1', $name));

		if ( array_key_exists($name, $this->data ) ) {
			return $this->data[$name];
		}

		return FALSE;
	}

	/**
		Magic set Function see 
		@link http://www.php.net/manual/en/language.oop5.overloading.php#object.set
	**/
	public function __set($name , $value ) {
		if ( property_exists($this, $name) ) {
			$this->{$name} = $value;
		}
		else {
			$other[$name] = $value;
		}
	}
}