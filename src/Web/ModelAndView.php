<?php

class ModelAndView
{
	private $viewName;
	protected $model;

	function __construct( $viewName = NULL, $model = NULL )
	{
		if( !is_null( $viewName ) )
		{
			$this->viewName = $viewName;
		}

		$this->model = new stdClass();

		if( !is_null( $model ) )
		{
			if( is_object( $model )  )
			{
				foreach( get_object_vars( $model ) as $key => $val )
				{
					$this->model->$key = $val;
				}
			}
			elseif( is_array( $model ) )
			{
				foreach( $model as $key => $val )
				{
					$this->model->key = $val;
				}
			}
		}

		$this->model->javascriptFile = array();
		$this->model->stylesheetFile = array();
	}

	public function setViewName( $viewName )
	{
		$this->viewName = $viewName;
	}

	public function getViewName()
	{
		return $this->viewName;
	}

	public function hasViewName()
	{
		return !is_null( $this->viewName );
	}

	public function addObject( $attributeName, $attributeValue )
	{
		if( $attributeName == 'javascriptFile' )
		{
			throw new Exception('Internal name \'javascriptFile\' is not allowed!');
		}
		elseif( $attributeName == 'stylesheetFile' )
		{
			throw new Exception('Internal name \'stylesheetFile\' is not allowed!');
		}
		$this->model->$attributeName = $attributeValue;
	}

	public function getObject( $attributeName )
	{
		return ( property_exists( $this->model, $attributeName ) ? $this->model->$attributeName : NULL );
	}

	public function getModel()
	{
		return $this->model;
	}

	public function addJavascriptFile( $javascriptFile )
	{
		$this->model->javascriptFile[] = $javascriptFile;
	}

	public function addStylesheetFile( $stylesheetFile )
	{
		$this->model->stylesheetFile[] = $stylesheetFile;
	}

	public function clear()
	{
		$this->viewName = NULL;
		$this->model = new stdClass();
		$this->model->javascriptFile = array();
		$this->model->stylesheetFile = array();
	}

}

?>