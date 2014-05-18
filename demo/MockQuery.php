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
		$this->resultCount = mt_rand(0, 20) * 5;
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
		$r = mt_rand(1, 100);
		switch ($this->type)
		{
			case 'mysql':
			case 'postgres':
				return \Nette\Utils\Html::el('')->setHtml("<pre><b>SELECT</b> * <b>\nFROM</b> foo\n<b>WHERE</b> id IN ($r)</pre>");
			case 'elastic':
				$query = json_decode(file_get_contents(__DIR__ . '/elastic.json'), TRUE);
				return \Nette\Utils\Html::el('')->setHtml(\Tracy\Dumper::toHtml($query, [\Tracy\Dumper::COLLAPSE_COUNT => 1, \Tracy\Dumper::DEPTH => 10]));
			case 'neo4j':
				return \Nette\Utils\Html::el('')->setHtml("<pre><b>MATCH</b> (v:Video)<-[:CONTAINS]-(t:Tag)\n<b>WHERE</b> v.eid = $r\n<b>RETURN</b> t</pre>");
			case 'redis':
			default:
				return \Nette\Utils\Html::el('')->setHtml("<pre><b>HGETALL</b> user:$r</pre>");
		}
	}

	public function getElapsedTime()
	{
		return $this->elapsedTime;
	}

}
