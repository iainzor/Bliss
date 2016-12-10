<?php
namespace Forms\Input;

use Bliss\Component;

class SelectInputOption extends Component
{
	/**
	 * @var mixed
	 */
	protected $value;
	
	/**
	 * @var string
	 */
	protected $label;
	
	/**
	 * Constructor
	 * 
	 * @param mixed $value
	 * @param string $label
	 */
	public function __construct($value, $label = null)
	{
		$this->value($value);
		$this->label($label);
	}
	
	/**
	 * Get or set the option's value
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	public function value($value = null)
	{
		return $this->getSet("value", $value);
	}
	
	/**
	 * Get or set the option's label.  If no label is provided, the option's
	 * value will be used.
	 * 
	 * @param string $label
	 * @return string
	 */
	public function label($label = null)
	{
		if ($label !== null) {
			$this->label = $label;
		}
		if (!$this->label) {
			$this->label = $this->value();
		}
		
		return $this->label;
	}
}