<?php

include_once( 'LIB/Db/DataSource.php' );

class DataSourceFactory
{
	
	private function DataSourceFactory()
	{
	}
	
	public static function getDataSource( $xml ) 
	{
		if( !isset( $xml->dsn ) )
		{
			return NULL;
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
		return new DataSource( $dsn, $charset, $options );
	}
	
}

?>