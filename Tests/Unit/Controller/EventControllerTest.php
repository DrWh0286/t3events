<?php

namespace DWenzel\T3events\Tests\Unit\Controller;

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

use DWenzel\T3events\Controller\EventController;
use DWenzel\T3events\Domain\Factory\Dto\EventDemandFactory;
use DWenzel\T3events\Domain\Model\Dto\EventDemand;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Domain\Model\Event;
use DWenzel\T3events\Domain\Repository\EventRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Session\SessionInterface;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use DWenzel\T3events\Utility\SettingsUtility;
use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewResolverInterface;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\View\TemplateView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Test case for class \DWenzel\T3events\Controller\EventController.
 *
 * @coversDefaultClass \DWenzel\T3events\Controller\EventController
 */
class EventControllerTest extends UnitTestCase
{
    /**
     * @var \DWenzel\T3events\Controller\EventController|\PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface
     */
    protected $subject;

    /**
     * @var array
     */
    protected $settings = [
        'hideIfEmptyResult' => 1
    ];

    /**
     * @var ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    /**
     * @var EventDemandFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDemandFactory;

    /**
     * @var EventRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventRepository;
    private VenueRepository|MockObject $venueRepository;
    /**
     * @var MockObject|(ResponseFactoryInterface&MockObject)
     */
    private MockObject|ResponseFactoryInterface $responseFactory;
    /**
     * @var (SessionInterface&MockObject)|MockObject
     */
    private MockObject|SessionInterface $session;
    private MockObject|SearchFactory $searchFacotry;


    protected function setUp(): void
    {
        //        $this->subject = $this->getAccessibleMock(
        //            EventController::class,
        //            ['overwriteDemandObject', 'emitSignal', 'addFlashMessage', 'translate'], [], '', false
        //        );

        $this->venueRepository = $this->getMockBuilder(VenueRepository::class)->disableOriginalConstructor()->getMock();
        $this->signalSlotDispatcher = $this->getMockBuilder(Dispatcher::class)->disableOriginalConstructor()->getMock();
        $this->searchFacotry = $this->getMockBuilder(SearchFactory::class)->disableOriginalConstructor()->getMock();

        $this->signalSlotDispatcher->expects(self::any())->method('dispatch')->willReturn([0 => []]);

        $this->subject = $this->getAccessibleMock(EventController::class, ['emitSignal', 'translate', 'addFlashMessage', 'overwriteDemandObject'], [$this->venueRepository, $this->signalSlotDispatcher, $this->searchFacotry]);

        $viewResolver = $this->getMockBuilder(ViewResolverInterface::class)->getMock();

        $this->view = $this->getMockBuilder(TemplateView::class)
            ->disableOriginalConstructor()
            ->getMock();

        $viewResolver->expects($this->any())->method('resolve')->willReturn($this->view);

        $this->subject->injectViewResolver($viewResolver);

        $this->eventDemandFactory = $this->getMockBuilder(EventDemandFactory::class)
            ->setMethods(['createFromSettings'])
            ->getMock();
        $mockDemand = $this->getMockEventDemand();
        $this->eventDemandFactory->method('createFromSettings')->will($this->returnValue($mockDemand));
        $this->subject->injectEventDemandFactory($this->eventDemandFactory);
        $mockResult = $this->getMockBuilder(QueryResultInterface::class)->getMock();
        $this->eventRepository = $this->getMockBuilder(EventRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventRepository->method('findDemanded')->will($this->returnValue($mockResult));
        $this->subject->injectEventRepository($this->eventRepository);
        /** @var SessionInterface|\PHPUnit_Framework_MockObject_MockObject $session */
        $this->session = $this->getMockBuilder(SessionInterface::class)->getMock();

        $mockRequest = $this->getMockBuilder(Request::class)->getMock();
        $this->subject->_set('view', $this->view);
        $this->subject->injectSession($this->session);
        $this->subject->_set('request', $mockRequest);

        $mockContentObjectRenderer = $this->getMockBuilder(ContentObjectRenderer::class)->getMock();
        /** @var ConfigurationManagerInterface|\PHPUnit_Framework_MockObject_MockObject $mockConfigurationManager */
        $mockConfigurationManager = $this->createMock(ConfigurationManagerInterface::class);
        $mockConfigurationManager->expects($this->any())->method('getContentObject')->will($this->returnValue($mockContentObjectRenderer));
        $mockConfigurationManager->expects($this->once())->method('getConfiguration')->willReturn($this->settings);
        $this->subject->injectConfigurationManager($mockConfigurationManager);

        $this->responseFactory = $this->getMockBuilder(ResponseFactoryInterface::class)->getMock();
        $this->subject->injectResponseFactory($this->responseFactory);
        $this->streamFactory = $this->getMockBuilder(StreamFactoryInterface::class)->getMock();
        $this->subject->injectStreamFactory($this->streamFactory);

        $this->subject->_set(SI::SETTINGS, $this->settings);
    }

    /**
     * @return EventDemand|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockEventDemand(array $methods = [])
    {
        return $this->getMockBuilder(EventDemand::class)
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * mocks getting an EventDemandObject from ObjectManager
     * @return \PHPUnit_Framework_MockObject_MockObject|EventDemand
     */
    public function mockGetEventDemandFromFactory()
    {
        $this->eventDemandFactory = $this->getMockBuilder(EventDemandFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['createFromSettings'])->getMock();
        $mockEventDemand = $this->getMockEventDemand();
        $this->eventDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->will($this->returnValue($mockEventDemand));
        $this->subject->injectEventDemandFactory($this->eventDemandFactory);
        return $mockEventDemand;
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
        $mockRequest->expects($this->exactly(2))->method('hasArgument')->willReturn(true);
        $mockRequest->expects($this->once())
            ->method('getArgument')
            ->will($this->returnValue($overwriteDemand));

        $mockSession->expects($this->once())
            ->method('set')
            ->with('tx_t3events_overwriteDemand', serialize($overwriteDemand));

        $this->subject->initializeAction();
    }

    /**
     * mocks the SettingsUtility
     */
    protected function mockSettingsUtility()
    {
        /** @var SettingsUtility|\PHPUnit_Framework_MockObject_MockObject $mockSettingsUtility */
        $mockSettingsUtility = $this->getMockBuilder(SettingsUtility::class)->disableOriginalConstructor()->getMock();
        $this->subject->injectSettingsUtility($mockSettingsUtility);
        $mockSettingsUtility->expects($this->any())
            ->method('getControllerKey')
            ->will($this->returnValue('performance'));
    }

    /**
     * @test
     */
    public function showActionAssignsVariablesToView(): void
    {
        $mockEvent = $this->getMockEvent();

        $this->view->expects($this->once())
            ->method('assignMultiple');

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->showAction($mockEvent);
    }

    /**
     * @return mixed
     */
    protected function getMockEvent()
    {
        return $this->getMockBuilder(Event::class)->getMock();
    }

    /**
     * @test
     */
    public function showActionEmitsSignal(): void
    {
        $mockEvent = $this->getMockEvent();

        $this->subject->expects($this->once())
            ->method('emitSignal')
            ->with(
                EventController::class,
                EventController::EVENT_SHOW_ACTION
            );

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->showAction($mockEvent);
    }

    /**
     * @test
     */
    public function listActionGetsEventDemandFromFactory(): void
    {
        $mockDemand = $this->getMockEventDemand();
        $this->eventDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->with($this->settings)
            ->will($this->returnValue($mockDemand));

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function listActionOverwritesDemandObject(): void
    {
        $mockDemand = $this->getMockEventDemand();
        $this->eventDemandFactory->method('createFromSettings')
            ->will($this->returnValue($mockDemand));
        $this->subject->expects($this->once())
            ->method('overwriteDemandObject')
            ->with($mockDemand);

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function listActionGetsEventsFromRepository(): void
    {
        $this->eventRepository->expects($this->once())
            ->method('findDemanded')
            ->with($this->isInstanceOf(EventDemand::class));

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function listActionAddsFlashMessageForEmptyResult(): void
    {
        $title = 'foo';
        $message = 'bar';

        $this->subject->_set(SI::SETTINGS, ['hideIfEmptyResult' => false]);

        $this->subject->expects($this->exactly(2))
            ->method('translate')
            ->withConsecutive(
                ['tx_t3events.noEventsForSelectionMessage'],
                ['tx_t3events.noEventsForSelectionTitle']
            )
            ->will($this->onConsecutiveCalls($message, $title));
        $this->subject->expects($this->once())
            ->method('addFlashMessage')
            ->with($message, $title, FlashMessage::WARNING);

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function listActionEmitsSignal(): void
    {
        $this->subject->expects($this->once())
            ->method('emitSignal')
            ->with(EventController::class, EventController::EVENT_LIST_ACTION);

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function listActionAssignsVariablesToView(): void
    {
        $this->view->expects($this->once())
            ->method('assignMultiple');

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->listAction();
    }
}
