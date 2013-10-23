<?php

include_once( 'LIB/Web/View.php' );

class SavantView implements View
{
	private $savant;
	private $viewName;
	
	function __construct( $savant, $viewName )
	{
		$this->savant = $savant;
		$this->viewName = $viewName;
	}
	
	public function render( $model )
	{
		if( !is_null( $model ) && is_object( $model ) )
		{
			foreach( get_object_vars( $model ) as $key => $val )
			{
				$this->savant->assign( $key, $val );
			}
		}
		$this->savant->fetch( $this->viewName );
		return $this->savant->fetch( $this->viewName );
	}
	
}

?>