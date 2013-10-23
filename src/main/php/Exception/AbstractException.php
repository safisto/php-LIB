<?php

abstract class AbstractException extends Exception
{
	private $msg;
	
	function __toString()
	{
		return get_class( $this ) . ': ' . $this->msg;
	}
	
	protected function setMessage( $msg )
	{
		$this->msg = $msg;
	}
	
}

?>