<?php

include_once( 'LIB/Db/DataSource.php' );

class Mdb2DataScource extends DataSource
{

	function __construct( $dsn, $charset, $options )
	{
		parent::__construct( $dsn, $charset, $options );
	}
	
}

?>