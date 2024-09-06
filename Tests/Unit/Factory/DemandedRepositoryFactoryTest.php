<?php

declare(strict_types=1);

namespace DWenzel\T3events\Tests\Unit\Factory;

use DWenzel\T3events\Domain\Repository\AudienceRepository;
use DWenzel\T3events\Domain\Repository\CategoryRepository;
use DWenzel\T3events\Domain\Repository\ContentRepository;
use DWenzel\T3events\Domain\Repository\EventRepository;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\PerformanceRepository;
use DWenzel\T3events\Domain\Repository\PerformanceStatusRepository;
use DWenzel\T3events\Domain\Repository\PersonRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Factory\DemandedRepositoryFactory;
use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class DemandedRepositoryFactoryTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $classes = [
            CategoryRepository::class => new CategoryRepository($objectManager),
            EventRepository::class => new EventRepository($objectManager),
            EventTypeRepository::class => new EventTypeRepository($objectManager),
            PersonRepository::class => new PersonRepository($objectManager),
            PerformanceStatusRepository::class  => new PerformanceStatusRepository($objectManager),
            PerformanceRepository::class  => new PerformanceRepository($objectManager),
            ContentRepository::class  => new ContentRepository($objectManager),
            VenueRepository::class  => new VenueRepository($objectManager),
            GenreRepository::class  => new GenreRepository($objectManager),
            AudienceRepository::class  => new AudienceRepository($objectManager),
        ];

        GeneralUtility::setContainer(
            new class ($classes) implements ContainerInterface
            {
                public function __construct(private readonly array $classes)
                {
                }

                public function get(string $id): ?object
                {
                    return $this->classes[$id] ?? null;
                }

                public function has(string $id): bool
                {
                    return isset($this->classes[$id]);
                }
            }
        );
    }

    /**
     * @test
     * @dataProvider repositoryProvider
     */
    public function factoryReturnsInstanceOfRequestedRepository(string $repository, string $expected): void
    {
        $factory = new DemandedRepositoryFactory();

        $repository = $factory->getDemandedRepositoryImplementationByKey($repository);

        $this->assertInstanceOf($expected, $repository);
    }

    public function repositoryProvider(): array
    {
        return [
            'category' => [
                'repository' => 'category',
                'expected' => CategoryRepository::class,
            ],
            'event' => [
                'repository' => 'event',
                'expected' => EventRepository::class,
            ],
            'event type' => [
                'repository' => 'eventType',
                'expected' => EventTypeRepository::class,
            ],
            'person' => [
                'repository' => 'person',
                'expected' => PersonRepository::class,
            ],
            'performance status' => [
                'repository' => 'performanceStatus',
                'expected' => PerformanceStatusRepository::class,
            ],
            'performance' => [
                'repository' => 'performance',
                'expected' => PerformanceRepository::class,
            ],
            'content' => [
                'repository' => 'content',
                'expected' => ContentRepository::class,
            ],
            'venue' => [
                'repository' => 'venue',
                'expected' => VenueRepository::class,
            ],
            'genre' => [
                'repository' => 'genre',
                'expected' => GenreRepository::class,
            ],
            'audience' => [
                'repository' => 'audience',
                'expected' => AudienceRepository::class,
            ],
        ];
    }
}
