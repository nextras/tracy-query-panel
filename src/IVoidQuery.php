<?php

namespace Nextras\TracyQueryPanel;

use Nette\Utils\Html;


/**
 * Query without meaningful result (sql insert, fulltext index, …)
 */
interface IVoidQuery
{

	/**
	 * Arbitrary identifier such as mysql, postgres, elastic, neo4j
	 * @return string
	 */
	public function getStorageType();



	/**
	 * Database name, fulltext index or similar, NULL if not applicable
	 * @return NULL|string
	 */
	public function getDatabaseName();



	/**
	 * Actual formatted query, e.g. 'SELECT * FROM ...'
	 * @return Html|string
	 */
	public function getQuery();



	/**
	 * @return NULL|float ms
	 */
	public function getElapsedTime();


	/**
	 * e.g. explain
	 * @return NULL|Html|string
	 */
	public function getInfo();

}
