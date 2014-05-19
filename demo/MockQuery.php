<?php

class MockQuery extends \Nette\Object implements \Tracy\QueryPanel\IQuery
{

	private $resultCount;
	private $elapsedTime;
	private $type;
	private $databaseName = FALSE;

	public function __construct()
	{
		$storage = array('mysql', 'postgres', 'elastic', 'neo4j', 'redis');
		$this->type = $storage[array_rand($storage)];
		$this->resultCount = mt_rand(0, 20) * 5;
		$this->elapsedTime = mt_rand(100, 6000) / 100;
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
			case 'postgres':
				$result = array();
				break;
			case 'elastic':
				$result = json_decode(file_get_contents(__DIR__ . '/response.elastic'), TRUE);
				break;
			case 'neo4j':
				$result = json_decode('[{"entity_id":2,"path_id":1,"types":["Content","Video"]}]', TRUE);
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
		if ($this->databaseName === FALSE)
		{
			if ($this->type === 'mysql')
			{
				$db = array('db_foo', 'db_bar');
				$this->databaseName = $db[array_rand($db)];
			}
			else if ($this->type === 'elastic')
			{
				$this->databaseName = 'appName';
			}
			else
			{
				$this->databaseName = NULL;
			}
		}

		return $this->databaseName;
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
				return \Nette\Utils\Html::el('')->setHtml("<pre><b>MATCH</b> (v:Video)<-[:CONTAINS]-(t:Tag)\n<b>WHERE</b> v.eid = {eid}\n<b>RETURN</b> t</pre>");
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
				$info = json_decode('[ { "Plan": { "Node Type": "Index Scan","Scan Direction": "Forward","Index Name": "videos_pkey","Relation Name": "videos","Alias": "e","Startup Cost": 0.16,"Total Cost": 13.69,"Plan Rows": 10,"Plan Width": 167,"Index Cond": "(id = ANY (\'{330,82,56,55,311,270,277,290,322,331}\'::integer[]))" } } ]', TRUE);
				break;
			case 'neo4j':
				$r = mt_rand(1, 100);
				$html = '<table><tbody><tr><th>eid</th></tr><tr><td><code>' . $r . '</code></td></tr></tbody></table>';
				return \Nette\Utils\Html::el()->setHtml($html);
			default:
				return NULL;
		}
		$html = \Tracy\Dumper::toHtml($info, array(\Tracy\Dumper::COLLAPSE => TRUE));
		return \Nette\Utils\Html::el()->setHtml($html);
	}

}
