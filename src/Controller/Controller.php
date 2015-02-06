<?php

namespace Mayhem\Controller;

use Mayhem\Routing\Response;

/**
 * Main controller of the framework
 */
class Controller
{
	
	public $slim;

	public $request;
	public $config;

	public $header_body_json;

	public $Response;

	function __construct($slim)
	{
		$this->slim = $slim;
		$this->Response = new Response($this->slim->response);

		$this->beforeFilter();
	}


}

?>