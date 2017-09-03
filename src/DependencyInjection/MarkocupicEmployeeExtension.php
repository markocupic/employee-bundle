<?php

/**
 * SAC Pilatus web plugin
 * Copyright (c) 2008-2017 Marko Cupic
 * @package sacpilatus-bundle
 * @author Marko Cupic m.cupic@gmx.ch, 2017
 * @link    https://sac-kurse.kletterkader.com
 */

namespace Markocupic\EmployeeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;


/**
 * Class MarkocupicSacpilatusExtension
 * @package Markocupic\SacpilatusBundle\DependencyInjection
 * How to Load Service Configuration inside a Bundle
 * https://symfony.com/doc/current/bundles/extension.html
 */
class MarkocupicEmployeeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        // Load Sensitive outsourced data
        //require_once (__DIR__.'/../../../../../constants.php');

        $loader->load('parameters.yml');
        $loader->load('listener.yml');
        $loader->load('services.yml');
    }
}
