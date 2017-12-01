<?php
namespace Database\Model;

interface ProtectedDataInterface
{
	public function getProtectedFields() : array;
}