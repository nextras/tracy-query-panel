<?php

class MockQuery extends \Nette\Object implements \Tracy\QueryPanel\IQuery
{

	private $resultCount;
	private $elapsedTime;
	private $type;

	public function __construct()
	{
		$storage = array('mysql', 'postgres', 'elastic', 'neo4j', 'redis');
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
		switch ($this->type)
		{
			case 'mysql':
				$result = array(
					array('id' => 1, 'foo' => 'bar', 'baz' => 'what'),
					array('id' => 2, 'foo' => 'rab', 'baz' => 'tahw'),
				);
				break;
			case 'elastic':
				$result = json_decode(file_get_contents(__DIR__ . '/response.elastic'), TRUE);
				break;
			default:
				return NULL;
		}

		$html = \Tracy\Dumper::toHtml($result, array(\Tracy\Dumper::COLLAPSE => TRUE, \Tracy\Dumper::DEPTH => 7));
		return \Nette\Utils\Html::el()->setHtml($html);
	}

	public function getStorageType()
	{
		return $this->type;
	}

	public function getDatabaseName()
	{
		if ($this->type === 'mysql')
		{
			$db = array('db_foo', 'db_bar');
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
				$query = json_decode(file_get_contents(__DIR__ . '/request.elastic'), TRUE);
				return \Nette\Utils\Html::el('')->setHtml(\Tracy\Dumper::toHtml($query, array(\Tracy\Dumper::COLLAPSE_COUNT => 1, \Tracy\Dumper::DEPTH => 10)));
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

	/**
	 * e.g. SQL explain
	 *
	 * @return NULL|\Nette\Utils\Html|string
	 */
	public function getInfo()
	{
		switch ($this->type)
		{
			case 'mysql':
				$info = array(array(
					'id' => '1',
					'select_type' => 'SIMPLE',
					'table' => 'user',
					'partitions' => 'NULL',
					'type' => 'ALL',
					'possible_keys' => 'NULL',
					'key' => 'NULL',
					'key_len' => 'NULL',
					'ref' => 'NULL',
					'rows' => '2154',
					'Extra' => 'NULL',
				));
				break;
			case 'postgres':
				$info = array(
					'Limit  (cost=0.14..10.26 rows=50 width=341)',
					'  ->  Index Scan Backward using blueprints_pkey on blueprints  (cost=0.14..20.37 rows=100 width=341)',
				);
				break;
			default:
				return NULL;
		}
		$html = \Tracy\Dumper::toHtml($info, array(\Tracy\Dumper::COLLAPSE => TRUE));
		return \Nette\Utils\Html::el()->setHtml($html);
	}

}
