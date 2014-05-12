<?php

class MockQuery extends \Nette\Object implements \Tracy\QueryPanel\IQuery
{

	private $resultCount;
	private $elapsedTime;
	private $type;

	public function __construct()
	{
		$storage = ['mysql', 'postgres', 'elastic', 'neo4j', 'redis'];
		$this->type = $storage[array_rand($storage)];
		$this->resultCount = mt_rand(0, 100);
		$this->elapsedTime = mt_rand(100, 6000) / 100;
	}

	public function getResultCount()
	{
		return $this->resultCount;
	}

	public function getResult()
	{

	}

	public function getStorageType()
	{
		return $this->type;
	}

	public function getDatabaseName()
	{
		if ($this->type === 'mysql')
		{
			$db = ['db_foo', 'db_bar'];
			return $db[array_rand($db)];
		}
		else if ($this->type === 'elastic')
		{
			return 'appName';
		}

		return NULL;
	}

	public function getQuery()
	{
		switch ($this->type)
		{
			case 'mysql':
			case 'postgres':
				return 'SELECT * FROM foo';
			case 'elastic':
				return '{match: ...}';
			case 'neo4j':
				return 'MATCH sth';
			case 'redis':
			default:
				return '...';
		}
	}

	public function getElapsedTime()
	{
		return $this->elapsedTime;
	}

}
