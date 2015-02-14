<?php
namespace View\Partial;

trait InjectableTrait
{
	/**
	 * @var \View\Partial[]
	 */
	private $areas = [];
	
	/**
	 * Add a view partial to an injectable area
	 * 
	 * @param string $area The name of the area
	 * @param \View\Partial The view partial to render
	 * @param int $order The order in which the partial should be rendered
	 */
	public function inject($area, Partial $partial, $order = 0)
	{
		if (!isset($this->areas[$area])) {
			$this->areas[$area] = [];
		}
		
		$this->areas[$area][] = [
			"partial" => $partial,
			"order" => (int) $order
		];
	}
	
	/**
	 * Render and return the contents of each partial for the area specified
	 * 
	 * @param string $area The name of the area to render
	 * @return string
	 */
	public function renderInjectables($area)
	{
		$contents = "";
		$partials = isset($this->areas[$area]) ? $this->areas[$area] : [];
		
		usort($partials, function($a, $b) {
			if ($a["order"] === $b["order"]) {
				return 0;
			}
			return $a["order"] < $b["order"] ? -1 : 1;
		});
		
		foreach ($partials as $data) {
			$partial = $data["partial"];
			$partialContents = $partial->render();
			$contents .= "{$partialContents}\n";
		}
		
		return $contents;
	}
}