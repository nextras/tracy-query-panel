<?php

namespace Nextras\TracyQueryPanel;

use Latte;
use Tracy;


class QueryPanel implements Tracy\IBarPanel
{

	/** @var QueryCollector */
	protected $collector;

	/** @var array [float $min, float $max] */
	private $extremes;



	public function __construct(Tracy\Bar $bar)
	{
		$this->collector = new QueryCollector();

		$bar->addPanel($this);
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

		return "$title, " . number_format($this->collector->getTotalElapsedTime(), 1) . '&nbsp;ms';
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
	 * @return string html
	 */
	public function getPanel()
	{
		$latte = new Latte\Engine;

		$latte->addFilter('storageId', [$this, 'getStorageId']);
		$latte->addFilter('colorRange', [$this, 'getColorInRange']);

		$args = [
			'title' => $this->getTitle(),
			'collector' => $this->collector,
		];

		if ($this->collector->getQueries())
		{
			$this->extremes = $this->collector->getTimeExtremes();
		}

		return $latte->renderToString(__DIR__ . '/queryPanel.latte', $args);
	}


	/**
	 * @internal
	 * @param IQuery $query
	 * @return string
	 */
	public function getStorageId($query)
	{
		return $query->getStorageType() . '|' . $query->getDatabaseName();
	}



	/**
	 * Linear color gradient
	 * @param float $value
	 * @return string hex color
	 */
	public function getColorInRange($value)
	{
		$a = [54, 170, 31];
		$b = [220, 1, 57];

		list($min, $max) = $this->extremes;

		$d = $max - $min;
		$lin = ($value - $min) / ($d ?: 0.5); // prevent x/0

		$color = [];
		for ($i = 0; $i < 3; ++$i)
		{
			$color[$i] = (int) ($a[$i] + ($b[$i] - $a[$i]) * $lin);
		}

		return 'rgb(' . implode(',', $color) . ')';
	}

}
