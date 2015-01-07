<?php

namespace Mayhem\Model;

use Aura\SqlQuery\QueryFactory;
use Mayhem\Database\Datasource;
use PDO;
use Exception;

class Model
{

	public $primaryKeyName = 'id';
	public $select;
	public $insert;

	private $pdo;
	private $sth;

	public $queryFactory;

	public $errors;

	public function __construct()
	{
		$this->setPDO();

		$this->queryFactory = new QueryFactory('mysql');
		$this->newQuery();
	}

	public function newQuery()
	{
		$query = $this->queryFactory->newSelect();

		$query->from($this->tableName);
		$this->select = $query;
	}
	public function newInsert()
	{
		$insert = $this->queryFactory->newInsert();

		$insert->into($this->tableName);
		$this->insert = $insert;
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

	private function setPDO()
	{
		$this->pdo = self::connect();
	}

	public function save($data)
	{
		$insert = $this->queryFactory->newInsert();
		$insert->into($this->tableName)->cols($data);
		$this->executeQuery($insert);
	}

	public function update($data)
	{
		if (array_key_exists($this->primaryKeyName, $data)) {
			$id = $data[$this->primaryKeyName];
			unset($data[$this->primaryKeyName]);
			$update = $this->queryFactory->newUpdate();
			$update
				->table($this->tableName)
				->cols($data)
				->where("{$this->primaryKeyName} = :id")
				->bindValue($this->primaryKeyName, $id);
			return $this->executeQuery($update);
		} else {
			throw new Exception("Can't update, primary key needed", 1);
		}

		return true;
	}

	public function executeQuery($query, $connection = null)
	{
		$sth = $this->pdo->prepare($query->__toString());
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