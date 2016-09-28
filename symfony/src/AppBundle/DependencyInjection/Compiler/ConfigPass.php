<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigPass implements CompilerPassInterface
{
	public function process( ContainerBuilder $container ) {

		// print_r($container->getParameterBag());
		// print_r($container->getServiceIds());exit;
		// $def = $container->findDefinition('easyadmin.config.manager');

	}
	

}