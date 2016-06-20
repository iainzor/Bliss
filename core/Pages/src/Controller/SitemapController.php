<?php
namespace Pages\Controller;

use Bliss\Controller\AbstractController,
	Pages\PageInterface,
	SimpleXMLElement;

class SitemapController extends AbstractController
{
	public function renderAction(\Pages\Module $pages, \Request\Module $request)
	{
		$scheme = filter_input(INPUT_SERVER, "HTTPS") ? "https://" : "http://";
		$host = filter_input(INPUT_SERVER, "HTTP_HOST");
		$baseUrl = $scheme . $host . $request->baseUrl();
		$xml = new SimpleXMLElement(
			'<?xml version="1.0" encoding="UTF-8"?>' .
			'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>'
		);
		
		foreach ($pages->pages() as $page) {
			$this->_addPage($page, $xml, $baseUrl);
		}
		
		return $xml->asXML();
	}
	
	private $urls = [];
	
	private function _addPage(PageInterface $page, SimpleXMLElement $parent, $baseUrl)
	{
		$url = $baseUrl . preg_replace("/^\/?(.*)$/i", "\\1", $page->path());
		
		if (!isset($this->urls[$url])) {
			$el = $parent->addChild("url");
			$el->addChild("loc", $url);
			
			$this->urls[$url] = true;
		}
		
		foreach ($page->pages() as $child) {
			$this->_addPage($child, $parent, $baseUrl);
		}
	}
}