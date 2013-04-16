<?php

include_once( 'LIB/Util/Validator.php' );

class Converter
{

	public static function convertInt( $value )
	{
		if( Validator::isInt( $value) )
		{
			return (int)$value;
		}
		return NULL;
	}

	public static function convertString( $value )
	{
        if( !trim( $value ) )
        {
            return NULL;
        }
        else
        {
            return strip_tags( $value );
        }
	}

    public static function encodeSingleQuotes( $value )
    {
        if( is_null( $value ) )
        {
            return null;
        }
        else
        {
            return preg_replace( '#\'#', '\\\'', $value );
        }
    }

    public static function encodeDoubleQuotes( $value )
    {
        if( is_null( $value ) )
        {
            return null;
        }
        else
        {
            return preg_replace( '#"#', '\"', $value );
        }
    }

    public static function decodeSingleQuotes( $value )
    {
        if( is_null( $value ) )
        {
            return null;
        }
        else
        {
            return preg_replace( '#\\\\\'#', '\'', $value );
        }
    }

    public static function decodeDoubleQuotes( $value )
    {
        if( is_null( $value ) )
        {
            return null;
        }
        else
        {
            return preg_replace( '#\"#', '"', $value );
        }
    }

}

?>