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

	public $Response;

	function __construct($slim, $request)
	{
		$this->slim = $slim;
		$this->request - $request;

		$this->Response = new Response($this->slim->response);

		if (method_exists($this, 'beforeFilter')) {
			$this->beforeFilter();
		}
	}


}

?>