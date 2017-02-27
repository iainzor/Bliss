<?php
namespace Http\Format;

use Http\Application,
	Core\ModuleDefinition;

class FormatRegistry
{
	/**
	 * @var FormatInterface[]
	 */
	private $formats = [];
	
	/**
	 * @var FormatInterface
	 */
	private $defaultFormat;
	
	/**
	 * Constructor
	 * 
	 * @param \Http\Format\FormatInterface $defaultFormat
	 */
	public function __construct(FormatInterface $defaultFormat)
	{
		$this->defaultFormat = $defaultFormat;
	}
	
	/**
	 * Register a format.  This will overwrite any existing format that shares
	 * the same extension.
	 * 
	 * @param \Http\Response\FormatInterface $format
	 */
	public function register(FormatInterface $format)
	{
		$this->formats[$format->extension()] = $format;
	}
	
	/**
	 * Determine the format for a path and return a new FormatInterface instance.
	 * If a format cannot be found, the registry's default format will be returned.
	 * 
	 * @param string $path
	 * @return \Http\Format\FormattedPath
	 */
	public function determine(string $path) : FormatInterface
	{
		foreach ($this->formats as $format) {
			if (preg_match("/^(.*)\.". $format->extension() ."$/i", $path)) {
				return $format;
			}
		}
		return $this->defaultFormat;
	}
	
	/**
	 * Generate a new format registry using an application instance
	 * 
	 * @param Application $app
	 * @param FormatInterface $defaultFormat
	 * @return \Http\Response\FormatRegistry
	 */
	public static function generate(Application $app, FormatInterface $defaultFormat) : FormatRegistry
	{
		$registry = new FormatRegistry($defaultFormat);
		$app->moduleRegistry()->each(function(ModuleDefinition $moduleDef) use ($app, $registry) {
			$instance = $moduleDef->instance($app);
			if ($instance instanceof FormatProviderInterface) {
				$instance->registerResponseFormats($registry);
			}
		});
		return $registry;
	}
}
