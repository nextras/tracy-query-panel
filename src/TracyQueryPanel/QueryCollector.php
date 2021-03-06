<?php

namespace Nextras\TracyQueryPanel;


class QueryCollector implements \Countable
{

	/** @var IVoidQuery[] */
	protected $queries = array();


	/**
	 * @param IVoidQuery $query
	 */
	public function addQuery(IVoidQuery $query)
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


	/**
	 * @return array
	 */
	public function getAggregations()
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
					'count' => 0
				);
			}
			$elapsed[$key]->elapsed += $query->getElapsedTime();
			$elapsed[$key]->count += 1;
		}

		usort($elapsed, function($a, $b) {
			return $a->elapsed < $b->elapsed;
		});

		return $elapsed;
	}



	/**
	 * @return IVoidQuery[] ordered by time called asc
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
