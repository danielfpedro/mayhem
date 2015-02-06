<?php

namespace Mayhem\Routing;

class Response
{

	private $slimResponse;

	function __construct($slimResponse)
	{
		$this->slimResponse = $slimResponse;
	}

	public function success($body, $type = 'json')
	{
		$code = 200;
		return $this->responseRaw($code, $body, $type);
	}

	public function error($code, $body, $errorType = 'error', $type = 'json')
	{
		return $this->responseRaw($code, [
				'message' => $body,
				'type' => $errorType,
				'code' => $code
			], $type);
	}

	public function responseRaw($code, $body = null, $type = 'json')
	{
		$this->slimResponse->setStatus($code);
		switch ($type) {
			case 'json':
				$this->slimResponse->headers->set('Content-Type', 'application/json');
				$body = json_encode($body);
				break;
		}
		return $this->slimResponse->write($body);
	}

}