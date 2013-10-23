<?php

include_once( 'LIB/Util/Converter.php' );

if( !defined( 'CONTROLLER_FOLDER' ) )
{
	define( 'CONTROLLER_FOLDER', 'controller' );
}
if( !defined( 'DEBUG' ) )
{
	define( 'DEBUG', 0 );
}

class HttpRequestHandler
{
	private $request;

	function __construct()
	{
		$this->request = new Request();
	}

	public function handleRequest()
	{
		$classname = $this->resolveControllerClassname();

		$controller = $this->getController( $classname );

		if( DEBUG >= 8 )
		{
			var_dump::display($this->request);
			var_dump::display($controller);
		}

		if( !is_null( $controller ) )
		{
			$controller->handleRequest( $this->request );
		}
	}

	protected final function getRequest()
	{
		return $this->request;
	}

	protected function resolveControllerClassname()
	{
		$classname = NULL;

		$parts = explode( '/', $this->request->getPath() );
		foreach( $parts as $part )
		{
			if( strlen( $part ) > 0 )
			{
				$classname .= strtoupper( substr( $part, 0, 1 ) ) . strtolower( substr( $part, 1 ) );
			}
		}
		if( is_null( $classname ) )
		{
			$classname = 'Default';
		}

		$classname .= 'Controller';

		return $classname;
	}

	protected function getController( $classname )
	{
		$file = CONTROLLER_FOLDER . '/' . $classname . '.php';
		if( !include_once( $file ) )
		{
			return NULL;
		}

		// check if class exists
		if( !class_exists( $classname ) )
		{
			$msg = 'Controller class "' . $classname . '" not found.';
			throw new Exception( $msg );
		}

		// create instance
		$controller = new $classname;

		// check if instance is type of Controller
		if( !($controller instanceof Controller) )
		{
			$msg = 'Class "' . $classname . '" is not of type Controller.';
			throw new Exception( $msg );
		}

		return $controller;
	}

}

class Request
{
	private $path;
	private $filename;
	private $params;
	private $files;

	function __construct()
	{
		$pos = strpos( $_SERVER['REQUEST_URI'], '?' );
		if( $pos !== false )
		{
			$uri = substr( $_SERVER['REQUEST_URI'], 0, $pos );
		}
		else
		{
			$uri = $_SERVER['REQUEST_URI'];
		}

		if( preg_match( '#(.*\/)([^/]*)#', $uri, $regs ) )
		{
			$this->path = $regs[1];
			$this->filename = $regs[2];
		}

		if( is_null( $this->path ) )
		{
			$this->path = '/';
		}
		if( is_null( $this->filename ) || !$this->filename )
		{
			$this->filename = 'index.php';
		}

		$this->params = $_REQUEST;

		if( !is_null( $_FILES ) && count( $_FILES ) )
		{
			$this->files = array();
			foreach( $_FILES as $key => $file )
			{
				if( $file['tmp_name'] )
				{
					$this->files[$key] = $file;
				}
			}
		}
	}

	public function getUri()
	{
		return $this->path . $this->filename;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getFilename()
	{
		return $this->filename;
	}

	public function getValue( $name, $key = null )
	{
		$value = $this->getRawValue( $name, $key );
		if( !is_null( $value ) )
		{
			return Converter::convertString( $value );
		}
		return NULL;
	}

	public function getRawValue( $name, $key = null )
	{
		if( isset( $this->params[$name] ) )
		{
			if( !is_array( $this->params[$name] ) )
			{
				return $this->params[$name];
			}
			elseif( !is_null( $key ) && array_key_exists( $key, $this->params[$name]) )
			{
				return $this->params[$name][$key];
			}
		}
		return NULL;
	}

	public function getIntValue( $name, $key = null )
	{
		$value = $this->getRawValue( $name, $key );
		if( !is_null( $value ) )
		{
			return Converter::convertInt( $value );
		}
		return NULL;
	}

	public function hasValue( $name, $key = null )
	{
		if( isset( $this->params[$name] ) )
		{
			if( !is_array( $this->params[$name] ) )
			{
				return true;
			}
			elseif( !is_null( $key ) && array_key_exists( $key, $this->params[$name]) )
			{
				return true;
			}
		}
		return false;
	}

	public function isArrayValue( $name )
	{
		return isset( $this->params[$name] ) && is_array( $this->params[$name] );
	}

	public function getArrayValueKeys( $name )
	{
		if( $this->isArrayValue( $name ) )
		{
			return array_keys( $this->params[$name] );
		}
		return NULL;
	}

	public function getArrayValueCount( $name )
	{
		if( $this->isArrayValue( $name ) )
		{
			return count( $this->params[$name] );
		}
		return -1;
	}

	public function isArrayValueEmpty( $name )
	{
		return $this->getArrayValueCount( $name ) == 0;
	}

	public function getFile( $name )
	{
		if( !$this->hasFile( $name ) )
		{
			return NULL;
		}
		return $this->files[$name];
	}

	public function hasFile( $name )
	{
		return ( !is_null( $this->files) && array_key_exists( $name, $this->files ) );
	}

}

?>