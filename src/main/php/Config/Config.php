<?php

abstract class Config
{

	function __construct( $file, $cache = NULL )
	{
		// check if the config-file exists
		if( !file_exists( $file ) )
		{
			$msg = 'Config file "' . $file . '" does not exist!';
			throw new Exception( $msg );
		}

		// check if there is a cache available
		if( !is_null( $cache ) )
		{
			// get the modification time of the config-file
			$time = filemtime( $file );
			
			// try to get the items from the cache
			$items = $cache->getItem( $this->getCacheId(), $time );

			// check if there was a cache hit
			if( !is_null( $items ) )
			{
				$this->log->debug( $this, 'Using cached items ...' );
				$this->setParsedItems( $items );
				return;
			}
		}
		
		// load the config-file
		$xml = @simplexml_load_file( $file );
		if( !$xml )
		{
			$msg = 'Could not load config file "' . $file . '"';
			throw new Exception( $msg );
		}
		
		// parse the config
		$this->parseXml( $xml );
		
		// check if there is a cache available
		if( !is_null( $cache ) )
		{
			// get the items
			$items = $this->getParsedItems();
			
			// set the items into the cache
			$cache->setItem( $this->getCacheId(), $items );
		}
	}
	
	/**
	 * Returns the identifier of the cache-item.
	 *
	 * @return string Identifier
	 */
	private function getCacheId()
	{
		return 'CLASS:' . get_class( $this );
	}
	
	/**
	 * Parses the given XML to create the items held by the configuration class.
	 *
	 * @param object $xml XML 
	 */
	abstract protected function parseXml( $xml );
	
	/**
	 * Gets the parsed items of the configuration class.
	 *
	 * @return array $items Parsed configuration items
	 */
	abstract protected function getParsedItems();
	
	/**
	 * Sets the items of the configuration class
	 *
	 * @param array $items Parsed configuration items
	 */
	abstract protected function setParsedItems( $items );

	public static function extractProperties( $xml ) 
	{
		$properties = array();
		if( isset( $xml->property ) )
		{
			foreach( $xml->property  as $property )
			{
				$attribs = $property->attributes();

				if( isset( $attribs->name ) )
				{
					$name = (string) $attribs->name;

					if( isset( $attribs->value ) )
					{
						$value = (string) $attribs->value;
					}
					else
					{
						$value = null;
					}

					$properties[$name] = $value;
				}
			}
		}
		return $properties;
	}
		
}

?>