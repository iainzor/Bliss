<?php
namespace Bliss;

class DateTime extends \DateTime
{
	/**
	 * Create a new DateTime instance from a UNIX timestamp
	 * 
	 * @param int $timestamp
	 * @return \Bliss\DateTime
	 */
	public static function fromTimestamp($timestamp)
	{
		$date = new self();
		$date->setTimestamp($timestamp);
		
		return $date;
	}
}