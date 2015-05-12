<?php
namespace Pages;

use Bliss\Component;

class Page extends Component implements PageInterface
{
	/**
	 * @var string
	 */
	protected $id;
	
	/**
	 * @var string
	 */
	protected $path;
	
	/**
	 * @var string
	 */
	protected $title;
	
	/**
	 * @var string
	 */
	protected $target;
	
	/**
	 * @var boolean
	 */
	protected $visible = true;
	
	/**
	 * @var \Pages\Container
	 */
	protected $pages;
	
	/**
	 * Constructor
	 */
	public function __construct() 
	{
		$this->pages = new Container();
	}
	
	/**
	 * Get or set the page's ID
	 * 
	 * @param string $id
	 * @return string
	 */
	public function id($id = null)
	{
		if ($id !== null) {
			$this->id = $id;
		}
		
		if ($this->id === null) {
			if ($this->path && $this->title) {
				$this->id = substr(md5($this->path . $this->title), 0, 10);
			} else {
				$this->id = substr(md5(uniqid()), 0, 10);
			}
		}
		
		return $this->id;
	}
	
	/**
	 * Get or set the page's path
	 * 
	 * @param string $path
	 * @return string
	 */
	public function path($path = null) 
	{
		if ($path !== null) {
			$this->path = $path;
		}
		return $this->path;
	}

	/**
	 * Get or set the title of the page
	 * 
	 * @param string $title
	 * @return string
	 */
	public function title($title = null) 
	{
		if ($title !== null) {
			$this->title = $title;
		}
		return $this->title;
	}
	
	/**
	 * Get or set the page's target
	 * 
	 * @param string $target
	 * @return string
	 */
	public function target($target = null) 
	{
		return $this->getSet("target", $target);
	}
	
	/**
	 * @param boolean $visible
	 * @return boolean
	 */
	public function visible($visible = null) {
		if ($visible !== null) {
			$this->visible = (boolean) $visible;
		}
		return $this->visible;
	}
	
	/**
	 * Get or set the child pages of this page
	 * 
	 * @param array $pages
	 * @param boolean $merge
	 * @return \Pages\Container
	 */
	public function pages(array $pages = null, $merge = false)
	{
		if (!isset($this->pages)) {
			$this->pages = new Container();
		}
		if ($pages !== null) {
			if ($merge === false) {
				$this->pages->clear();
			}
			
			foreach ($pages as $data) {
				$page = Page::factory($data);
				$this->pages->add($page);
			}
		}
		return $this->pages;
	}
}