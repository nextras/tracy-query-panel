<?php

namespace NextrasTests\TracyQueryPanel;

use Mockery;


class TestCase extends \Tester\TestCase
{

	protected function tearDown()
	{
		parent::tearDown();
		Mockery::close();
	}

}
