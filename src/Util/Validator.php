<?php

class Validator
{

	public static function isInt( $value )
	{
		if( !ereg( '^[-]?[0-9]+$', $value ) )
		{
			return false;
		}
		return true;
	}
	
	public static function isEmail( $value )
	{
		return eregi( '^[a-z0-9,!#\$%&\'\*\+/=\?\^_`\{\|}~-]+(\.[a-z0-9,!#\$%&\'\*\+/=\?\^_`\{\|}~-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,})$', $value );
	}
		
	public static function isSimpleString( $value )
	{
		return eregi( '^[a-z0-9дцья_\-\ \.]+$', $value );
	}
	
}

?>