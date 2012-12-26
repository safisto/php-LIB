<?php

include_once( 'LIB/Web/Controller/AbstractController.php' );
include_once( 'LIB/Config/WebApplicationConfig.php' );
include_once( 'LIB/Menu/Menu.php' );
include_once( 'LIB/Web/View/SavantViewResolver.php' );
include_once( 'LIB/Module/ModuleService.php' );

abstract class SimpleWebController extends AbstractController
{
	protected $menu;
	private $template;
	private $viewResolver;
	private $moduleService;
	private $properties;
	
	function __construct()
	{
		// load the web application config
		$cfg = new WebApplicationConfig();
		
		$this->menu = $cfg->getMenu();
		
		// view resolver
		$this->viewResolver = new SavantViewResolver();
		
		// module service
		$this->moduleService = new ModuleService( $cfg->getDataSource() );
		
		// application properties
		$this->properties = $cfg->getProperties();
	}

	protected function getProperty( $key )
	{
		return ( array_key_exists( $key, $this->properties ) ? $this->properties[$key] : null );
	}
	
	protected function addTemplateFolder( $folder )
	{
		$this->viewResolver->addTemplateFolder( $folder );
	}
	
	protected function preHandle( $modelAndView )
	{
		if( !parent::preHandle( $modelAndView ) )
		{
			return false;
		}
		
		$this->menu->setUri( $this->getRequest()->getUri() );

		$modelAndView->addObject( 'menu', $this->menu );
		$modelAndView->addObject( 'template', $this->template );

		return true;
	}
	
	protected function postHandle( $modelAndView )
	{
		parent::postHandle( $modelAndView );
		
		$this->moduleService->destroy();
	}

    protected function createModelAndView()
    {
        $request = $this->getRequest();
        return new ModelAndView(  ereg_replace( '\.php$', '', $request->getUri() ) );
    }

	protected function render( $modelAndView )
	{
        $data = $this->createPageData( $modelAndView );
        $page = $this->viewResolver->resolveView( $this->template );
		echo $page->render( $data );
	}
	
	protected function createPageData( $modelAndView )
    {
		$viewName = $modelAndView->getViewName();
		if( !is_null( $viewName ) )
		{
			$view = $this->resolveView( $viewName );

			$data = new stdClass();
			$data->menu = $this->menu;
			$data->content = $view->render( $modelAndView->getModel() );
		}
		else
		{
			$data = $modelAndView->getModel();
		}

        return $data;
    }
    
    protected function renderCustomView( $modelAndView )
    {
		$viewName = $modelAndView->getViewName();
		if( !is_null( $viewName ) )
		{
			$view = $this->resolveView( $viewName );
			return $view->render( $modelAndView->getModel() );
		}
    }
	
	protected function resolveView( $name )
	{
		return $this->viewResolver->resolveView( $name );
	}

	protected function getData( $moduleName, $moduleFunction, $moduleParams )
	{
		return $this->moduleService->executeModule( $moduleName, $moduleFunction, $moduleParams );
	}
	
	protected function getModule( $moduleName )
	{
		return $this->moduleService->getModule( $moduleName );
	}
	
	protected final function setTemplate( $template )
	{
		$this->template = $template;
	}
	
	protected final function loadModule( $moduleName )
	{
		$this->moduleService->getModule( $moduleName );
	}
	
}

?>