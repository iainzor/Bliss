<?php
namespace Response\Format;

interface ProviderInterface
{
	public function initResponseFormats(Registry $formats);
}