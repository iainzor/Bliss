<?php
namespace Cache\Driver;

interface StorageFactoryInterface
{
	/**
	 * @param array $options
	 * @return StorageInterface
	 */
	public function create(array $options);
}