<?php
namespace Error;

interface ErrorHandlerInterface
{
	public function handleError($number, $string, $file, $line);
	
	public function handleException(\Error $e);
}