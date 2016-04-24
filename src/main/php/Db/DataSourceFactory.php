<?php

class DataSourceFactory
{
	
	private function DataSourceFactory()
	{
	}
	
	public static function getDataSource( $xml ) 
	{
		if( !isset( $xml->dsn ) )
		{
			return null;
		}
		
		$dsn = (string)$xml->dsn;
		$charset = isset( $xml->charset ) ? (string)$xml->charset : null;
		$options = array();
		if( isset( $xml->option ) )
		{
			foreach( $xml->option  as $option )
			{
				$attribs = $option->attributes();
		
				if( isset( $attribs->key ) )
				{
					$key = (string) $attribs->key;
				}
				else
				{
					$msg = 'Attribute "key" has not been set for the database "option" element.';
					throw new Exception( $msg );
				}
		
				if( isset( $attribs->value ) )
				{
					$value = (string) $attribs->value;
				}
				else
				{
					$msg = 'Attribute "value" has not been set for the database "option" element.';
					throw new Exception( $msg );
				}
		
				$options[$key] = $value;
			}
		}
		
		$dsType = isset( $xml['type'] ) ? strtolower( $xml['type'] ) : 'pdo';
		
		if( $dsType == 'mdb2' )
		{
			include_once( 'LIB/Db/Mdb2DataSource.php' );
			$ds = new Mdb2DataScource( $dsn, $charset, $options );
		}
		else
		{
			$username = isset( $xml->username ) ? (string)$xml->username : null;
			$password = isset( $xml->password ) ? (string)$xml->password : null;

			include_once( 'LIB/Db/PdoDataSource.php' );
			$ds = new PdoDataScource( $dsn, $username, $password, $charset, $options );
		}
		
		return $ds;
	}
	
}

?>