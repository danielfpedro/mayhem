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

	public function response($code, $status, $body, $type = 'json')
	{
		return $this->responseRaw($code, ['status' => $status, 'message' => $body], $type);
	}

	public function responseRaw($code, $body = null, $type = 'json')
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