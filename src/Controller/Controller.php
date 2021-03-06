<?php

namespace Mayhem\Controller;

use Mayhem\Routing\Request;
use Mayhem\Routing\Response;

/**
 * Main controller of the framework
 */
class Controller
{
	
	public $slim;

	public $request;
	public $config;

	public $Response;

	function __construct($slim, $request)
	{
		$this->slim = $slim;
		$this->request = $request;

		$this->Request = new Request($this->slim->request, $request);
		$this->Response = new Response($this->slim->response);
	}


}

?>