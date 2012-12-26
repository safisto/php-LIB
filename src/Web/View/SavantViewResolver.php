<?php

if( !defined( 'TEMPLATE_FOLDER' ) )
{
	define( 'TEMPLATE_FOLDER', getenv( 'DOCUMENT_ROOT' ) . '/templ' );
}
if( !defined( 'TEMPLATE_PLUGIN_FOLDER' ) )
{
	define( 'TEMPLATE_PLUGIN_FOLDER', getenv( 'DOCUMENT_ROOT' ) . '/plugin' );
}
if( !defined( 'TEMPLATE_EXTENSION' ) )
{
	define( 'TEMPLATE_EXTENSION', '.html' );
}

include_once( 'LIB/Web/ViewResolver.php' );
include_once( 'LIB/Web/View/SavantView.php' );
include_once( 'Savant3.php' );

class SavantViewResolver implements ViewResolver
{
	private $savant;
        private $templateFolders = array();
	
	function __construct()
	{
		$this->savant = new Savant3();
				
                $this->addTemplateFolder( TEMPLATE_FOLDER );

		if( !file_exists( TEMPLATE_PLUGIN_FOLDER ) )
		{
			$msg = 'Template plugin folder "' . TEMPLATE_PLUGIN_FOLDER . '" does not exist.';
			throw new Exception( $msg );
		}
		$this->savant->addPath( 'resource', TEMPLATE_PLUGIN_FOLDER );
	}

        public function addTemplateFolder( $folder )
        {
            if( !file_exists( $folder ) )
            {
                    $msg = 'Template folder "' . $folder . '" does not exist.';
                    throw new Exception( $msg );
            }
            array_unshift( $this->templateFolders, $folder );

            $this->savant->addPath( 'template', $folder );
        }
	
	public function resolveView( $uri )
	{
		$templateName = $uri . TEMPLATE_EXTENSION;

		// check, if the template exists
                $templateExists = false;
                foreach( $this->templateFolders as $templateFolder )
                {
                    if( file_exists( $templateFolder . $templateName ) )
                    {
                        $templateExists = true;
                        break;
                    }
                }
		if( !$templateExists )
		{
			$msg = 'Savant Template "' . $templateName . '" does not exist!';
			throw new Exception( $msg );
		}

                return new SavantView( $this->savant, $templateName );
	}
	
}

?>