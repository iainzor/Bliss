<?php
namespace Database\Table;

interface ModelProviderInterface
{
	public function getModelClass() : string;
}