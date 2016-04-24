<?php

if( !defined( 'MODULE_FOLDER' ) )
{
	define( 'MODULE_FOLDER', 'module' );
}

class ModuleService
{
	private $modules = array();
	
	private $dataSource;
	private $connection;
	
	function __construct( $dataSource = NULL )
	{
		$this->dataSource = $dataSource;
	}
	
	private function getMdb2Connection()
	{
		if( is_null( $this->connection ) )
		{
			include_once( 'MDB2.php' );
			
			$this->connection = MDB2::factory( $this->dataSource->getDsn(), $this->dataSource->getOptions() );
			if ( PEAR::isError( $this->connection ) )
			{
				$msg = 'Unable to create connection. Cause: ' . $this->connection->getMessage();
				throw new Exception( $msg );
			}
			$this->connection->setFetchMode( MDB2_FETCHMODE_OBJECT );
			
			$charset = $this->dataSource->getCharset();
			if( !is_null( $charset ) )
			{
				$this->connection->setCharset( $charset );
			}
		}
		return $this->connection;
	}

	private function getPdoConnection()
	{
		if( is_null( $this->connection ) )
		{
			$charset = $this->dataSource->getCharset();
			if( !is_null( $charset ) )
			{
				$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $charset);
			}
			else
			{
				$options = array();
			}
			
			$this->connection = new PDO( $this->dataSource->getDsn(), $this->dataSource->getUsername(), $this->dataSource->getPassword(), $options );
		}
		return $this->connection;
	}

	public function destroy()
	{
		$this->modules = NULL;
		
		// make sure the database connection is closed
		if( !is_null( $this->connection ) && !$this->connection instanceof PDO )
		{
			$this->connection->disconnect();
		}
	}
	
	protected function resolveModuleClassname( $moduleName )
	{
		return strtoupper( substr( $moduleName, 0, 1 ) ) . substr( $moduleName, 1 ) . 'Module';
	}
	
	public function getModule( $moduleName )
	{
		if( !array_key_exists( $moduleName, $this->modules ) )
		{
			$classname = $this->resolveModuleClassname( $moduleName );
			
			$file = MODULE_FOLDER . '/' . $classname . '.php';
			if( !@include_once( $file ) )
			{
				$msg = 'Unable to load file "' . $file . '".';
				throw new Exception( $msg );
			}
	
			// check if class exists
			if( !class_exists( $classname ) )
			{
				$msg = 'Module class "' . $classname . '" not found.';
				throw new Exception( $msg );
			}
	
			// create instance
			$module = new $classname;
			
			// check if instance is type of Module
			if( !($module instanceof Module) )
			{
				$msg = 'Class "' . $classname . '" is not of type Module.';
				throw new Exception( $msg );
			}
			
			$this->modules[$moduleName] = $module;
		}
		return $this->modules[$moduleName];
	}

	public function executeModule( $moduleName, $moduleFunction, $moduleParams )
	{
		$module = $this->getModule( $moduleName );

		if( !method_exists( $module, $moduleFunction ) )
		{
			$msg = 'Function  "' . $moduleFunction . '" of module "' . $moduleName . '" does not exist.';
			throw new Exception( $msg );
		}

		if( is_null( $moduleParams ) )
		{
			$moduleParams = new stdClass(); 
		}
		
		if( $module instanceof Mdb2Module )
		{
			return $module->$moduleFunction( $this->getMdb2Connection(), $moduleParams );
		}
		elseif( $module instanceof PdoModule )
		{
			return $module->$moduleFunction( $this->getPdoConnection(), $moduleParams );
		}
		else
		{
			return $module->$moduleFunction( $moduleParams );
		}
	}
	
}

?>