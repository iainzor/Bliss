<?php
namespace Forms\Input;

use Bliss\Component;

abstract class AbstractInput extends Component
{
	protected $type;
	
	abstract public function type();
}