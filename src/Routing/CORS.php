<?php

namespace Mayhem\Routing;

/**
 * Set the CORS to the app can be accessed from external origin
 */
class CORS
{
	/**
	 * Set the CORS headers
	 */
	static public function getCORS()
	{

		if (isset($_SERVER['HTTP_ORIGIN'])) {
		    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		    header('Access-Control-Allow-Credentials: true');
		    header('Access-Control-Max-Age: 86400');
		    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD");
		}
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])){
		        header("Access-Control-Allow-Methods: GET, PUT, DELETE, OPTIONS");
		    }
		    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])){
		        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		    }
		    exit(0);
		}

	}
}

?>