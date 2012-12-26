<?php

include_once( 'LIB/Web/Controller.php' );
include_once( 'LIB/Web/ModelAndView.php' );

abstract class AbstractController implements Controller
{
	private $request;
	
	public function handleRequest( $request )
	{
		$this->request = $request;
		
		$modelAndView = $this->createModelAndView();

		if( $this->preHandle( $modelAndView ) )
		{
			$function = $this->resolvePageFunctionName();
			if( method_exists( $this, $function ) )
			{
				// call function
				$this->$function( $modelAndView );
			}
	
			$this->postHandle( $modelAndView );
		}

        // render page
        $this->render( $modelAndView );
	}

    protected function createModelAndView()
    {
        return new ModelAndView(  ereg_replace( '\.php$', '', $request->getUri() ) );
    }
	
	protected function preHandle( $modelAndView )
	{
		return true;
	}
	
	protected abstract function render( $modelAndView );
	
	protected function postHandle( $modelAndView )
	{
		return true;
	}
	
	protected final function getRequest()
	{
		return $this->request;
	}
	
	protected function resolvePageFunctionName()
	{
		$part = ereg_replace( '\.php$', '', $this->request->getFilename() );
		return 'get' . strtoupper( substr( $part, 0, 1 ) ) . substr( $part, 1 ) . 'Page';
	}
	
}

?>