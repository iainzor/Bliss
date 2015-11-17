<?php
namespace Pages;

use Bliss\AbstractIterator;

class Container extends AbstractIterator
{
	/**
	 * Clear all pages in the container
	 */
	public function clear()
	{
		$this->clearItems();
	}
	
	/**
	 * Add one or more pages to the container
	 * The $pages argument can take either an single or multi-demensional array 
	 * with [name => value] pairs
	 * 
	 * @param array|Container $pages
	 * @return void
	 */
	public function add($pages)
	{
		if (empty($pages)) {
			return;
		}
		
		if ($pages instanceof self || $pages instanceof Page) {
			$pages = $pages->toArray();
		}
		
		$keys = array_keys($pages);
		if (!is_numeric($keys[0])) {
			$pages = [$pages];
		}
		
		foreach ($pages as $data) {
			$page  = Page::factory($data);
			$this->addItem($page);
		}
	}
	
	/**
	 * Convert the container into an array 
	 * 
	 * @return array
	 */
	public function toArray()
	{
		$items = $this->allItems();
		usort($items, function(Page $a, Page $b) {
			if ($a->order() === $b->order()) {
				return strcasecmp($a->title(), $b->title());
			}
			return $a->order() > $b->order() ? 1 : -1;
		});
		
		$data = [];
		foreach ($items as $page) {
			$data[] = $page->toArray();
		}
		return $data;
	}
}