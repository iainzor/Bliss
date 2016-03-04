<?php
namespace Cache\Driver;

use Bliss\App;

interface StorageFactoryInterface
{
	/**
	 * @param App\Container $app
	 * @param array $options
	 * @return StorageInterface
	 */
	public function create(App\Container $app, array $options);
}