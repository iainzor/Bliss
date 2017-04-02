<?php
namespace Http;

class JsonRequest
{
	/**
	 * @var array
	 */
	private $data = [];
	
	/**
	 * Constructor
	 * 
	 * @param \Http\Request $request
	 */
	public function __construct(Request $request)
	{
		$body = $request->body();
		$decoded = json_decode($body, true);
		
		if ($decoded) {
			$this->data = $decoded;
		}
	}
	
	/**
	 * Attempt to get a value from the JSON data.  If the name cannot be found,
	 * the $defaultValue will be returned
	 * 
	 * @param string $name
	 * @param mixed $defaultValue
	 * @return mixed
	 */
	public function get(string $name, $defaultValue = null)
	{
		return isset($this->data[$name])
			? $this->data[$name]
			: $defaultValue;
	}
}