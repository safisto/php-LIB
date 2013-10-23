<?php

final class DataSource
{
	private $dsn;
	private $charset;
	private $options;
	
	function DataSource( $dsn, $charset, $options = array() )
	{
		$this->dsn = $dsn;
		$this->charset = $charset;
		$this->options = $options;
	}

	public final function getDsn()
	{
		return $this->dsn;
	}
	
	public final function getCharset()
	{
		return $this->charset;
	}

	public final function getOptions()
	{
		return $this->options;
	}
	
}

?>