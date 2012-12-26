<?php

include_once( 'LIB/Config/Config.php' );
include_once( 'LIB/Db/DataSourceFactory.php' );
include_once( 'LIB/Menu/MenuFactory.php' );

if( !defined( 'APPLICATION_CONFIG_FILE' ) )
{
	define( 'APPLICATION_CONFIG_FILE', 'application.xml' );
}

class WebApplicationConfig extends Config
{
	private $dataSource;
	private $menu;
	private $properties;
	
	function __construct()
	{
		parent::__construct( APPLICATION_CONFIG_FILE );
	}
	
	protected function parseXml( $xml )
	{
		if( $xml->getName() != 'application' )
		{
			$msg = 'Invalid config file. Root element must be "application".';
			throw new Exception( $msg );
		}
		if( isset( $xml->database ) )
		{
			$this->dataSource = DataSourceFactory::getDataSource( $xml->database );
		}
		if( isset( $xml->menu ) )
		{
			$this->menu = MenuFactory::getMenu( $xml->menu );
		}
		if( isset( $xml->properties ) )
		{
			$this->properties = Config::extractProperties( $xml->properties );
		}
		
		if( is_null( $this->properties) )
		{
			$this->properties = array();
		}
	}

	protected function getParsedItems()
	{
		$items = array(
			'dataSource'	=> $this->dataSource,
			'menu'			=> $this->menu,
			'properties'	=> $this->properties
		);
		return $items;
	}
	
	protected function setParsedItems( $items )
	{
		$this->dataSource	= $items['dataSource'];
		$this->menu			= $items['menu'];
		$this->properties   = $items['properties'];
	}
	
	public function getDataSource()
	{
		return $this->dataSource;
	}
	
	public function getMenu() 
	{
		return $this->menu;
	}
	
	public function getProperties()
	{
		return $this->properties;
	}
	
}

?>