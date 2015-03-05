<?php

namespace Mayhem\Database;

use PDO;
use Exception;

class Datasource
{


	public static function getConnection($connection)
	{
		$datasource = self::getDatasource();

		try {
			$data = $datasource[$connection];
		} catch (Exception $e) {
			throw new Exception("Connection '{$connection}' name not found on App\config\datasource", 1);	
		}

		$stringConnection = "mysql:host={$data['host']};dbname={$data['dbname']};charset={$data['charset']}";

		$conn = new PDO($stringConnection, $data['user'], $data['password']);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		return $conn;
	}

	public static function getConnectionInfo($connection)
	{
		$datasource = self::getDatasource();
		try {
			return $datasource[$connection];
		} catch (Exception $e) {
			throw new Exception("Connection '{$connection}' name not found on App\config\datasource", 1);	
		}
	}

	private static function getDatasource()
	{
		require(CONFIG . 'datasource.php');
		return $datasource;
	}
}