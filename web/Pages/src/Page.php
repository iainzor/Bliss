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
	protected $isVisible = true;
	
	/**
	 * @var boolean
	 */
	protected $isActive = false;
	
	/**
	 * @var \Pages\Container
	 */
	protected $pages;
	
	/**
	 * @var int
	 */
	protected $order = 100;
	
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
	public function isVisible($visible = null) 
	{
		if ($visible !== null) {
			$this->isVisible = (boolean) $visible;
		}
		return $this->isVisible;
	}
	
	/**
	 * @param type $flag
	 * @return type
	 */
	public function isActive($flag = null) 
	{
		if ($flag !== null) {
			$this->isActive = (boolean) $flag;
		}
		return $this->isActive;
	}
	
	/**
	 * Activate any pages that match the path
	 * 
	 * @param string $path
	 * @return Page[] All pages that were activated
	 */
	public function activate($path)
	{
		if ($this->path() === $path) {
			$this->isActive(true);
		}
		
		foreach ($this->pages() as $page) {
			$page->activate($path);
		}
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
	
	/**
	 * Get or set the ordering of the page
	 * 
	 * @param int $index
	 * @return int
	 */
	public function order($index = null)
	{
		return $this->getSet("order", $index);
	}
}