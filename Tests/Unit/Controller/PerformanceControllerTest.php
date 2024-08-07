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
use DWenzel\T3events\Domain\Model\Dto\PerformanceDemand;
use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Model\Performance;
use DWenzel\T3events\Domain\Repository\CategoryRepository;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\PerformanceRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Session\SessionInterface;
use DWenzel\T3events\Tests\Unit\Object\MockObjectManagerTrait;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use DWenzel\T3events\Utility\SettingsUtility;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Fluid\View\TemplateView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use PHPUnit\Framework\TestCase;

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
class PerformanceControllerTest extends TestCase
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

    /**
     * @var PerformanceDemandFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $performanceDemandFactory;

    /**
     * @var PerformanceRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $performanceRepository;

    /**
     * @var ContentObjectRenderer|MockObject
     */
    protected $contentObject;

    /**
     * @var SessionInterface |MockObject
     */
    protected $session;

    protected function setUp(): void
    {
        $this->subject = $this->getAccessibleMock(PerformanceController::class,
            ['dummy', 'emitSignal', 'createSearchObject'], [], '', false);
        $this->session = $this->getMockBuilder(SessionInterface::class)
            ->setMethods(['has', 'get', 'clean', 'set', 'setNamespace'])->getMock();
        $this->performanceDemandFactory = $this->getMockBuilder(PerformanceDemandFactory::class)
            ->setMethods(['createFromSettings'])
            ->getMock();
        $mockDemand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $this->performanceDemandFactory->method('createFromSettings')->will(self::returnValue($mockDemand));
        $this->subject->injectPerformanceDemandFactory($this->performanceDemandFactory);

        $mockResult = $this->getMockBuilder(QueryResultInterface::class)->getMock();
        $this->performanceRepository = $this->getMockBuilder(PerformanceRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findDemanded'])
            ->getMock();
        $this->performanceRepository->method('findDemanded')->will(self::returnValue($mockResult));
        $this->subject->injectPerformanceRepository($this->performanceRepository);

        $this->view = $this->getMockBuilder(TemplateView::class)
            ->disableOriginalConstructor()
            ->setMethods(['assign', 'assignMultiple'])
            ->getMock();
        $this->contentObject = $this->getMockBuilder(ContentObjectRenderer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockDispatcher = $this->getMockBuilder(Dispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockRequest = $this->getMockBuilder(Request::class)->getMock();
        $mockConfigurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
            ->setMethods(
                [
                    'getContentObject', 'setContentObject', 'getConfiguration',
                    'setConfiguration', 'isFeatureEnabled'
                ]
            )
            ->getMockForAbstractClass();
        $this->objectManager = $this->getMockObjectManager();
        $this->subject->_set('view', $this->view);
        $this->subject->_set('session', $this->session);
        $this->subject->_set('contentObject', $this->contentObject);
        $this->subject->_set('signalSlotDispatcher', $mockDispatcher);
        $this->subject->_set('request', $mockRequest);
        $this->subject->_set('configurationManager', $mockConfigurationManager);
        $this->subject->_set('objectManager', $this->objectManager);
        $this->subject->_set(SI::SETTINGS, $this->settings);
    }

    /**
     * @test
     * @covers ::injectPerformanceRepository
     */
    public function injectPerformanceRepositorySetsPerformanceRepository(): void
    {
        /** @var PerformanceRepository $repository */
        $repository = $this->getMockBuilder(PerformanceRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->injectPerformanceRepository($repository);

        $this->assertSame(
            $repository,
            $this->subject->_get('performanceRepository')
        );
    }

    /**
     * @test
     * @covers ::injectGenreRepository
     */
    public function injectGenreRepositorySetsGenreRepository(): void
    {
        /** @var GenreRepository $repository */
        $repository = $this->getMockBuilder(GenreRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->injectGenreRepository($repository);

        $this->assertAttributeSame(
            $repository,
            'genreRepository',
            $this->subject
        );
    }

    /**
     * @test
     * @covers ::injectVenueRepository
     */
    public function injectVenueRepositorySetsVenueRepository(): void
    {
        /** @var VenueRepository $repository */
        $repository = $this->getMockBuilder(VenueRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->injectVenueRepository($repository);

        $this->assertAttributeSame(
            $repository,
            'venueRepository',
            $this->subject
        );
    }

    /**
     * @test
     * @covers ::injectEventTypeRepository
     */
    public function injectEventTypeRepositorySetsEventTypeRepository(): void
    {
        /** @var EventTypeRepository $repository */
        $repository = $this->getMockBuilder(EventTypeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->injectEventTypeRepository($repository);

        $this->assertSame(
            $repository,
            $this->subject->_get('eventTypeRepository')
        );
    }

    /**
     * @test
     * @covers ::injectCategoryRepository
     */
    public function injectCategoryRepositorySetsCategoryRepository(): void
    {
        /** @var CategoryRepository $repository */
        $repository = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->injectCategoryRepository($repository);

        $this->assertSame(
            $repository,
            $this->subject->_get('categoryRepository')
        );
    }

    /**
     * @test
     */
    public function initializeActionsSetsContentObject(): void
    {
        $this->subject->_set(SI::SETTINGS, []);
        $this->mockSettingsUtility();
        $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
            ->setMethods(
                [
                    'getContentObject', 'setContentObject', 'getConfiguration',
                    'setConfiguration', 'isFeatureEnabled'
                ]
            )
            ->getMock();

        $configurationManager->expects(self::once())
            ->method('getContentObject');
        $this->subject->_set('configurationManager', $configurationManager);

        $this->subject->initializeAction();
    }

    protected function mockSettingsUtility()
    {
        /** @var SettingsUtility|\PHPUnit_Framework_MockObject_MockObject $mockSettingsUtility */
        $mockSettingsUtility = $this->getMockBuilder(SettingsUtility::class)
            ->setMethods(['getControllerKey'])
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
        $this->subject->_set(SI::SETTINGS, []);
        $this->mockSettingsUtility();
        $overwriteDemand = ['foo'];
        $mockSession = $this->subject->_get('session');
        $mockRequest = $this->subject->_get('request');
        $mockRequest->expects(self::once())
            ->method('hasArgument')
            ->will(self::returnValue(true));
        $mockRequest->expects(self::once())
            ->method('getArgument')
            ->will(self::returnValue($overwriteDemand));

        $mockSession->expects(self::once())
            ->method('set')
            ->with('tx_t3events_overwriteDemand', serialize($overwriteDemand));

        $this->subject->initializeAction();
    }

    /**
     * @test
     */
    public function initializeQuickMenuActionResetsOverwriteDemandInSession(): void
    {
        $mockSession = $this->subject->_get('session');
        $mockRequest = $this->subject->_get('request');
        $mockRequest->expects(self::once())
            ->method('hasArgument')
            ->will(self::returnValue(false));
        $mockSession->expects(self::once())
            ->method('clean');
        $this->subject->initializeQuickMenuAction();
    }

    /**
     * @test
     * @covers ::createDemandFromSettings
     */
    public function createDemandFromSettingsReturnsDemandObject(): void
    {
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
        $this->mockSettingsUtility();
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

        $this->subject->overwriteDemandObject($demand, $overwriteDemand);
    }

    /**
     * @test
     * @covers ::listAction
     */
    public function listActionCallsOverwriteDemandObject(): void
    {
        $this->subject = $this->getMockBuilder(PerformanceController::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'overwriteDemandObject',
                    'createDemandFromSettings',
                    'emitSignal'
                ]
            )
            ->getMock();
        /** @var PerformanceRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(PerformanceRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->injectPerformanceRepository($repository);
        /** @var TemplateView|\PHPUnit_Framework_MockObject_MockObject $view */
        $view = $this->getMockBuilder(TemplateView::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->inject($this->subject, 'view', $view);
        $this->inject($this->subject, 'contentObject', $this->contentObject);
        $settings = array('foo');
        $this->inject($this->subject, SI::SETTINGS, $settings);
        $this->inject($this->subject, 'performanceDemandFactory', $this->performanceDemandFactory);
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $mockDemand = $this->getMockBuilder(PerformanceDemand::class)->getMock();

        $this->performanceDemandFactory->expects(self::once())
            ->method('createFromSettings')
            ->will(self::returnValue($mockDemand));

        $this->subject->expects(self::once())
            ->method('overwriteDemandObject')
            ->with($mockDemand);
        $this->subject->listAction([]);
    }

    /**
     * @test
     * @covers ::listAction
     */
    public function listActionCallsFindDemanded(): void
    {
        $this->subject = $this->getAccessibleMock(
            'DWenzel\\T3events\\Controller\\PerformanceController',
            array('overwriteDemandObject', 'emitSignal'),
            [], '', false
        );
        /** @var PerformanceRepository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder(PerformanceRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findDemanded'])
            ->getMock();
        $this->subject->injectPerformanceRepository($repository);
        $view = $this->getMockBuilder(TemplateView::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->_set('view', $view);
        $settings = array('foo');
        $this->subject->_set(SI::SETTINGS, $settings);
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $mockDemand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $this->subject->_set(SI::SETTINGS, $settings);
        $this->inject($this->subject, 'performanceDemandFactory', $this->performanceDemandFactory);

        $this->performanceDemandFactory->expects(self::once())
            ->method('createFromSettings')
            ->will(self::returnValue($mockDemand));

        $this->subject->expects(self::once())
            ->method('overwriteDemandObject')
            ->with($mockDemand)
            ->will(self::returnValue($mockDemand));

        $repository->expects(self::once())
            ->method('findDemanded')
            ->with($mockDemand);

        $this->subject->listAction([]);
    }

    /**
     * @test
     */
    public function showActionAssignsVariables(): void
    {
        //$this->markTestSkipped('wrong arguments in assignMultiple');
        $fixture = $this->getAccessibleMock(
            PerformanceController::class,
            ['emitSignal'], [], '', false
        );
        $settings = ['foo'];
        $performance = new Performance();
        $templateVariables = [
            SI::SETTINGS => $settings,
            'performance' => $performance
        ];

        $fixture->expects(self::once())
            ->method('emitSignal')
            ->will(self::returnValue($templateVariables));

        $view = $this->getMockBuilder(TemplateView::class)
            ->disableOriginalConstructor()
            ->setMethods(['assignMultiple'])
            ->getMock();

        $view->expects(self::once())
            ->method('assignMultiple')
            ->with();

        $fixture->_set('view', $view);
        $fixture->_set(SI::SETTINGS, $settings);

        $fixture->showAction($performance);
    }

    /**
     * @test
     */
    public function quickMenuActionGetsOverwriteDemandFromSession(): void
    {
        $this->injectMockRepositories(['findMultipleByUid', 'findAll']);
        $mockSession = $this->getMockBuilder(SessionInterface::class)
            ->setMethods(['get', 'set', 'has', 'clean', 'setNamespace'])
            ->getMock();
        $mockSession->expects(self::once())
            ->method('get');
        $this->subject->_set('session', $mockSession);
        $this->subject->expects(self::once())
            ->method('emitSignal')
            ->will(self::returnValue([]));

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
            $this->inject($this->subject, $propertyName, $mock);
        }
    }

    /**
     * @test
     */
    public function quickMenuActionGetsGenresFromSettings(): void
    {
        $settings = [SI::GENRES => '1,2,3'];
        $this->subject->_set(SI::SETTINGS, $settings);

        $this->injectMockRepositories(['findMultipleByUid', 'findAll']);
        $mockGenreRepository = $this->subject->_get('genreRepository');
        $mockGenreRepository->expects(self::once())
            ->method('findMultipleByUid')
            ->with('1,2,3', 'title');
        $this->subject->expects(self::once())
            ->method('emitSignal')
            ->will(self::returnValue([]));

        $this->subject->quickMenuAction();
    }

    /**
     * @test
     */
    public function quickMenuActionGetsVenuesFromSettings(): void
    {
        $settings = [SI::VENUES => '1,2,3'];
        $this->subject->_set(SI::SETTINGS, $settings);

        $this->injectMockRepositories(['findMultipleByUid', 'findAll']);
        $mockVenueRepository = $this->subject->_get('venueRepository');
        $mockVenueRepository->expects(self::once())
            ->method('findMultipleByUid')
            ->with('1,2,3', 'title');
        $this->subject->expects(self::once())
            ->method('emitSignal')
            ->will(self::returnValue([]));

        $this->subject->quickMenuAction();
    }

    /**
     * @test
     */
    public function quickMenuActionGetsEventTypesFromSettings(): void
    {
        $settings = [SI::EVENT_TYPES => '1,2,3'];
        $this->subject->_set(SI::SETTINGS, $settings);

        $this->injectMockRepositories(['findMultipleByUid', 'findAll']);
        $mockEventTypeRepository = $this->subject->_get('eventTypeRepository');
        $mockEventTypeRepository->expects(self::once())
            ->method('findMultipleByUid')
            ->with('1,2,3', 'title');
        $this->subject->expects(self::once())
            ->method('emitSignal')
            ->will(self::returnValue([]));

        $this->subject->quickMenuAction();
    }

    /**
     * @test
     */
    public function constructorSetsNameSpace(): void
    {
        $this->subject->__construct();
        $this->assertAttributeSame(
            get_class($this->subject),
            'namespace',
            $this->subject
        );
    }

    /**
     * mocks getting an PerformanceDemandObject from ObjectManager
     * @return \PHPUnit_Framework_MockObject_MockObject|PerformanceDemand
     */
    public function mockGetPerformanceDemandFromFactory()
    {
        $this->performanceDemandFactory = $this->getMockBuilder(PerformanceDemandFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createFromSettings'])
            ->getMock();
        /** @var PerformanceDemand|\PHPUnit_Framework_MockObject_MockObject $demand */
        $mockPerformanceDemand = $this->getMockBuilder(PerformanceDemand::class)->getMock();
        $this->performanceDemandFactory->expects(self::once())
            ->method('createFromSettings')
            ->will(self::returnValue($mockPerformanceDemand));
        $this->subject->injectPerformanceDemandFactory($this->performanceDemandFactory);
        return $mockPerformanceDemand;
    }

}
