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

namespace ProophTest\Bundle\EventStore;

use PDO;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prooph\Bundle\EventStore\Exception\RuntimeException;
use Prooph\Bundle\EventStore\Factory\ProjectionManagerFactory;
use Prooph\Common\Messaging\MessageFactory;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\EventStoreDecorator;
use Prooph\EventStore\InMemoryEventStore;
use Prooph\EventStore\Pdo\MariaDbEventStore;
use Prooph\EventStore\Pdo\MySqlEventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy;
use Prooph\EventStore\Pdo\PostgresEventStore;
use Prooph\EventStore\Pdo\Projection\MariaDbProjectionManager;
use Prooph\EventStore\Pdo\Projection\MySqlProjectionManager;
use Prooph\EventStore\Pdo\Projection\PostgresProjectionManager;
use Prooph\EventStore\Projection\InMemoryProjectionManager;

class ProjectionManagerFactoryTest extends TestCase
{
    private ProjectionManagerFactory $sut;

    protected function setUp(): void
    {
        $this->sut = new ProjectionManagerFactory();
    }

    #[Test]
    public function it_should_not_accept_an_unknown_event_store(): void
    {
        $unknownEventStore = $this->createStub(EventStore::class);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'ProjectionManager for %s not implemented.',
            \get_class($unknownEventStore)
        ));

        $this->sut->createProjectionManager($unknownEventStore);
    }

    #[Test]
    #[DataProvider('provideEventStores')]
    public function it_should_create_a_projection_manager(
        string $expectedProjectionManagerType,
        string $eventStoreType,
        int $decoratorLevels
    ): void {
        $eventStore = $this->createAnEventStore($eventStoreType);

        for ($level = 0; $level < $decoratorLevels; $level++) {
            $eventStore = $this->createAnEventStoreDecorator($eventStore);
        }

        $connection = $this->createAPdoObject();
        $projectionManager = $this->sut->createProjectionManager($eventStore, $connection);

        self::assertInstanceOf($expectedProjectionManagerType, $projectionManager);
    }

    #[Test]
    public function it_cannot_create_a_pdo_manager_without_pdo_connection(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('PDO connection missing');

        $eventStore = $this->createAnEventStore(PostgresEventStore::class);
        $this->sut->createProjectionManager($eventStore);
    }

    public static function provideEventStores(): array
    {
        $eventStores = [
            'InMemoryEventStore' => [
                InMemoryProjectionManager::class,
                InMemoryEventStore::class,
                0,
            ],
            'PostgresEventStore' => [
                PostgresProjectionManager::class,
                PostgresEventStore::class,
                0,
            ],
            'MySqlEventStore' => [
                MySqlProjectionManager::class,
                MySqlEventStore::class,
                0,
            ],
            'Single level EventStoreDecorator' => [
                PostgresProjectionManager::class,
                PostgresEventStore::class,
                1,
            ],
            'Multi level EventStoreDecorator' => [
                PostgresProjectionManager::class,
                PostgresEventStore::class,
                2,
            ],
        ];

        if (\class_exists(MariaDbEventStore::class)) {
            $eventStores['MariaDbEventStore'] = [
                MariaDbProjectionManager::class,
                MariaDbEventStore::class,
                0,
            ];
        }

        return $eventStores;
    }

    private function createAnEventStore(string $type): EventStore
    {
        if (InMemoryEventStore::class === $type) {
            return new InMemoryEventStore();
        }

        return new $type(
            $this->createAMessageFactory(),
            $this->createAPdoObject(),
            $this->createAPersistenceStrategy()
        );
    }

    private function createAMessageFactory(): MessageFactory
    {
        return $this->createStub(MessageFactory::class);
    }

    private function createAPdoObject(): PDO
    {
        return $this->createStub(PDO::class);
    }

    private function createAPersistenceStrategy(): PersistenceStrategy
    {
        return $this->createStub(PersistenceStrategy::class);
    }

    private function createAnEventStoreDecorator(EventStore $decoratedEventStore): EventStoreDecorator
    {
        $eventStoreDecorator = $this->createStub(EventStoreDecorator::class);
        $eventStoreDecorator
            ->method('getInnerEventStore')
            ->willReturn($decoratedEventStore);

        return $eventStoreDecorator;
    }
}
