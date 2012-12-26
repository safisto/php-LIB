<?php

class Rss
{
	public $title;
	public $link;
	public $description;
	public $language;
	public $copyright;
	private $items = array();
	
	public function addItem( $item )
	{
		array_push( $this->items, $item );
	}
	
	public function getItems()
	{
		return $this->items;
	}
	
}

class RssItem
{
	public $title;
	public $description;
	public $link;
	public $author;
	public $id;
	public $date;	
}

?>