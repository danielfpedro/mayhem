<?php

namespace Mayhem\Controller;

/**
 * Main controller of the framework
 */
class Controller
{
	
	public $slim;

	public $request;
	public $config;

	function __construct()
	{
	}

	public function response($code, $body = null, $type = 'json')
	{
		switch ($type) {
			case 'json':
				$body = json_encode($body);
				break;
		}
		return $this->slim->halt($code, $body);
	}
}

?>