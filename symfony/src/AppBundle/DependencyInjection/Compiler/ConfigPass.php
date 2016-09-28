<?php

namespace AppBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigPass implements CompilerPassInterface
{
	public function process( ContainerBuilder $container ) {

		// $container->getParameterBag();
		// $container->getServiceIds();

		// use default IS_AUTHENTICATED_FULLY permission by default
		$config = $container->getParameter('easyadmin.config');

		// update design menu
		foreach($config['design']['menu'] as $k => $v) {
			if (!isset($v['role'])) {
				$config['design']['menu'][$k]['role'] = 'IS_AUTHENTICATED_FULLY';
			}
		}

		// update entities
		foreach ($config['entities'] as $k => $v) {
			if (!isset($v['role'])) {
				$config['entities'][$k]['role'] = 'IS_AUTHENTICATED_FULLY';
			}
		}

		$container->setParameter('easyadmin.config', $config);

	}
	

}