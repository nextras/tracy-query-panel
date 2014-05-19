<?php

namespace Mikulas\Tracy\QueryPanel;

use Everyman\Neo4j\Command;
use Everyman\Neo4j\Query;
use Everyman\Neo4j\Query\ResultSet;
use Everyman\Neo4j\Transport;
use Nette\Object;
use Nette\Utils\Html;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Tracy\Dumper;
use Tracy\QueryPanel\IQuery;


class Neo4jQuery extends Object implements IQuery
{

	/** @var Command */
	private $command;

	/** @var ResultSet */
	private $result;

	/** @var Transport */
	private $transport;

	/**
	 * @param Command $command
	 * @param ResultSet $result
	 * @param Transport $transport
	 */
	public function __construct($command, $result, $transport)
	{
		$this->command = $command;
		$this->result = $result;
		$this->transport = $transport;
	}

	/**
	 * @return int
	 */
	public function getResultCount()
	{
		return $this->result->count();
	}

	/**
	 * @return Html|string
	 */
	public function getResult()
	{
		$data = iterator_to_array($this->result);
		foreach ($data as &$row)
		{
			$row = $this->rowToArray($row);
		}

		$html = Dumper::toHtml($data, [
			Dumper::COLLAPSE => TRUE,
		]);
		return Html::el()->setHtml($html);
	}

	private function rowToArray(Query\Row $row)
	{
		$row = iterator_to_array($row);
		foreach ($row as &$value)
		{
			if ($value instanceof Query\Row)
			{
				$value = $this->rowToArray($value);
			}
		}
		return $row;
	}

	/**
	 * Arbitrary identifier such as mysql, postgres, elastic, neo4j
	 *
	 * @return string
	 */
	public function getStorageType()
	{
		return 'neo4j';
	}

	/**
	 * Database, fulltext index or similar, NULL if not applicable
	 *
	 * @return NULL|string
	 */
	public function getDatabaseName()
	{
		return NULL;
	}

	/**
	 * @return Html|string
	 */
	public function getQuery()
	{
		$hack = Access($this->command);
		/** @var Query $query */
		$query = $hack->query;
		$html = $this->formatQuery($query->getQuery());
		return Html::el()->setHtml($html);
	}

	private function formatQuery($query)
	{
		$query = preg_replace('~^\s+~m', '', $query);

		$query = preg_replace('~\b(AS|ASSERT|CONSTRAINT|CREATE|DELETE|DROP|FOREACH|IS|LIMIT|(OPTIONAL\s+)?MATCH|MERGE|MATCH|ON|ORDER BY|REMOVE|RETURN|SET|SKIP|UNIQUE|WHERE|WITH)\b~',
			'<strong style="color: blue">$0</strong>', $query);
		$query = preg_replace('~\W(\]?->?)?\(.*?\)(<?-\[?)?~',
			'<strong style="color: green">$0</strong>', $query);

		return "<pre>$query</pre>";
	}

	/**
	 * @return NULL|float ms
	 */
	public function getElapsedTime()
	{
		$hack = Access($this->transport);
		return curl_getinfo($hack->handle, CURLINFO_TOTAL_TIME) * 1000;
	}

	/**
	 * e.g. SQL explain
	 *
	 * @return NULL|Html|string
	 */
	public function getInfo()
	{
		$hack = Access($this->command, 'getData');
		$html = $this->formatVars($hack->call()['params']);
		return Html::el()->setHtml($html);
	}

	private function formatVars($vars)
	{
		$keys = '<tr>';
		$vals = '<tr>';
		foreach ($vars as $key => $val)
		{
			$keys .= "<th>$key</th>";
			$vals .= "<td><code>$val</code></td>";
		}
		$keys .= '</tr>';
		$vals .= '</tr>';
		return "<table>{$keys}{$vals}</table>";
	}

}
