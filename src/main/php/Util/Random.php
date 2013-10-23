<?php

class Random
{
	const SALT = 'ABCHEFGHJKMNPQRSTUVWXYZ0123456789';

	public static function password( $numberCharacters = 6 )
	{
		srand( (double)microtime() * 1000000 );
		$password = '';
		for( $i=0; $i <= $numberCharacters; $i++ )
		{
			$num = rand() % 33;
			$tmp = substr( self::SALT, $num, 1 );
			$password .= $tmp;
		}
		return $password;
	}
	
}

?>