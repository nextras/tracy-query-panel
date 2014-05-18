<?php

namespace Tracy\QueryPanel;

use Nette;


class QueryCollector extends Nette\Object
{

	/** @var IQuery[] */
	protected $queries = array();



	public function addQuery(IQuery $query)
	{
		$this->queries[] = $query;
	}



	/**
	 * @return float ms
	 */
	public function getTotalElapsedTime()
	{
		$elapsed = 0;
		foreach ($this->queries as $query)
		{
			$elapsed += $query->getElapsedTime();
		}

		return $elapsed;
	}



	public function getElapsedTimePerStorage()
	{
		$elapsed = array();
		foreach ($this->queries as $query)
		{
			$storage = $query->getStorageType();
			$db = $query->getDatabaseName();
			$key =  "$storage|$db";

			if (!isset($elapsed[$key]))
			{
				$elapsed[$key] = (object) array(
					'storageType' => $storage,
					'databaseName' => $db,
					'elapsed' => 0,
				);
			}
			$elapsed[$key]->elapsed += $query->getElapsedTime();
		}

		usort($elapsed, function($a, $b) {
			return $a->elapsed < $b->elapsed;
		});

		return $elapsed;
	}



	/**
	 * @return IQuery[]
	 */
	public function getQueries()
	{
		return $this->queries;
	}



	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->queries);
	}


	/**
	 * @return array [float $min, float $max]
	 */
	public function getTimeExtremes()
	{
		$times = array();
		foreach ($this->queries as $query)
		{
			$times[] = $query->getElapsedTime();
		}
		return array(min($times), max($times));
	}

}
