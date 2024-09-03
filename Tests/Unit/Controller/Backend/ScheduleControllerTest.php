<?php

namespace DWenzel\T3events\Tests\Unit\Controller\Backend;

use DWenzel\T3events\Controller\Backend\ScheduleController;
use DWenzel\T3events\Domain\Factory\Dto\PerformanceDemandFactory;
use DWenzel\T3events\Domain\Model\Dto\DemandInterface;
use DWenzel\T3events\Domain\Model\Dto\ModuleData;
use DWenzel\T3events\Domain\Model\Dto\PerformanceDemand;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\PerformanceRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Core\Http\ResponseFactory;
use TYPO3\CMS\Core\Http\StreamFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

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

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->any())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject = $this->getAccessibleMock(
            ScheduleController::class,
            [
            'createDemandFromSettings',
            'emitSignal',
            'getFilterOptions',
            'overwriteDemandObject'
            ],
            [
                $this->performanceRepository,
                $this->genreRepository,
                $this->venueRepository,
                $this->eventTypeRepository,
                $this->searchFactory,
            ]
        );
        $this->view = $this->getMockForAbstractClass(
            ViewInterface::class
        );
        $this->moduleData = $this->getMockBuilder(ModuleData::class)->getMock();
        /** @var PerformanceRepository|\PHPUnit_Framework_MockObject_MockObject $mockPerformanceRepository */
        $mockPerformanceRepository = $this->getMockBuilder(PerformanceRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->subject->_set('view', $this->view);
        $this->subject->_set('moduleData', $this->moduleData);


        /** @var PerformanceDemandFactory|\PHPUnit_Framework_MockObject_MockObject performanceDemandFactory */
        $this->performanceDemandFactory = $this->getMockBuilder(PerformanceDemandFactory::class)
            ->setMethods(['createFromSettings'])->getMock();
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $mockDemand */
        $mockDemand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $this->performanceDemandFactory->method('createFromSettings')->will($this->returnValue($mockDemand));
        $this->subject->injectPerformanceDemandFactory($this->performanceDemandFactory);
        $this->subject->_set(SI::SETTINGS, $this->settings);
        $this->subject->injectResponseFactory($this->responseFactory);
        $this->subject->injectStreamFactory($this->streamFactory);
    }

    /**
     * @test
     */
    public function listActionCreatesDemandFromSettings(): void
    {
        $settings = [
            'filter' => []
        ];

        $this->subject->_set(
            SI::SETTINGS,
            $settings
        );

        $this->performanceDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->with($settings);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function listActionGetsOverwriteDemandFromModuleData(): void
    {
        $this->mockCreateDemandFromSettings();
        $this->moduleData->expects($this->once())
            ->method('getOverwriteDemand');
        $this->subject->listAction();
    }

    /**
     * @return DemandInterface |\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockCreateDemandFromSettings()
    {
        $mockDemand = $this->getMockBuilder(PerformanceDemand::class)->getMock();

        $this->performanceDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->will($this->returnValue($mockDemand));

        return $mockDemand;
    }

    /**
     * @test
     */
    public function listActionSetsOverwriteDemandOnModuleData(): void
    {
        $overwriteDemand = ['foo'];
        $this->mockCreateDemandFromSettings();
        $this->moduleData->expects($this->once())
            ->method('setOverwriteDemand')
            ->with($overwriteDemand);

        $this->subject->listAction($overwriteDemand);
    }

    /**
     * @test
     */
    public function listActionOverwritesDemandObject(): void
    {
        $mockDemandObject = $this->mockCreateDemandFromSettings();
        $overwriteDemand = ['foo'];
        $this->subject->expects($this->once())
            ->method('overwriteDemandObject')
            ->with($mockDemandObject, $overwriteDemand);

        $this->subject->listAction($overwriteDemand);
    }

    /**
     * @test
     */
    public function listActionEmitsSignal(): void
    {
        $this->mockCreateDemandFromSettings();

        // can not match expectedTemplateVariables - always got an array containing all arguments as third argument.
        $this->subject->expects($this->once())
            ->method('emitSignal');

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function listActionAssignsVariablesToView(): void
    {
        // can not match expectedTemplateVariables as soon as method 'emitSignal' is called.
        $this->view->expects($this->once())
            ->method('assignMultiple');
        $this->subject->listAction();
    }
}
