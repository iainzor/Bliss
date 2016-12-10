<?php
namespace Forms\Input;

class SelectInput extends AbstractInput
{
	/**
	 * @var array
	 */
	protected $options = [];
	
	/**
	 * Constructor
	 * 
	 * @param SelectInputOption[] $options 
	 */
	public function __construct(array $options = [])
	{
		$this->options($options);
	}
	
	public function type() { return "select"; }
	
	/**
	 * Get or set the options available to the select input
	 * 
	 * @param SelectInputOption[] $options
	 * @return SelectInputOption[]
	 */
	public function options(array $options = null)
	{
		if ($options !== null) {
			$this->options = [];
			foreach ($options as $option) {
				$this->addOption($option);
			}
		}
		return $this->options;
	}
	
	/**
	 * Add an option to the select input
	 * 
	 * @param SelectInputOption $option
	 */
	public function addOption(SelectInputOption $option)
	{
		$this->options[] = $option;
	}
}