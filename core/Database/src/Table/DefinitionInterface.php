<?php
namespace Database\Table;

interface DefinitionInterface
{
	/**
	 * @param \Database\Table\Definition $definition
	 */
	public function initTableDefinition(Definition $definition);
}