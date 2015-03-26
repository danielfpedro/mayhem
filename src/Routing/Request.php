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
		$this->inputJson = $slimRequest->getBody();
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

	public function json($field = null)
	{
		if ($field) {
			return isset($this->inputJson[$field]) ? $this->inputJson[$field] : null;
		}
		return $this->inputJson;
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