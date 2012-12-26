<?php

class Menu
{
	private $menuItems = array();
	private $links = array();
	private $link2role = array();
	private $selectedItem = NULL;
	private $uri;
	
    public function addMenuItem( $menuItem, $rootMenuItem = NULL )
    {
        if( is_null( $rootMenuItem ) )
        {
            $this->menuItems[] = $menuItem;
        }
        else
        {
            $menuItem->setRootMenuItem( $rootMenuItem );
            $rootMenuItem->addMenuItem( $menuItem );
        }

        if( !is_null( $menuItem->link ) )
        {
            $link = $menuItem->link;
            if( ereg( '^/', $link ) )
            {
                if( ereg( '/$', $link ) )
                {
                    $link .= 'index.php';
                }
            }
            $this->notifyLinkToRootMenuItems( $link, $menuItem );

            $role = $menuItem->getUserRole();
            if( !is_null( $role ) )
            {
                $this->link2role[$link] = $role;
            }
        }
    }

    private function notifyLinkToRootMenuItems( $link, $menuItem )
    {
        if( !array_key_exists( $link, $this->links ) )
        {
            $this->links[$link] = array();
        }
        $this->links[$link][] = $menuItem;

        $rootMenuItem = $menuItem->getRootItem();
        if( !is_null( $rootMenuItem ) )
        {
            $this->notifyLinkToRootMenuItems( $link, $rootMenuItem );
        }
    }

	public function setUri( $uri )
	{
		$this->uri = $uri;

		if( isset( $this->links[$uri] ) )
		{
			$items = $this->links[$uri];
			foreach( $items as $item )
			{
				$item->setSelected( true );
			}
			
			$this->selectedItem = NULL;
			foreach( $this->menuItems as $item )
			{
				if( $item->isSelected() )
				{
					$this->selectedItem = $item;
					break;
				}
			}
		}
	}
	
	public function getRequiredUserRole()
	{
		if( isset( $this->link2role[$this->uri] ) )
		{
			return $this->link2role[$this->uri];
		}
		return NULL;		
	}

	public function filterByUserRoles( $roles )
	{
		if( is_null( $roles ) || !is_array( $roles ) )
		{
			$roles = array();
		}
		
		for( $i = 0; $i < count( $this->menuItems ); $i++ )
		{
			$role = $this->menuItems[$i]->getUserRole();
			
			$this->menuItems[$i]->filterByUserRoles( $roles );

			if( !is_null( $role ) && !in_array( $role, $roles ) )
			{
				array_splice( $this->menuItems, $i, 1 );
				$i--;
				continue;
			}
		}
	}
	
	public function getItems( $usergroups = NULL )
	{
		return $this->menuItems;
	}

	public function getSelectedItem()
	{
		return $this->selectedItem;
	}
	
	public function hasSelectedItem()
	{
		return !is_null( $this->selectedItem );
	}
}

class MenuItem
{
    public $id;
    public $title;
	public $link;
    public $target;
    
	private $userrole;
	private $root;
    private $items = array();
	private $selected = false;
	private $properties = array();
	
	function MenuItem( $title, $userrole, $link, $target = NULL )
	{
		$this->title = $title;
		$this->userrole = $userrole;
		$this->link = $link;
        $this->target = $target;
	}

    public function getId()
    {
        return $this->id;
    }

    public function setRootMenuItem( $menuItem )
    {
        $this->root = $menuItem;
    }

    public function addMenuItem( $menuItem )
    {
        if( !in_array( $menuItem, $this->items ) )
        {
            $this->items[] = $menuItem;
        }
    }
	
    public function addProperty( $key, $value )
    {
    	$this->properties[$key] = $value;
    }
    
    public function getProperty( $key )
    {
    	return ( array_key_exists( $key, $this->properties ) ? $this->properties[$key] : null );
    }
    
	public function getUserRole()
	{
		return $this->userrole;
	}

    public function getRootItem()
    {
        return $this->root;
    }

	public function getItems()
	{
		return $this->items;
	}
	
	public function hasItems()
	{
		return count( $this->items );
	}

	public function setSelected( $selected )
	{
		$this->selected = (boolean)$selected;
	}
	
	public function isSelected()
	{
		return $this->selected;
	}

	public function getSelectedItem()
	{
		foreach( $this->items as $item )
		{
			if( $item->isSelected() )
			{
				return $item;
			}
		}
		return null;
	}
	
	public function hasSelectedItem()
	{
		foreach( $this->items as $item )
		{
			if( $item->isSelected() )
			{
				return true;
			}
		}
		return false;
	}

	public function filterByUserRoles( $roles )
	{
		if( is_null( $roles ) || !is_array( $roles ) )
		{
			$roles = array();
		}
		
		for( $i = 0; $i < count( $this->items ); $i++ )
		{
			$role = $this->items[$i]->getUserRole();
			if( !is_null( $role ) && !in_array( $role, $roles ) )
			{
				array_splice( $this->items, $i, 1 );
				$i--;
				continue;
			}
			$this->items[$i]->filterByUserRoles( $roles );
		}
	}
	
}

?>