<?php
namespace Pages;

interface PageInterface
{
	/**
	 * @param string $id
	 * @return string
	 */
	public function id($id = null);
	
	/**
	 * @param string $title
	 * @return string
	 */
	public function title($title = null);
	
	/**
	 * @param string $path
	 * @return string
	 */
	public function path($path = null);
	
	/**
	 * @param boolean $flag
	 * @return boolean
	 */
	public function visible($flag = null);
	
	/**
	 * @param array $pages
	 * @param boolean $merge
	 * @return \Pages\PageInterface
	 */
	public function pages(array $pages = null, $merge = false);
	
	/**
	 * @param array $resources
	 * @return array 
	 */
	public function resources(array $resources = null);
}