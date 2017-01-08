<?php
namespace Bliss;

class ComponentCollection implements \ArrayAccess, \Iterator, \Countable
{
	/**
	 * @var \Bliss\Component[]
	 */
	private $items = [];
	
	/**
	 * Constructor
	 * 
	 * @param array $items
	 */
	public function __construct(array $items = [])
	{
		$this->items = $items;
	}
	
	/**
	 * Push an item onto the end of the collection
	 * 
	 * @param Component $item
	 */
	protected function push(Component $item)
	{
		$this->items[] = $item;
	}
	
	/**
	 * Add an into into the beginning of the collection
	 * 
	 * @param Component $item
	 */
	protected function unshift(Component $item)
	{
		array_unshift($this->items, $item);
	}
	
	/**
	 * Clear all items from the collection
	 * 
	 */
	public function clear()
	{
		$this->items = [];
	}
	
	/**
	 * Get all items in the collection
	 * 
	 * @return Component[]
	 */
	protected function all()
	{
		return $this->items;
	}
	
	/**
	 * Convert all items in the collection to an array
	 * 
	 * @return array
	 * @throws \UnexpectedValueException
	 */
	public function toArray()
	{
		$data = [];
		foreach ($this->items as $i => $item) {
			if (!($item instanceof Component)) {
				throw new \UnexpectedValueException("Item at index #{$i} is not an instance of \\Bliss\\Component");
			}
			$data[] = $item->toArray();
		}
		return $data;
	}
	
	# Implementation of \ArrayAccess
	public function offsetExists($offset) { return isset($this->items[$offset]); }
	public function offsetGet($offset) { return $this->items[$offset]; }
	public function offsetSet($offset, $value) { $this->items[$offset] = $value; }
	public function offsetUnset($offset) { unset($this->items[$offset]); }


	# Implementation of \Iterator
	public function current() { return current($this->items); }
	public function key() { return key($this->items); }
	public function next() { return next($this->items); }
	public function rewind() { return reset($this->items); }
	public function valid() { $key = key($this->items); return $key !== null && $key !== false; }

	# Implementation of \Countable
	public function count($mode = COUNT_NORMAL) { return count($this->items, $mode); }
}