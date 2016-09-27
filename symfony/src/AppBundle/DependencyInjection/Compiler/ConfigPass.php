<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigPass implements CompilerPassInterface
{
	public function process( ContainerBuilder $container ) {

		// print_r($container->getParameterBag());
		// print_r($container->getServiceIds());

//		$def = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
//		$def->addMethodCall('addResolveTargetEntity', array(
//			'Bpeh\NestablePageBundle\Model\PageBase', 'AppBundle\Entity\Page', array()
//		));
//		$def->addMethodCall('addResolveTargetEntity', array(
//			'Bpeh\NestablePageBundle\Model\PageMetaBase', 'AppBundle\Entity\PageMeta', array()
//		));


	}


}