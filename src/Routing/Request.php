<?php

namespace Mayhem\Routing;

class Request 
{

	private $query;
	private $data;
	private $inputJson;

	private $params;

	private $action;
	private $controller;

	function __construct($slimRequest, $request)
	{
		/**
		 * Query
		 */
		$this->query = $slimRequest->get();
		/**
		 * Data
		 */
		if ($slimRequest->post()) {
			$this->data = $slimRequest->post();
		} elseif ($slimRequest->put()) {
			$this->data = $slimRequest->put();
		} elseif ($slimRequest->delete()) {
			$this->data = $slimRequest->delete();
		} else {
			$this->data = [];
		}
		/**
		 * Input
		 */
		$this->input = $slimRequest->getBody();
		/**
		 * Action
		 */
		$this->action = $request['action'];
		/**
		 * Controller
		 */
		$this->controller = $request['controller'];
	}

	public function query($field = null)
	{
		if ($field) {
			return isset($this->query[$field]) ? $this->query[$field] : null;
		}
		return $this->query;
	}

	public function data($field = null)
	{
		if ($field) {
			return isset($this->data[$field]) ? $this->data[$field] : null;
		}
		return $this->data;
	}

	public function input($type)
	{
		if ($type == 'json' && $this->input) {
			return json_decode($this->input, true);
		}
		return [];
	}
	public function action()
	{
		return $this->action;
	}
	public function controller()
	{
		return $this->controller;
	}
}