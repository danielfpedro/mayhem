<?php

namespace Mayhem\Routing;

use Slim\Slim;

/**
 * Dispatcher
 */
class Dispatcher
{	
	/**
	 * Dispatch the rount geted from Slim framework
	 * @param  array $config App config
	 * @return method         The method called
	 */
	public static function dispatch($config)
	{
		$slim = new Slim(['debug'=> $config['debug']]);

		$slim->map('/:controller(/:action)(/:params+)', function($controller, $action = null, $params = []) use ($slim, $config){
			
			$controller_class_name = Dispatcher::setControllerClassName($controller);

			if (class_exists($controller_class_name)) {
				$obj = new $controller_class_name();
				$obj->config = $config;
				$obj->request = $slim->request;
				$obj->header_body_json = json_decode($obj->request->getBody(), true);
				$obj->slim = $slim;

				//Remove the _ from action name, users can't acces methods the starts with _ char
				$action = ($action && $action[0] == '_') ? ltrim($action, '_') : $action;

				$result = Dispatcher::resolveAction($action, $slim->request->getMethod());
				$action = $result['action'];
				if ($result['params']) {
					$firstParam[] = $result['params'];
					$params = array_merge($firstParam, $params);
				}

				if (method_exists($obj, $action)) {
					if ($config['responseType'] == 'JSON') {
						call_user_func_array([$obj, $action], $params);
						return true;
					}
				} else {
					$notFoundMsg = 'Action not Found';
				}
			} else {
				$notFoundMsg = 'Controller not Found';
			}

			$slim->halt(404, ($config['debug']) ? $notFoundMsg : 'Not Found');
		})
		->via('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS');

		$slim->run();
	}

/**
 * Resolve the action name based on RESTful logic
 * @param  string $action the action name to be resolved
 * @param  string $method the method to say how the action will be resolved
 * @return string
 */
	public static function resolveAction($action, $method)
	{
		$params = null;
		if (!$action) {
			switch ($method) {
				case 'GET':
					$action = "index";
					break;
				case 'POST':
					$action = "add";
					break;
			}
		} else {
			if(is_numeric($action)){
				$params = $action;
				switch ($method) {
					case 'GET':
						$action = "view";
						break;
					case 'PUT':
						$action = "edit";
						break;
					case 'DELETE':
						$action = "delete";
						break;
				}
			}
		}

		return ['action' => $action, 'params' => $params];
	}

/**
 * Se the controller class name from a controller name
 * @param  string $controller Controller name
 * @return string             Controller class name
 */
	public static function setControllerClassName($controller)
	{
		return'App\Controller\\' . ucfirst($controller) . 'Controller';
	}
}

?>