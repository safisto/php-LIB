<?php

class Validator
{

	public static function isInt( $value )
	{
		if( !preg_match( '#^[-]?[0-9]+$#', $value ) )
		{
			return false;
		}
		return true;
	}

	public static function isEmail( $value )
	{
		return preg_match( '#^[a-z0-9,!\#\$%&\'\*\+/=\?\^_`\{\|}~-]+(\.[a-z0-9,!\#\$%&\'\*\+/=\?\^_`\{\|}~-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,})$#i', $value );
	}

	public static function isSimpleString( $value )
	{
		return preg_match( '#^[a-z0-9äöüß_\-\ \.]+$#i', $value );
	}

}

?>