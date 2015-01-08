<?php

namespace Mayhem\Model;

use Aura\SqlQuery\QueryFactory;
use Mayhem\Database\Datasource;
use PDO;
use Exception;

class Model
{

	public $connection = 'default';
	public $primaryKeyName = 'id';

	private $dbh;
	private $sth;

	public $queryFactory;

	public $errors;

	public function __construct()
	{
		$this->setPDO();
		$this->queryFactory = new QueryFactory('mysql');
	}

	private function setPDO()
	{
		$this->dbh = self::connect();
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

	public function executeQuery($query, $connection = null)
	{
		$sth = $this->dbh->prepare($query->__toString());
		$sth->execute($query->getBindValues());
		return $sth;
	}

	public function find()
	{
		$select = $this->queryFactory->newSelect();

		$select->from($this->tableName);
		return $select;
	}

	public function customInsert()
	{
		$insert = $this->queryFactory->newInsert();

		$insert->into($this->tableName);
		return $insert;
	}
	public function customUpdate()
	{
		$update = $this->queryFactory->newUpdate();

		$update->into($this->tableName);
		return $update;
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

	public function delete($id)
	{
		$delete = $this->queryFactory->newDelete();
		$delete->from($this->tableName)->where("{$this->primaryKeyName} = :id")->bindValue(':id', $id);
		$this->executeQuery($delete);
	}

	public function customDelete()
	{
		$delete = $this->queryFactory->customDelete();

		$delete->from($this->tableName);
		return $delete;
	}

	public function all($query)
	{
		return $this->executeQuery($query)->fetchAll(PDO::FETCH_OBJ);
	}

	public function one($query)
	{
		return $this->executeQuery($query->limit(1))->fetch(PDO::FETCH_OBJ);
	}	

}