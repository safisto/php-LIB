<?php

include_once( 'LIB/Db/DataSource.php' );

class PdoDataScource extends DataSource
{
	protected $username;
	protected $password;
	
	function __construct( $dsn, $username, $password, $charset, $options = array() )
	{
		parent::__construct( $dsn, $charset, $options );
		
		$this->username = $username;
		$this->password = $password;
	}
	
	public final function getUsername()
	{
		return $this->username;
	}
	
	public final function getPassword()
	{
		return $this->password;
	}
	
}

?>