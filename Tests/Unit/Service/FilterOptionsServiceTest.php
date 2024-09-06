<?php

declare(strict_types=1);

namespace DWenzel\T3events\Tests\Unit\Service;

use DWenzel\T3events\Domain\Repository\AudienceRepository;
use DWenzel\T3events\Domain\Repository\CategoryRepository;
use DWenzel\T3events\Domain\Repository\ContentRepository;
use DWenzel\T3events\Domain\Repository\DemandedRepositoryInterface;
use DWenzel\T3events\Domain\Repository\EventRepository;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\PerformanceRepository;
use DWenzel\T3events\Domain\Repository\PerformanceStatusRepository;
use DWenzel\T3events\Domain\Repository\PersonRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Factory\DemandedRepositoryFactory;
use DWenzel\T3events\Service\FilterOptionsService;
use DWenzel\T3events\Service\TranslationService;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class FilterOptionsServiceTest extends UnitTestCase
{
    private FilterOptionsService $filterOptionsService;
    private TranslationService|MockObject $translateService;
    private DemandedRepositoryFactory|MockObject $demandedRepositoryFactory;

    public function setUp(): void
    {
        $this->translateService = $this->createMock(TranslationService::class);
        $this->demandedRepositoryFactory = $this->createMock(DemandedRepositoryFactory::class);

        $this->filterOptionsService = new FilterOptionsService(
            $this->translateService,
            $this->demandedRepositoryFactory
        );
    }

    /**
     * @test
     */
    public function findAllIsCalledAndAddedToFilterOptionsForKeyWithRepository(): void
    {
        $settings = [
            'category' => ''
        ];

        $resultFromRepository = $this->createMock(QueryResultInterface::class);

        $repository = $this->createMock(DemandedRepositoryInterface::class);
        $this->demandedRepositoryFactory
            ->expects($this->once())
            ->method('getDemandedRepositoryImplementationByKey')
            ->willReturn($repository);

        $repository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($resultFromRepository);

        $resultingFilterOptions = $this->filterOptionsService->getFilterOptions($settings);

        $this->assertArrayHasKey('categorys', $resultingFilterOptions);
        $this->assertEquals($resultingFilterOptions['categorys'], $resultFromRepository);
    }
}
