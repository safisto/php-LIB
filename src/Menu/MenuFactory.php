<?php

include_once( 'LIB/Menu/Menu.php' );

class MenuFactory
{
	
	private function MenuFactory()
	{
	}

	public static function getMenu( $xml ) 
	{
        $menu = new Menu();

		if( isset( $xml->item ) )
		{
            $metaInfo = new stdClass();
            $metaInfo->items = array();
            $metaInfo->linkedItems = array();
			self::extractMenuItems( $xml->item, $menu, $metaInfo );

            foreach( $metaInfo->linkedItems as $linking )
            {
                if( !array_key_exists( $linking->id, $metaInfo->items ) )
                {
                    $msg = 'Id "' . $linking->id . '" des Url Links ist nicht vorhanden.';
                    throw new Exception( $msg );
                }
                $linking->item->link = $metaInfo->items[$linking->id]->link;
            }
		}
        return $menu;
	}
	
	private static function extractMenuItems( $xml, $menu, $metaInfo, $parentMenuItem = NULL )
	{
		$menuItems = array();
		foreach( $xml as $item )
		{
			$id = NULL;
			if( isset( $item->id ) )
			{
				$id = (string) $item->id;
			}

			$usergroup = NULL;
			if( isset( $item->usergroup ) )
			{
				$usergroup = (string) $item->usergroup;
			}
			
			if( isset( $item->title ) )
			{
				$title = (string) $item->title;
			}
			else
			{
				$msg = 'Element "title" has not been set for the "menu" element.';
				throw new Exception( $msg );
			}
			
			$linkUrl = NULL;
            $linkTarget = NULL;
            $linkItem = NULL;
			if( isset( $item->link ) )
			{
				$linkAttribs = $item->link->attributes();

				if( isset( $linkAttribs->url ) )
				{
					$linkUrl = (string) $linkAttribs->url;
				}
				if( isset( $linkAttribs->item ) )
				{
					$linkItem = (string) $linkAttribs->item;
				}
                if( isset( $linkAttribs->target ) )
                {
                    $linkTarget = (string) $linkAttribs->target;
                }

				if( is_null( $linkUrl ) && is_null( $linkItem ) )
				{
					$msg = 'No "url" or "item" attribute given for "link" element of a menu item.';
					throw new Exception( $msg );
				}
				elseif( !is_null( $linkUrl ) && !is_null( $linkItem ) )
				{
					$msg = 'Only one attribute "url" OR "item" is allowed for "link" element of a menu item.';
					throw new Exception( $msg );
				}
			}

            $menuItem = new MenuItem( $title, $usergroup, $linkUrl, $linkTarget );
            
			if( isset( $item->property ) )
			{
				foreach( $item->property  as $property )
				{
					$attribs = $property->attributes();
	
					if( isset( $attribs->name ) )
					{
						$name = (string) $attribs->name;
	
						if( isset( $attribs->value ) )
						{
							$value = (string) $attribs->value;
						}
						else
						{
							$value = null;
						}
						$menuItem->addProperty( $name, $value );
					}
				}
			}
            
            if( !is_null( $id ) )
            {
	            $menuItem->id = $id;
	
	            if( array_key_exists( $id, $metaInfo->items ) )
	            {
					$msg = 'Id "' . $id . '" existiert bereits.';
					throw new Exception( $msg );
	            }
	            else
	            {
	                $metaInfo->items[$id] = $menuItem;
	            }
            }
            
            if( !is_null( $linkItem ) )
            {
                $linking = new stdClass();
                $linking->id = $linkItem;
                $linking->item = $menuItem;
                $metaInfo->linkedItems[] = $linking;
            }

            $menu->addMenuItem( $menuItem, $parentMenuItem );

			if( isset( $item->item ) )
			{
				self::extractMenuItems( $item->item, $menu, $metaInfo, $menuItem );
			}
		}
	}

}

?>