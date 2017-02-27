<?php
namespace Http\Format;

interface FormatProviderInterface
{
	public function registerResponseFormats(FormatRegistry $formatRegistry);
}
