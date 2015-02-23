<?php

namespace Nextras\TracyQueryPanel\Handlers;

use DibiConnection;
use DibiEvent;
use Nette\Utils\Html;
use Nextras\TracyQueryPanel\IQuery;
use Nextras\TracyQueryPanel\QueryPanel;
use Tracy\Dumper;


class DibiHandler implements IQuery
{

	/** @var DibiEvent */
	protected $event;

	public function __construct(DibiEvent $event)
	{
		$this->event = $event;
	}

	public static function register(DibiConnection $dibi, QueryPanel $panel)
	{
		$dibi->onEvent[] = function(DibiEvent $event) use ($panel) {
			$panel->addQuery(new static($event));
		};
	}

	/**
	 * Suggested behavior: print Tracy\Dumper::toHtml() array
	 * of returned rows so row count is immediately visible.
	 *
	 * @return NULL|Html|string
	 */
	public function getResult()
	{
		if (!$this->event->result)
		{
			return NULL;
		}

		if ($this->event->result instanceof \DibiResult)
		{
			$html = Dumper::toHtml($this->event->result->fetchAll(), [
				Dumper::COLLAPSE => TRUE,
				Dumper::TRUNCATE => 50,
			]);
		}
		else
		{
			$html = Html::el()->setText($this->event->result);
		}
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
	 * Actual formatted query, e.g. 'SELECT * FROM ...'
	 *
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
	 * @throws \DibiException
	 * @throws \Exception
	 */
	public function getInfo()
	{
		if (!$this->event->sql)
		{
			return NULL;
		}

		$query = 'EXPLAIN (FORMAT JSON) ' . $this->event->sql;
		$explain = $this->event->connection->nativeQuery($query);
		$data = json_decode($explain->fetchSingle(), TRUE);
		return Html::el()->setHtml(Dumper::toHtml($data, [
			Dumper::COLLAPSE => TRUE,
		]));
	}

}
