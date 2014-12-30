<?php

namespace Mayhem\Model;

use Aura\SqlQuery\QueryFactory;
use Mayhem\Database\Datasource;
use PDO;

class Model
{
	public $select;

	public function __construct()
	{
		$this->newQuery();
	}

	public function newQuery()
	{
		$query_factory = new QueryFactory('mysql');
		$query = $query_factory->newSelect();

		$query->from($this->tableName);
		$this->select = $query;
	}

	public static function connect($default = 'default')
	{
		$stringConnection = Datasource::getStringConnection('default');
		try {
			$conn = new PDO($stringConnection[0], $stringConnection[1], $stringConnection[2]);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return $conn;
		} catch(PDOException $e) {
			throw $e;
		}
	}

	private function executeQuery($query, $connection = null)
	{
		$pdo = self::connect();
		$sth = $pdo->prepare($query->__toString());
		$sth->execute($query->getBindValues());

		return $sth;
	}

	public function findAll($query = null)
	{
		return $this->executeQuery($query)->fetchAll(PDO::FETCH_OBJ);
	}

	public function findOne($query = null)
	{
		return $this->executeQuery($this->select)->fetch(PDO::FETCH_OBJ);
	}	

}