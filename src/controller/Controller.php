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
		$this->slim->response->setStatus($code);
		switch ($type) {
			case 'json':
				$this->slim->response->headers->set('Content-Type', 'application/json');
				$body = json_encode($body);
				break;
		}
		return $this->slim->response->write($body);
	}
}

?>