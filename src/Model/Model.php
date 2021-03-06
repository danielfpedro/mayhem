<?php

namespace Mayhem\Model;

use Mayhem\Database\Datasource;
use Mayhem\Validation\ValitronAdapter;

use Aura\SqlQuery\QueryFactory;
use Valitron\Validator;

use PDO;
use Exception;

class Model
{

	public $connection = 'default';
	public $pkName = 'id';

	public $validations;

	private $dbh;
	private $sth;

	public $queryFactory;

	public $validationErrors;

	public $lastInsertId;

	public function __construct($connection = null)
	{
		$connection = (!is_null($connection)) ? $connection : $this->connection;
		$this->queryFactory = new QueryFactory(Datasource::getConnectionInfo($connection)['type']);
		$this->setDbh($connection);
	}

	private function setDbh($connection)
	{
		try {
			$conn = Datasource::getConnection($connection);
			$this->dbh = $conn;
		} catch(PDOException $e) {
			throw $e;
		}
	}

	public function executeQuery($query, $connection = null)
	{
		$sth = $this->dbh->prepare($query->__toString());
		$sth->execute($query->getBindValues());
		$this->rowsAffected = $sth->rowCount();
		return $sth;
	}

	public function find()
	{
		$select = $this->queryFactory->newSelect();

		$select->from("$this->tableName $this->tableAlias");
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

		$update->table($this->tableName);
		return $update;
	}

	public function save($data, $options = [])
	{
		return $this->saveOrUpdate('create', $data, $options);
	}

	public function update($data, $options = [])
	{
		return $this->saveOrUpdate('update', $data, $options);
	}

	public function saveOrUpdate($type, $data, $options = [])
	{
		if (!$data) {
			throw new Exception("Data to be saved can't be null", 1);
		}

		$useValidation = true;
		if (!method_exists($this, 'defaultRules')) {
			$useValidation = false;
		} elseif (isset($options['validate'])) {
			$useValidation = $options['validate'];
		}
		
		if ($useValidation) {
			$validator = new Validator($data);

			/**
			 * Se Adiciona as regras customizadas somente se existirem
			 */
			if (method_exists($this, 'customRules')) {
				foreach ($this->customRules() as $key => $rule) {
					$validator->addRule($key, $rule['rule'], $rule['message']);
				}
			}
			$validator = ValitronAdapter::AdaptRules($this->defaultRules(), $validator, $type);

			if (method_exists($this, 'beforeValidate') && $useValidation){
				$data = $this->beforeValidate($data, $type);
			}
			if (!$validator->validate()) {
				$this->validationErrors = $validator->errors();
				return false;
			}
		}

		if (isset($options['allowedFields'])) {
			$cleanData = [];
			foreach ($options['allowedFields'] as $key => $value) {
				$cleanData[$value] = $data[$value];
			}
		} else {
			$cleanData = $data;
		}


		if (method_exists($this, 'beforeSave')){
			$cleanData = $this->beforeSave($cleanData, $type);
		}

		if ($type == 'create') {
			$insert = $this->queryFactory->newInsert();
			$insert->into($this->tableName)->cols($cleanData);
			$query = $insert;
		} else {
			if (array_key_exists($this->pkName, $cleanData)) {
				$id = $cleanData[$this->pkName];
				unset($cleanData[$this->pkName]);

				$update = $this->queryFactory->newUpdate();
				$update
					->table($this->tableName)
					->cols($cleanData)
					->where("{$this->pkName} = :id")
					->bindValue($this->pkName, $id);

				$query = $update;
			} else {
				throw new Exception("Can't update, primary key needed", 1);
			}
		}
		
		$this->executeQuery($query);
		
		if ($type == 'create') {
			$this->lastInsertId = $this->dbh->lastInsertId('id');
		}

		if (method_exists($this, 'afterSave')){
			$this->afterSave($data, $type);
		}
		return true;
	}

	public function delete($id)
	{
		$delete = $this->queryFactory->newDelete();
		$delete->from($this->tableName)->where("{$this->pkName} = :id")->bindValue(':id', $id);
		$this->executeQuery($delete);

		//Always return true because controller expect boolean, if aboce instruction get error an exception will be raised
		return true;
	}

	public function customDelete()
	{
		$delete = $this->queryFactory->newDelete();

		$delete->from($this->tableName);
		return $delete;
	}

	public function all($query)
	{
		$values = $this->executeQuery($query)->fetchAll(PDO::FETCH_OBJ);
		return ($values) ? $values : null;
	}

	public function one($query)
	{
		$value = $this->executeQuery($query->limit(1))->fetch(PDO::FETCH_OBJ);;
		return ($value) ? $value : null;
	}	

}