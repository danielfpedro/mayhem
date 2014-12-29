<?php

namespace Mayhem\Database;

class Datasource
{
	public static function getStringConnection($connection = 'default')
	{
		require(CONFIG . 'datasource.php');
		
		$data = $datasource[$connection];

		return ["mysql:host={$data['host']};dbname={$data['dbname']};charset={$data['charset']}", $data['user'], $data['password']];
	
	}
}