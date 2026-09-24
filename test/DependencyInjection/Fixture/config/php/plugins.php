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

use ProophTest\Bundle\EventStore\DependencyInjection\Fixture\EventStore\BlackHole as BlackHoleEventStore;
use ProophTest\Bundle\EventStore\DependencyInjection\Fixture\Plugin\BlackHole as BlackHolePlugin;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('prooph_event_store', [
        'stores' => [
            'with_plugin_store' => [
                'event_store' => BlackHoleEventStore::class,
            ],
            'without_plugin_store' => [
                'event_store' => BlackHoleEventStore::class,
            ],
        ],
    ]);

    $container->services()
        ->set(BlackHolePlugin::class)
        ->tag('prooph_event_store.with_plugin_store.plugin')
        ->public();
};
