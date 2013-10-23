<?php

class InstantiationException extends AbstractException
{
	
	function InstantiationException( $args )
	{
		if( is_array( $args ) && count( $args ) )
		{
			$this->setMessage( $args[0] );			
		}
		elseif ( is_string( $args ) )
		{
			$this->setMessage( $args );
		}
	}
	
}

?>