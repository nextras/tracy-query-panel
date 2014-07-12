<?php

namespace Mikulas\Tracy\QueryPanel;

use DibiEvent;
use Nette\Object;
use Nette\Utils\Html;
use Tracy\Dumper;
use Tracy\QueryPanel\IQuery;


class DibiQuery extends Object implements IQuery
{

	/** @var DibiEvent */
	private $event;

	public function __construct(DibiEvent $event)
	{
		$this->event = $event;
	}

	/**
	 * @return int
	 */
	public function getResultCount()
	{
		return $this->event->result->getRowCount();
	}

	/**
	 * @return Html|string
	 */
	public function getResult()
	{
		if (!$this->event->result)
		{
			return;
		}

		$html = Dumper::toHtml($this->event->result->fetchAll(), [
			Dumper::COLLAPSE => TRUE,
			Dumper::TRUNCATE => 50,
		]);
		return Html::el()->setHtml($html);
	}

	/**
	 * Arbitrary identifier such as mysql, postgres, elastic, neo4j
	 *
	 * @return string
	 */
	public function getStorageType()
	{
		$class = get_class($this->event->connection->driver);
		$name = preg_replace('~^Dibi|Driver$~', '', $class);
		return lcFirst($name);
	}

	/**
	 * Database, fulltext index or similar, NULL if not applicable
	 *
	 * @return NULL|string
	 */
	public function getDatabaseName()
	{
		return $this->event->connection->databaseInfo->name;
	}

	/**
	 * @return Html|string
	 */
	public function getQuery()
	{
		$html = \dibi::dump($this->event->sql, TRUE);
		return Html::el()->setHtml($html);
	}

	/**
	 * @return NULL|float ms
	 */
	public function getElapsedTime()
	{
		return $this->event->time * 1000;
	}

	/**
	 * e.g. SQL explain
	 *
	 * @return NULL|Html|string
	 */
	public function getInfo()
	{
		$query = 'EXPLAIN (FORMAT JSON) (' . $this->event->sql . ')';
		$explain = $this->event->connection->nativeQuery($query);
		$data = json_decode($explain->fetchSingle(), TRUE);
		return Html::el()->setHtml(Dumper::toHtml($data, [
			Dumper::COLLAPSE => TRUE,
		]));
	}

}
