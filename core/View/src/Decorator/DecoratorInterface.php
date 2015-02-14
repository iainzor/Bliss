<?php
namespace View\Decorator;

interface DecoratorInterface
{
	public function decorate($content, array $params = []);
}