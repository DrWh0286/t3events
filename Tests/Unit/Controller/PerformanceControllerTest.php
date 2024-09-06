<?php

namespace DWenzel\T3events\Tests\Unit\Controller;

/***************************************************************
 *  Copyright notice
 *  (c) 2012 Dirk Wenzel <wenzel@webfox01.de>, Agentur Webfox
 *            Michael Kasten <kasten@webfox01.de>, Agentur Webfox
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use DWenzel\T3events\Controller\PerformanceController;
use DWenzel\T3events\Domain\Factory\Dto\PerformanceDemandFactory;
use DWenzel\T3events\Domain\Model\Dto\DemandInterface;
use DWenzel\T3events\Domain\Model\Dto\PerformanceDemand;
use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Domain\Model\Performance;
use DWenzel\T3events\Domain\Repository\CategoryRepository;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\PerformanceRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Event\PerformanceControllerListActionWasExecuted;
use DWenzel\T3events\Event\PerformanceControllerQuickMenuActionWasExecuted;
use DWenzel\T3events\Event\PerformanceControllerShowActionWasExecuted;
use DWenzel\T3events\Service\FilterOptionsService;
use DWenzel\T3events\Session\SessionInterface;
use DWenzel\T3events\Tests\Unit\Object\MockObjectManagerTrait;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use DWenzel\T3events\Utility\SettingsUtility;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Fluid\View\TemplateView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for class \DWenzel\T3events\Controller\PerformanceController.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package TYPO3
 * @subpackage Events
 * @author Dirk Wenzel <wenzel@webfox01.de>
 * @author Michael Kasten <kasten@webfox01.de>
 * @coversDefaultClass \DWenzel\T3events\Controller\PerformanceController
 */
class PerformanceControllerTest extends UnitTestCase
{
    use MockObjectManagerTrait;

    /**
     * @var PerformanceController|\PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface
     */
    protected $subject;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    protected PerformanceDemandFactory|MockObject $performanceDemandFactory;

    protected PerformanceRepository|MockObject $performanceRepository;

    /**
     * @var ContentObjectRenderer|MockObject
     */
    protected $contentObject;

    /**
     * @var SessionInterface |MockObject
     */
    protected $session;
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
     * @var (FilterOptionsService&MockObject)|MockObject
     */
    private FilterOptionsService|MockObject $filterOptionsService;
    /**
     * @var (CategoryRepository&MockObject)|MockObject
     */
    private CategoryRepository|MockObject $categoryRepository;
    /**
     * @var (SettingsUtility&MockObject)|MockObject
     */
    private SettingsUtility|MockObject $settingsUtility;
    /**
     * @var MockObject|(EventDispatcherInterface&MockObject)
     */
    private MockObject|EventDispatcherInterface $eventDispatcher;
    /**
     * @var MockObject|(ConfigurationManagerInterface&MockObject)
     */
    private MockObject|ConfigurationManagerInterface $configurationManager;
    /**
     * @var MockObject|(Request&MockObject)
     */
    private MockObject|Request $request;

    protected function setUp(): void
    {
        $this->performanceRepository = $this->getMockBuilder(PerformanceRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var GenreRepository $repository */
        $this->genreRepository = $this->getMockBuilder(GenreRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->venueRepository = $this->getMockBuilder(VenueRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->eventTypeRepository = $this->getMockBuilder(EventTypeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->searchFactory = $this->getMockBuilder(SearchFactory::class)->getMock();

        $this->filterOptionsService = $this->getMockBuilder(FilterOptionsService::class)->disableOriginalConstructor()->getMock();
        $this->categoryRepository = $this->getMockBuilder(CategoryRepository::class)->disableOriginalConstructor()->getMock();
        $this->performanceDemandFactory = $this->getMockBuilder(PerformanceDemandFactory::class)->disableOriginalConstructor()->getMock();
        $this->session = $this->getMockBuilder(SessionInterface::class)->getMock();
        $this->settingsUtility = $this->getMockBuilder(SettingsUtility::class)->disableOriginalConstructor()->getMock();

        $this->subject = new PerformanceController(
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

        $mockResult = $this->getMockBuilder(QueryResultInterface::class)->getMock();

        $this->performanceRepository->method('findDemanded')->will(self::returnValue($mockResult));

        $this->view = $this->getMockBuilder(TemplateView::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contentObject = $this->getMockBuilder(ContentObjectRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockDispatcher = $this->getMockBuilder(Dispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->getMockBuilder(Request::class)->getMock();
        $this->configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
            ->getMockForAbstractClass();
        $this->configurationManager->expects($this->any())->method('getContentObject')->willReturn($this->contentObject);
        $this->configurationManager->expects($this->any())->method('getConfiguration')->willReturn($this->settings);

        $this->responseFactory = $this->getMockBuilder(ResponseFactoryInterface::class)->getMock();
        $this->streamFactory = $this->getMockBuilder(StreamFactoryInterface::class)->getMock();
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();

        $this->subject->setView($this->view);
        $this->subject->injectSignalSlotDispatcher($mockDispatcher);
        $this->subject->setRequest($this->request);
        $this->subject->injectConfigurationManager($this->configurationManager);
        $this->subject->injectResponseFactory($this->responseFactory);
        $this->subject->injectStreamFactory($this->streamFactory);
        $this->subject->injectEventDispatcher($this->eventDispatcher);
    }

    /**
     * @test
     */
    public function initializeActionsSetsContentObject(): void
    {
        $this->subject->overrideSettings([]);

        $this->configurationManager->expects(self::once())
            ->method('getContentObject');

        $this->subject->initializeAction();
    }

    protected function mockSettingsUtility()
    {
        /** @var SettingsUtility|\PHPUnit_Framework_MockObject_MockObject $mockSettingsUtility */
        $mockSettingsUtility = $this->getMockBuilder(SettingsUtility::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->injectSettingsUtility($mockSettingsUtility);
        $mockSettingsUtility->expects($this->any())
            ->method('getControllerKey')
            ->will(self::returnValue('performance'));
    }

    /**
     * @test
     */
    public function initializeActionSetsOverwriteDemandInSession(): void
    {
        $this->subject->overrideSettings([]);

        $this->settingsUtility->expects(self::once())->method('mergeSettings')->willReturn([]);

        $overwriteDemand = ['foo'];

        $this->request->expects(self::any())
            ->method('hasArgument')
            ->will(self::returnValue(true));
        $this->request->expects(self::once())
            ->method('getArgument')
            ->will(self::returnValue($overwriteDemand));

        $this->session->expects(self::once())
            ->method('set')
            ->with('tx_t3events_overwriteDemand', serialize($overwriteDemand));

        $this->subject->initializeAction();
    }

    /**
     * @test
     * @covers ::createDemandFromSettings
     */
    public function createDemandFromSettingsReturnsDemandObject(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandFactory');
        $this->assertInstanceOf(
            PerformanceDemand::class,
            $this->subject->_call('createDemandFromSettings', $this->settings)
        );
    }

    /**
     * @test
     * @covers ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsGenres(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)
            ->getMock();
        $overwriteDemand = array(
            SI::LEGACY_KEY_GENRE => '1,2,3'
        );

        $demand->expects(self::once())->method('setGenres')
            ->with('1,2,3');

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     * @covers ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsVenues(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        $demand = $this->getMockBuilder(PerformanceDemand::class)
            ->getMock();
        $overwriteDemand = ['venue' => '1,2,3'];

        $demand->expects(self::once())->method('setVenues')
            ->with('1,2,3');

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     * @covers ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsEventType(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $overwriteDemand = ['eventType' => '1,2,3'];

        $demand->expects(self::once())->method('setEventTypes')
            ->with('1,2,3');

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     * @covers ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsEventLocations(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $overwriteDemand = ['eventLocation' => '1,2,3'];

        $demand->expects(self::once())->method('setEventLocations')
            ->with('1,2,3');

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     * @covers ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsCategoryConjunction(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $overwriteDemand = ['categoryConjunction' => 'asc'];

        $demand->expects(self::once())->method('setCategoryConjunction')
            ->with('asc');

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsSearch(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        $fieldNames = 'foo,bar';
        $search = 'baz';
        $settings = [
            'search' => [
                'fields' => $fieldNames
            ]
        ];
        $this->subject->_set(SI::SETTINGS, $settings);

        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $mockSearchObject = $this->getMockBuilder(Search::class)->getMock();
        $overwriteDemand = [
            'search' => [
                'subject' => $search
            ]
        ];

        $this->subject->expects(self::once())
            ->method('createSearchObject')
            ->with($overwriteDemand['search'], $settings['search'])
            ->will(self::returnValue($mockSearchObject));

        $demand->expects(self::once())->method('setSearch')
            ->with($mockSearchObject);

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     * @covers ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsSortBy(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $overwriteDemand = array(
            'sortBy' => 'foo'
        );

        $demand->expects(self::once())->method('setSortBy')
            ->with('foo');

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     * @covers ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsSortOrder(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $overwriteDemand = array(
            'sortBy' => 'foo',
            SI::SORT_DIRECTION => 'bar'
        );

        $demand->expects(self::once())->method('setOrder')
            ->with('foo|bar');

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     * @covers ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsDefaultSortDirectionAscending(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $overwriteDemand = array(
            SI::SORT_DIRECTION => 'foo'
        );

        $demand->expects(self::once())->method('setSortDirection')
            ->with('asc');

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     * @covers ::overwriteDemandObject
     */
    public function overwriteDemandObjectSetsSortDirectionDescending(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $overwriteDemand = array(
            SI::SORT_DIRECTION => 'desc'
        );

        $demand->expects(self::once())->method('setSortDirection')
            ->with('desc');

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     */
    public function overwriteDemandObjectSetsStartDate(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $dateString = '2012-10-15';
        $overwriteDemand = [
            SI::START_DATE => $dateString
        ];
        $defaultTimeZone = new \DateTimeZone(date_default_timezone_get());
        $expectedDateTimeObject = new \DateTime($dateString, $defaultTimeZone);
        $demand->expects(self::once())
            ->method('setStartDate')
            ->with($expectedDateTimeObject);

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     */
    public function overwriteDemandObjectSetsEndDate(): void
    {
        $this->markTestSkipped('this test needs to be moved to PerformanceDemandTest');
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $demand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $dateString = '2012-10-15';
        $overwriteDemand = [
            SI::END_DATE => $dateString
        ];
        $defaultTimeZone = new \DateTimeZone(date_default_timezone_get());
        $expectedDateTimeObject = new \DateTime($dateString, $defaultTimeZone);
        $demand->expects(self::once())
            ->method('setEndDate')
            ->with($expectedDateTimeObject);

        $demand->overwriteDemandObject($overwriteDemand, []);
    }

    /**
     * @test
     * @covers ::listAction
     */
    public function listActionCallsOverwriteDemandObject(): void
    {
        $settings = array('foo');
        $this->subject->overrideSettings($settings);
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $mockDemand = $this->getMockBuilder(PerformanceDemand::class)->getMock();

        $this->settingsUtility->expects(self::once())->method('mergeSettings')->willReturn($settings);

        $this->performanceDemandFactory->expects(self::once())
            ->method('createFromSettings')
            ->will(self::returnValue($mockDemand));

        $mockDemand->expects(self::once())
            ->method('overwriteDemandObject')
            ->with(['demand' => 'dummy'], $settings);

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PerformanceControllerListActionWasExecuted::class))
            ->willReturn($event = new PerformanceControllerListActionWasExecuted(['foo', 'bar']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->initializeAction();
        $this->subject->listAction([]);
    }

    /**
     * @test
     * @covers ::listAction
     */
    public function listActionCallsFindDemanded(): void
    {
        $settings = array('foo');
        $this->subject->overrideSettings($settings);

        /** @var PerformanceDemand|MockObject $demand */
        $performanceDemand = $this->getMockBuilder(PerformanceDemand::class)->disableOriginalConstructor()->getMock();

        $this->contentObject->data = [];

        $this->settingsUtility->expects(self::once())->method('mergeSettings')->willReturn($settings);

        $overrideDemand = serialize(['demand' => 'dummy']);
        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn($overrideDemand);

        $this->performanceDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->willReturn($performanceDemand);

        $performanceDemand->expects($this->once())
            ->method('overwriteDemandObject')
            ->with(unserialize($overrideDemand), $settings);

        $this->performanceRepository->expects($this->once())
            ->method('findDemanded')
            ->with($performanceDemand);

        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PerformanceControllerListActionWasExecuted::class))
            ->willReturn($event = new PerformanceControllerListActionWasExecuted(['foo', 'bar']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->view->expects($this->once())->method('assignMultiple')->with(['foo', 'bar']);

        $this->subject->initializeAction();
        $this->subject->listAction([]);
    }

    /**
     * @test
     */
    public function showActionAssignsVariables(): void
    {
        $settings = ['foo'];
        $performance = new Performance();
        $templateVariables = [
            SI::SETTINGS => $settings,
            'performance' => $performance
        ];
        $this->configurationManager->expects($this->once())->method('getConfiguration')->willReturn($settings);
        $this->subject->injectConfigurationManager($this->configurationManager);

        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PerformanceControllerShowActionWasExecuted::class))
            ->willReturn($event = new PerformanceControllerShowActionWasExecuted($templateVariables));

        $this->view->expects(self::once())
            ->method('assignMultiple')
            ->with($event->getTemplateVariables());

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->initializeAction();
        $this->subject->showAction($performance);
    }

    /**
     * @test
     */
    public function quickMenuActionGetsOverwriteDemandFromSession(): void
    {
        $this->injectMockRepositories(['findMultipleByUid', 'findAll']);

        $eventDispatcher = new class implements EventDispatcherInterface
        {
            public function dispatch(object $event): object
            {
                return $event;
            }
        };

        $this->subject->injectEventDispatcher($eventDispatcher);

        $overwriteDemand = ['demand' => 'dummy'];
        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize($overwriteDemand));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $filterOptions = [];
        $this->filterOptionsService->expects($this->once())->method('getFilterOptions')->willReturn($filterOptions);

        $templateVariables = [
            'filterOptions' => $filterOptions,
            SI::GENRES => null,
            SI::VENUES => null,
            SI::EVENT_TYPES => null,
            SI::SETTINGS => $this->settings,
            SI::OVERWRITE_DEMAND => $overwriteDemand
        ];

        $this->view->expects(self::once())->method('assignMultiple')->with($templateVariables);

        $this->subject->quickMenuAction();
    }

    /**
     * @param array $methodsToStub
     */
    protected function injectMockRepositories(array $methodsToStub)
    {
        $repositoryClasses = [
            'genreRepository' => GenreRepository::class,
            'venueRepository' => VenueRepository::class,
            'eventTypeRepository' => EventTypeRepository::class,
        ];
        foreach ($repositoryClasses as $propertyName => $className) {
            $mock = $this->getAccessibleMock($className, $methodsToStub, [], '', false, true, false);
            $mock->_set($propertyName, $this->subject);
        }
    }

    /**
     * @test
     */
    public function quickMenuActionGetsGenresFromSettings(): void
    {
        $settings = [SI::GENRES => '1,2,3'];
        $this->configurationManager->expects($this->once())->method('getConfiguration')->willReturn($settings);
        $this->subject->injectConfigurationManager($this->configurationManager);

        $filterOptions = [];
        $this->filterOptionsService->expects($this->once())->method('getFilterOptions')->willReturn($filterOptions);

        $variables = [
            'foo' => 'bar'
        ];
        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PerformanceControllerQuickMenuActionWasExecuted::class))
            ->willReturn($event = new PerformanceControllerQuickMenuActionWasExecuted($variables));

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->view->expects($this->once())->method('assignMultiple')->with($event->getTemplateVariables());

        $this->subject->quickMenuAction();

        /** @var ViewInterface $view */
        $view = $this->subject->getView();
    }

    /**
     * @test
     */
    public function quickMenuActionGetsVenuesFromSettings(): void
    {
        $settings = [SI::VENUES => '1,2,3'];
        $this->configurationManager->expects($this->once())->method('getConfiguration')->willReturn($settings);
        $this->subject->injectConfigurationManager($this->configurationManager);

        $filterOptions = [];
        $this->filterOptionsService->expects($this->once())->method('getFilterOptions')->willReturn($filterOptions);

        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PerformanceControllerQuickMenuActionWasExecuted::class))
            ->willReturn($event = new PerformanceControllerQuickMenuActionWasExecuted([]));

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->quickMenuAction();
    }

    /**
     * @test
     */
    public function quickMenuActionGetsEventTypesFromSettings(): void
    {
        $settings = [SI::EVENT_TYPES => '1,2,3'];
        $this->configurationManager->expects($this->once())->method('getConfiguration')->willReturn($settings);
        $this->subject->injectConfigurationManager($this->configurationManager);

        $filterOptions = [];
        $this->filterOptionsService->expects($this->once())->method('getFilterOptions')->willReturn($filterOptions);

        $this->eventDispatcher->expects(self::once())
            ->method('dispatch')
            ->with($this->isInstanceOf(PerformanceControllerQuickMenuActionWasExecuted::class))
            ->willReturn($event = new PerformanceControllerQuickMenuActionWasExecuted([]));

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->quickMenuAction();
    }

    /**
     * @test
     */
    public function constructorSetsNameSpace(): void
    {
        $namespace = $this->subject->getNamespace();
        $this->assertSame(
            get_class($this->subject),
            $namespace
        );
    }
}
