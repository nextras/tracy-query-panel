<?php

namespace Tracy\QueryPanel;

use Nette\Utils\Html;


interface IQuery extends IVoidQuery
{

	/**
	 * Suggested behavior: print Tracy\Dumper::toHtml() array
	 * of returned rows so row count is immediately visible.
	 *
	 * @return Html|string
	 */
	public function getResult();

}
