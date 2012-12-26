<?php

include_once( 'Cache/Lite.php' );

class Cache extends Cache_Lite
{

	function Cache( $options = array( NULL ) )
	{
		parent::Cache_Lite( $options );
	}
	
	public final function setItem( $id, $data )
	{
		if( is_null( $id ) )
		{
			$msg = 'Cache-item id is null!';
			throw new Exception( $msg );
		}

		// create a new cache item and serialize it
		$item = serialize( new CacheItem( $data ) );
		
		// save item to cache
		$success = $this->save( $item, $id );

		// check if the cache item has been saved successfully
		if( !$success )
		{
			$msg = 'Unable to save cache-item for id: ' . $id;
			throw new Exception( $msg );
		}
	}
	
	public function getItem( $id, $expiry = NULL )
	{
		// retrieve the cache-item
		$item = unserialize( $this->get( $id ) );
		
		// check if a cache-item is available
		if( $item === false )
		{
			return NULL;
		}
		// check the instance of the cache-item
		elseif ( !($item instanceof CacheItem) )
		{
			return NULL;
		}
		// check the expiry of the cache-item
		else if( !is_null( $expiry ) && ( $item->getTime() + $expiry ) < time() )
		{
			return NULL;
		}
		return $item->getData();
	}
	
}

class CacheItem
{
	private $data;
	private $time;
	
	function CacheItem( $data )
	{
		$this->data = $data;
		$this->time = time();
	}
	
	public final function getTime()
	{
		return $this->time;
	}
	
	public final function getData()
	{
		return $this->data;
	}
	
}

?>