<?php
namespace Bliss;

abstract class AbstractIterator implements \Iterator
{
	/**
	 * @var array
	 */
	private $items = [];
	
	/**
	 * Add a single item to the iterator
	 * 
	 * @param mixed $item
	 * @param mixed $index Optional index for the item
	 */
	final protected function addItem($item, $index = null)
	{
		if ($index !== null) {
			$this->items[$index] = $item;
		} else {
			$this->items[] = $item;
		}
	}
	
	/**
	 * Remove a single item from the iterator's items
	 * 
	 * @param mixed $item
	 * @return boolean TRUE if successful, FALSE otherwise
	 */
	final protected function removeItem($item)
	{
		$key = array_search($item, $this->items);
		if ($key !== false) {
			unset($this->items[$key]);
			return true;
		}
		return false;
	}
	
	/**
	 * Clear the items in the iterator
	 */
	final protected function clearItems()
	{
		$this->items = [];
	}
	
	/**
	 * Get all items in the iterator
	 * 
	 * @return array
	 */
	final protected function allItems()
	{
		return $this->items;
	}

	public function current() { return current($this->items); }

	public function key() { return key($this->items); }

	public function next() { return next($this->items); }

	public function rewind() { return reset($this->items); }

	public function valid() { $key = key($this->items); return $key !== false && $key !== null; }

}