<?php

namespace Nextras\TracyQueryPanel\Handlers;

use Nette\Database as NDB;
use Nette\Utils\Html;
use Nextras\TracyQueryPanel\IQuery;
use Nextras\TracyQueryPanel\QueryPanel;


class NetteDatabaseHandler implements IQuery
{

	/** @var NDB\ResultSet */
	protected $result;

	public function __construct(NDB\ResultSet $result)
	{
		$this->result = $result;
	}

	public static function register(QueryPanel $queryPanel, NDB\Connection $connection)
	{
		/**
		 * @param NDB\ResultSet|NDB\DriverException $result
		 */
		$connection->onQuery[] = function($_, $result) use ($queryPanel) {
			if ($result instanceof NDB\ResultSet)
			{
				$queryPanel->addQuery(new static($result));
			}
		};
	}

	/**
	 * Suggested behavior: print Tracy\Dumper::toHtml() array
	 * of returned rows so row count is immediately visible.
	 *
	 * @return Html|string
	 */
	public function getResult()
	{
		ob_start();
		NDB\Helpers::dumpResult($this->result);
		return ob_get_clean();
	}

	/**
	 * Arbitrary identifier such as mysql, postgres, elastic, neo4j
	 *
	 * @return string
	 */
	public function getStorageType()
	{
		$dsn = $this->result->connection->getDsn();
		return substr($dsn, 0, strpos($dsn, ':'));
	}

	/**
	 * Database name, fulltext index or similar, NULL if not applicable
	 *
	 * @return NULL|string
	 */
	public function getDatabaseName()
	{
		$dsn = $this->result->connection->getDsn();
		$matches = [];
		if (preg_match('~\b(database|dbname)=(.*?)(;|$)~', $dsn, $matches))
		{
			return $matches[2];
		}
		return NULL;
	}

	/**
	 * Actual formatted query, e.g. 'SELECT * FROM ...'
	 *
	 * @return Html|string
	 */
	public function getQuery()
	{
		return trim(NDB\Helpers::dumpSql($this->result->getQueryString(), $this->result->getParameters(), $this->result->connection));
	}

	/**
	 * @return NULL|float ms
	 */
	public function getElapsedTime()
	{
		return $this->result->getTime();
	}

	/**
	 * e.g. explain
	 *
	 * @return NULL|Html|string
	 */
	public function getInfo()
	{
		// TODO: Implement getInfo() method.
	}

}
