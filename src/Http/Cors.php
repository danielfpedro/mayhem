<?php

namespace Mayhem\Http;

/**
 * Set the CORS to the app can be accessed from external origin
 */
class Cors
{
	/**
	 * Set the CORS headers
	 */
	static public function getCORS()
	{

		if (isset($_SERVER['HTTP_ORIGIN'])) {
		    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		    header('Access-Control-Allow-Credentials: true');
		    header('Access-Control-Max-Age: 86400');    // cache for 1 day
		    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD");
		    header("Access-Control-Allow-Headers', 'Authorization, X-Authorization, Origin, Accept, Content-Type, X-Requested-With, X-HTTP-Method-Override");
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