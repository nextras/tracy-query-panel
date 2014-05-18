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

	/** @var array [float $min, float $max] */
	private $extremes;



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
		$latte->addFilter('colorRange', $this->getColorInRange);

		$args = array(
			'title' => $this->getTitle(),
			'collector' => $this->collector,
		);

		if ($this->collector->queries)
		{
			$this->extremes = $this->collector->getTimeExtremes();
		}

		return $latte->renderToString(__DIR__ . '/queryPanel.latte', $args);
	}


	/**
	 * Linear color gradient
	 * @param float $value
	 * @return string hex color
	 */
	public function getColorInRange($value)
	{
		$a = array(54, 170, 31);
		$b = array(220, 1, 57);

		list($min, $max) = $this->extremes;

		$lin = ($value - $min) / ($max - $min);
		$color = array();
		for ($i = 0; $i < 3; ++$i)
		{
			$color[$i] = (int) ($a[$i] + ($b[$i] - $a[$i]) * $lin);
		}

		return 'rgb(' . implode(',', $color) . ')';
	}



	public function getRandomColor($node)
	{
		if ($node instanceof IVoidQuery)
		{
			$storage = $node->getStorageType();
			$name = $node->getDatabaseName();
		}
		else // StdObject
		{
			$storage = $node->storageType;
			$name = $node->databaseName;
		}
		$key = "$storage|$name";
		if (!isset($this->colorMap[$key]))
		{
			srand(hexdec(base64_encode($key)) + 3);

			$master = array(0xE6, 0xDF, 0xBF);
			$color = array();
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
