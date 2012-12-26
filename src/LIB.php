<?php

if( !defined( 'DEBUG' ) )
{
	define( 'DEBUG', 0 );
}

if( DEBUG )
{
    include_once( 'Var_Dump.php' );
}

class LIB
{
	private function LIB()
	{
	}

	public static final function exception( $obj, $exceptionClass )
	{
		include_once( 'LIB/exception/AbstractException.php' );
		
		if( !is_object($obj) )
		{
			die( 'Could not create exception instance for a non object caller: ' . $obj );
		}
		elseif( is_null( $exceptionClass ) )
		{
			die( 'Could not create exception instance for object caller \'' . get_class( $obj ) . '\': exception classname is null.' );
		}
		elseif( !is_string( $exceptionClass ) )
		{
			die( 'Could not create exception instance for object caller \'' . get_class( $obj ) . '\': exception classname is not of type string.' );
		}
		elseif( !strlen( $exceptionClass ) )
		{
			die( 'Could not create exception instance for object caller \'' . get_class( $obj ) . '\': exception classname is empty.' );
		}
		elseif ( !@include_once( 'LIB/exception/' . $exceptionClass . '.php' ) )
		{
			die( 'Could not create exception instance for object caller \'' . get_class( $obj ) . '\': exception classname file could not be found.' );
		}
		elseif ( !class_exists( $exceptionClass ) )
		{
			die( 'Could not create exception instance for object caller \'' . get_class( $obj ) . '\': exception class could not be found.' );
		}
		$args = array_slice( func_get_args(), 2 );
		throw( new $exceptionClass( $args ) );
	}

}

?>