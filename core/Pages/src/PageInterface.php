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
	 * @param string $target
	 * @return string
	 */
	public function target($target = null);
	
	/**
	 * @param boolean $flag
	 * @return boolean
	 */
	public function isVisible($flag = null);
	
	/**
	 * @param boolean $flag
	 * @return boolean
	 */
	public function isActive($flag = null);
	
	/**
	 * @param array $pages
	 * @param boolean $merge
	 * @return \Pages\PageInterface
	 */
	public function pages(array $pages = null, $merge = false);
}