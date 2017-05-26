<?php
namespace Acl;

use Core\AbstractApplication,
	Core\ConfigurableModuleInterface,
	Core\ModuleConfig,
	Core\ModuleDefinition;

class Module implements ConfigurableModuleInterface
{
	public function configure(AbstractApplication $app, ModuleConfig $config) 
	{
		$app->di()->register(Acl::class, function() use ($app, $config) {
			$acl = new Acl();
			
			$app->moduleRegistry()->each(function(ModuleDefinition $moduleDef) use ($app, $acl) {
				$module = $moduleDef->instance($app);
				if ($module instanceof RoleProviderInterface) {
					$module->registerAclRoles($acl);
				}
			});
			
			return $acl;
		});
	}
}