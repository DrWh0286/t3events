<?php

namespace DWenzel\T3events\Tests\Unit\Controller\Backend;

use DWenzel\T3events\Controller\Backend\ScheduleController;
use DWenzel\T3events\Domain\Factory\Dto\PerformanceDemandFactory;
use DWenzel\T3events\Domain\Model\Dto\ModuleData;
use DWenzel\T3events\Domain\Model\Dto\PerformanceDemand;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Domain\Repository\CategoryRepository;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\PerformanceRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Event\ScheduleControllerListActionWasExecuted;
use DWenzel\T3events\Service\FilterOptionsService;
use DWenzel\T3events\Session\SessionInterface;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use DWenzel\T3events\Utility\SettingsUtility;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
class ScheduleControllerTest extends UnitTestCase
{
    /**
     * @var ScheduleController|\PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface
     */
    protected $subject;

    /**
     * @var ModuleData | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleData;

    /**
     * @var ViewInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    /**
     * @var PerformanceDemandFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $performanceDemandFactory;

    /**
     * @var array
     */
    protected $settings = [];
    /**
     * @var (PerformanceRepository&MockObject)|MockObject
     */
    private PerformanceRepository|MockObject $performanceRepository;
    /**
     * @var (GenreRepository&MockObject)|MockObject
     */
    private GenreRepository|MockObject $genreRepository;
    /**
     * @var (VenueRepository&MockObject)|MockObject
     */
    private MockObject|VenueRepository $venueRepository;
    /**
     * @var (EventTypeRepository&MockObject)|MockObject
     */
    private EventTypeRepository|MockObject $eventTypeRepository;
    /**
     * @var (SearchFactory&MockObject)|MockObject
     */
    private MockObject|SearchFactory $searchFactory;
    /**
     * @var MockObject|(ResponseFactoryInterface&MockObject)
     */
    private MockObject|ResponseFactoryInterface $responseFactory;
    /**
     * @var MockObject|(StreamFactoryInterface&MockObject)
     */
    private StreamFactoryInterface|MockObject $streamFactory;
    /**
     * @var (FilterOptionsService&MockObject)|MockObject
     */
    private FilterOptionsService|MockObject $filterOptionsService;
    /**
     * @var (EventDispatcherInterface&MockObject)|MockObject
     */
    private MockObject|EventDispatcherInterface $eventDispatcher;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->performanceRepository = $this->getMockBuilder(PerformanceRepository::class)->disableOriginalConstructor()->getMock();
        $this->genreRepository = $this->getMockBuilder(GenreRepository::class)->disableOriginalConstructor()->getMock();
        $this->venueRepository = $this->getMockBuilder(VenueRepository::class)->disableOriginalConstructor()->getMock();
        $this->eventTypeRepository = $this->getMockBuilder(EventTypeRepository::class)->disableOriginalConstructor()->getMock();
        $this->searchFactory = $this->getMockBuilder(SearchFactory::class)->disableOriginalConstructor()->getMock();
        $this->responseFactory = $this->getMockBuilder(ResponseFactoryInterface::class)->getMock();
        $this->streamFactory = $this->getMockBuilder(StreamFactoryInterface::class)->getMock();
        $this->filterOptionsService = $this->getMockBuilder(FilterOptionsService::class)->disableOriginalConstructor()->getMock();
        $this->categoryRepository = $this->getMockBuilder(CategoryRepository::class)->disableOriginalConstructor()->getMock();
        $this->session = $this->getMockBuilder(SessionInterface::class)->getMock();
        $this->settingsUtility = $this->getMockBuilder(SettingsUtility::class)->disableOriginalConstructor()->getMock();
        $this->performanceDemandFactory = $this->getMockBuilder(PerformanceDemandFactory::class)->disableOriginalConstructor()->getMock();

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->any())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject = new ScheduleController(
            $this->performanceRepository,
            $this->genreRepository,
            $this->venueRepository,
            $this->eventTypeRepository,
            $this->searchFactory,
            $this->filterOptionsService,
            $this->categoryRepository,
            $this->performanceDemandFactory,
            $this->session,
            $this->settingsUtility
        );

        $this->view = $this->getMockForAbstractClass(
            ViewInterface::class
        );
        $this->moduleData = $this->getMockBuilder(ModuleData::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $this->subject->setView($this->view);
        $this->subject->setModuleData($this->moduleData);

        $this->subject->overrideSettings($this->settings);
        $this->subject->injectResponseFactory($this->responseFactory);
        $this->subject->injectStreamFactory($this->streamFactory);
        $this->subject->injectEventDispatcher($this->eventDispatcher);
    }

    /**
     * @test
     */
    public function listActionOverwritesDemandObject(): void
    {
        $overwriteDemand = [
            'foo' => 'bar'
        ];
        $demand = $this->getMockBuilder(PerformanceDemand::class)->disableOriginalConstructor()->getMock();
        $this->performanceDemandFactory->expects($this->once())->method('createFromSettings')->willReturn($demand);
        $this->filterOptionsService->expects($this->once())->method('getFilterOptions')->willReturn([]);
        $this->moduleData->expects($this->once())->method('setOverwriteDemand')->with($overwriteDemand);
        $demand->expects($this->once())->method('overwriteDemandObject')->with($overwriteDemand, $this->settings);

        $templateVariables = [
            'performances' => $this->performanceRepository->findDemanded($demand),
            SI::OVERWRITE_DEMAND => $overwriteDemand,
            'demand' => $demand,
            SI::SETTINGS => $this->settings,
            'filterOptions' => [],
            SI::MODULE => SI::ROUTE_SCHEDULE_MODULE
        ];

        $event = new ScheduleControllerListActionWasExecuted($templateVariables);
        $this->eventDispatcher->expects($this->once())->method('dispatch')->with($event)->willReturn($event);

        $this->view->expects($this->once())
            ->method('assignMultiple')->with($templateVariables);

        $this->subject->listAction($overwriteDemand);
    }

    /**
     * @test
     */
    public function listActionAssignsVariablesToView(): void
    {
        $demand = $this->getMockBuilder(PerformanceDemand::class)->disableOriginalConstructor()->getMock();
        $this->performanceDemandFactory->expects($this->once())->method('createFromSettings')->willReturn($demand);
        $this->filterOptionsService->expects($this->once())->method('getFilterOptions')->willReturn([]);
        $this->moduleData->expects($this->once())->method('getOverwriteDemand')->willReturn(['foo' => 'bar']);
        $demand->expects($this->once())->method('overwriteDemandObject')->with(['foo' => 'bar'], $this->settings);

        $templateVariables = [
            'performances' => $this->performanceRepository->findDemanded($demand),
            SI::OVERWRITE_DEMAND => ['foo' => 'bar'],
            'demand' => $demand,
            SI::SETTINGS => $this->settings,
            'filterOptions' => [],
            SI::MODULE => SI::ROUTE_SCHEDULE_MODULE
        ];

        $event = new ScheduleControllerListActionWasExecuted($templateVariables);
        $this->eventDispatcher->expects($this->once())->method('dispatch')->with($event)->willReturn($event);

        $this->view->expects($this->once())
            ->method('assignMultiple')->with($templateVariables);

        $this->subject->listAction();
    }
}
