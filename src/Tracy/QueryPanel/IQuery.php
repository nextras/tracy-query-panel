<?php

namespace Tracy\QueryPanel;

use Nette\Utils\Html;


interface IQuery extends IVoidQuery
{

	/**
	 * @return int
	 */
	public function getResultCount();



	/**
	 * @return Html|string
	 */
	public function getResult();

}
