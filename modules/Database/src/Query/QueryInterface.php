<?php
namespace Database\Query;

interface QueryInterface
{
	public function generateSQL();
}