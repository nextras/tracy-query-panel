<?php


class NDBConnectionMock extends Nette\Database\Connection
{

	public function connect()
	{
		return;
	}

	public function query($statement = NULL)
	{
		$result = Mockery::mock('\\Nette\\Database\\ResultSet');

		$result->shouldReceive('getConnection')
			->andReturn($this);
		$result->shouldReceive('getTime')
			->andReturn(1337);
		$result->shouldReceive('getQueryString')
			->andReturn($statement);
		$result->shouldReceive('getParameters')
			->andReturn([]);
		$result->shouldReceive('getColumnCount')
			->andReturn(1);
		$result->shouldReceive('valid')
			->andReturn(TRUE)
			->andReturn(FALSE);
		$result->shouldReceive('rewind')
			->andReturn([1]);

		$this->onQuery($this, $result);
	}

}
