<?php
namespace Polymer;

use View\Partial,
	UnifiedUI\Module as UI;

class Module extends \Bliss\Module\AbstractModule implements Partial\InjectorInterface
{
	public function initPartialInjector(\View\Partial\InjectableInterface $injectable) 
	{
		$injectable->inject(UI::AREA_HEAD, new Partial\Partial(
			$this->resolvePath("layouts/partials/polymer.phtml"), 
			$this->app
		));
	}
}