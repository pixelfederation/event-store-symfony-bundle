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

use Prooph\EventStore\InMemoryEventStore;
use ProophTest\Bundle\EventStore\DependencyInjection\Fixture\Projection\BlackHoleReadModelProjection;
use ProophTest\Bundle\EventStore\DependencyInjection\Fixture\Projection\TodoProjection;
use ProophTest\Bundle\EventStore\DependencyInjection\Fixture\Projection\TodoReadModel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('prooph_event_store', [
        'stores' => [
            'main_store' => [
                'event_store' => InMemoryEventStore::class,
            ],
        ],
        'projection_managers' => [
            'main_projection_manager' => [
                'event_store' => '@prooph_event_store.main_store',
                'projections' => [
                    'todo_projection' => [
                        'read_model' => TodoReadModel::class,
                        'projection' => TodoProjection::class,
                        'options' => [
                            'cache_size' => 1000,
                            'sleep' => 100000,
                            'persist_block_size' => 1000,
                            'lock_timeout_ms' => 1000,
                            'trigger_pcntl_dispatch' => false,
                            'update_lock_threshold' => 0,
                            'gap_detection' => [
                                'retry_config' => [0, 5, 10, 15, 30, 60, 90],
                                'detection_window' => 'P1M',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    $services = $container->services();

    $services->set(InMemoryEventStore::class, InMemoryEventStore::class);

    $services->set(BlackHoleReadModelProjection::class)
        ->tag('prooph_event_store.projection', [
            'projection_name' => 'black_hole_projection',
            'projection_manager' => 'main_projection_manager',
            'read_model' => TodoProjection::class,
        ]);

    $services->set(TodoReadModel::class);
    $services->set(TodoProjection::class);

    $services->alias(
        'test.prooph_event_store.projection_manager_for_projections_locator',
        'prooph_event_store.projection_manager_for_projections_locator'
    )->public();

    $services->alias(
        'test.prooph_event_store.projections_locator',
        'prooph_event_store.projections_locator'
    )->public();

    $services->alias(
        'test.prooph_event_store.projection_options_locator',
        'prooph_event_store.projection_options_locator'
    )->public();

    $services->alias(
        'test.prooph_event_store.projection_read_models_locator',
        'prooph_event_store.projection_read_models_locator'
    )->public();
};
