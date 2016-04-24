<?php

class DataSource
{
	protected $dsn;
	protected $charset;
	protected $options;
	
	function __construct( $dsn, $charset, $options = array() )
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