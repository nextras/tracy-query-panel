<?php

namespace Tracy\QueryPanel;

use Latte;
use Nette;
use Tracy;


class QueryPanel extends Nette\Object implements Tracy\IBarPanel
{

	/** @var QueryCollector */
	protected $collector;

	/** @var array */
	private $colorMap;



	public function __construct()
	{
		$this->collector = new QueryCollector();
	}



	public function addQuery(IQuery $query)
	{
		$this->collector->addQuery($query);
	}



	/**
	 * @return string
	 */
	public function getTitle()
	{
		$c = $this->collector->count();
		if ($c === 0)
		{
			$title = 'no queries';
		}
		else if ($c === 1)
		{
			$title = '1 query';
		}
		else
		{
			$title = "$c queries";
		}

		return "$title, " . number_format($this->collector->totalElapsedTime, 1) . '&nbsp;ms';
	}



	/**
	 * Renders HTML code for custom tab.
	 *
	 * @return string
	 */
	public function getTab()
	{
		$img = base64_encode(file_get_contents(__DIR__ . '/icon.svg'));
		return '<img width="16" height="16" src="data:image/svg+xml;base64,' . $img . '" />'
			. $this->getTitle()
			. '</span>';
	}



	/**
	 * Renders HTML code for custom panel.
	 *
	 * @return string
	 */
	public function getPanel()
	{
		$latte = new Latte\Engine;

		$latte->addFilter('color', $this->getRandomColor);

		$args = [
			'title' => $this->getTitle(),
			'collector' => $this->collector,
		];

		return $latte->renderToString(__DIR__ . '/queryPanel.latte', $args);
	}



	public function getRandomColor($node)
	{
		$key = "$node->storageType|$node->databaseName";
		if (!isset($this->colorMap[$key]))
		{
			srand(hexdec(base64_encode($key)) + 3);

			$master = [0xE6, 0xDF, 0xBF];
			$color = [];
			foreach ($master as $i => $base)
			{
				$color[$i] = dechex(($base + rand(0, 255) * 3) / 4);
			}

			$this->colorMap[$key] = implode('', $color);

			srand(mt_rand());
		}

		return $this->colorMap[$key];
	}

}
