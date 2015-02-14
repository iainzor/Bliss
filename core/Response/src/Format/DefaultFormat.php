<?php
namespace Response\Format;

class DefaultFormat extends AbstractFormat
{
	public function mime() { return "text/html"; }

	public function transform(\Response\Module $response) {}
}