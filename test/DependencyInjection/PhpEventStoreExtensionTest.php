<?php

/**
 * This file is part of prooph/event-store-symfony-bundle.
 * (c) 2014-2024 Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2015-2024 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\Bundle\EventStore\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class PhpEventStoreExtensionTest extends AbstractEventStoreExtensionTestCase
{
    protected function loadFromFile(ContainerBuilder $container, $file)
    {
        $loadPhp = new PhpFileLoader($container, new FileLocator(__DIR__ . '/Fixture/config/php'));
        $loadPhp->load($file . '.php');
    }
}
