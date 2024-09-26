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
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Event\EventControllerListActionWasExecuted;
use DWenzel\T3events\Event\EventControllerShowActionWasExecuted;
use DWenzel\T3events\Service\TranslationService;
use DWenzel\T3events\Session\SessionInterface;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use DWenzel\T3events\Utility\SettingsUtility;
use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3Fluid\Fluid\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Fluid\View\TemplateView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Test case for class \DWenzel\T3events\Controller\EventController.
 *
 * @coversDefaultClass EventController
 */
class EventControllerTest extends UnitTestCase
{
    protected EventController|MockObject $subject;
    protected array $settings = [
        'hideIfEmptyResult' => 1
    ];
    protected ViewInterface|MockObject $view;
    protected EventDemandFactory|MockObject $eventDemandFactory;
    protected EventRepository|MockObject $eventRepository;
    private VenueRepository|MockObject $venueRepository;
    private MockObject|ResponseFactoryInterface $responseFactory;
    private MockObject|SessionInterface $session;
    private MockObject|EventDispatcherInterface $eventDispatcher;
    private TranslationService|MockObject $translationService;
    private SettingsUtility|MockObject $settingsUtility;

    protected function setUp(): void
    {
        $this->venueRepository = $this->getMockBuilder(VenueRepository::class)->disableOriginalConstructor()->getMock();
        $this->searchFacotry = $this->getMockBuilder(SearchFactory::class)->disableOriginalConstructor()->getMock();
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $this->eventDemandFactory = $this->getMockBuilder(EventDemandFactory::class)->disableOriginalConstructor()->getMock();
        $this->eventRepository = $this->getMockBuilder(EventRepository::class)->disableOriginalConstructor()->getMock();
        $this->genreRepository = $this->getMockBuilder(GenreRepository::class)->disableOriginalConstructor()->getMock();
        $this->eventTypeRepository = $this->getMockBuilder(EventTypeRepository::class)->disableOriginalConstructor()->getMock();
        $this->session = $this->getMockBuilder(SessionInterface::class)->getMock();
        $this->translationService = $this->getMockBuilder(TranslationService::class)->disableOriginalConstructor()->getMock();
        $this->settingsUtility = $this->getMockBuilder(SettingsUtility::class)->disableOriginalConstructor()->getMock();

        $this->subject = $this->getAccessibleMock(
            EventController::class,
            ['addFlashMessage'],
            [
                $this->venueRepository,
                $this->eventDemandFactory,
                $this->eventRepository,
                $this->genreRepository,
                $this->eventTypeRepository,
                $this->session,
                $this->translationService,
                $this->settingsUtility
            ]
        );

        $this->subject->injectEventDispatcher($this->eventDispatcher);

        $this->view = $this->getMockBuilder(TemplateView::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->_set('view', $this->view);

        $mockResult = $this->getMockBuilder(QueryResultInterface::class)->getMock();
        $this->eventRepository->method('findDemanded')->will($this->returnValue($mockResult));

        $mockRequest = $this->getMockBuilder(Request::class)->getMock();
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
     * @test
     */
    public function initializeActionSetsOverwriteDemandInSession(): void
    {
        $this->subject->_set(SI::SETTINGS, []);
        $this->settingsUtility->expects($this->any())
            ->method('getControllerKey')
            ->will($this->returnValue('performance'));
        $overwriteDemand = ['foo'];
        $mockRequest = $this->subject->_get('request');
        $mockRequest->expects($this->exactly(2))->method('hasArgument')->willReturn(true);
        $mockRequest->expects($this->once())
            ->method('getArgument')
            ->will($this->returnValue($overwriteDemand));

        $this->session->expects($this->once())
            ->method('set')
            ->with('tx_t3events_overwriteDemand', serialize($overwriteDemand));

        $this->subject->initializeAction();
    }

    /**
     * @test
     */
    public function showActionAssignsVariablesToView(): void
    {
        $mockEvent = $this->getMockEvent();

        $this->eventDispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(EventControllerShowActionWasExecuted::class))
            ->willReturn(new EventControllerShowActionWasExecuted(['foo' => 'bar']));

        $this->view->expects($this->once())
            ->method('assignMultiple')->with(['foo' => 'bar']);

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
    public function showActionDispatchesShowActionWasCalledEvent(): void
    {
        $mockEvent = $this->getMockEvent();

        $this->eventDispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(EventControllerShowActionWasExecuted::class))
            ->willReturn(new EventControllerShowActionWasExecuted(['foo' => 'bar']));

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
        $mockDemand = $this->getMockBuilder(EventDemand::class)->getMock();
        $this->eventDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->with($this->settings)
            ->will($this->returnValue($mockDemand));

        $this->eventDispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(EventControllerListActionWasExecuted::class))
            ->willReturn(new EventControllerListActionWasExecuted(['foo' => 'bar']));

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
        $mockDemand = $this->getMockBuilder(EventDemand::class)->getMock();
        $this->eventDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->will($this->returnValue($mockDemand));
        $mockDemand->expects($this->once())
            ->method('overwriteDemandObject')
            ->with(['demand' => 'dummy'], $this->settings);

        $this->eventDispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(EventControllerListActionWasExecuted::class))
            ->willReturn(new EventControllerListActionWasExecuted(['foo' => 'bar']));

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

        $mockDemand = $this->getMockBuilder(EventDemand::class)->getMock();
        $this->eventDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->will($this->returnValue($mockDemand));
        $mockDemand->expects($this->once())
            ->method('overwriteDemandObject')
            ->with(['demand' => 'dummy'], $this->settings);

        $this->eventDispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(EventControllerListActionWasExecuted::class))
            ->willReturn(new EventControllerListActionWasExecuted(['foo' => 'bar']));

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

        $mockDemand = $this->getMockBuilder(EventDemand::class)->getMock();
        $this->eventDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->will($this->returnValue($mockDemand));
        $mockDemand->expects($this->once())
            ->method('overwriteDemandObject')
            ->with(['demand' => 'dummy'], ['hideIfEmptyResult' => false]);

        $this->translationService->expects($this->exactly(2))
            ->method('translate')
            ->withConsecutive(
                ['tx_t3events.noEventsForSelectionMessage'],
                ['tx_t3events.noEventsForSelectionTitle']
            )
            ->will($this->onConsecutiveCalls($message, $title));
        $this->subject->expects($this->once())
            ->method('addFlashMessage')
            ->with($message, $title, FlashMessage::WARNING);

        $this->eventDispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(EventControllerListActionWasExecuted::class))
            ->willReturn(new EventControllerListActionWasExecuted(['foo' => 'bar']));

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
    public function listActionDispatchesListActionWasCalledEvent(): void
    {
        $mockDemand = $this->getMockBuilder(EventDemand::class)->getMock();
        $this->eventDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->will($this->returnValue($mockDemand));
        $mockDemand->expects($this->once())
            ->method('overwriteDemandObject')
            ->with(['demand' => 'dummy'], $this->settings);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(EventControllerListActionWasExecuted::class))
            ->willReturn(new EventControllerListActionWasExecuted([]));

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
        $mockDemand = $this->getMockBuilder(EventDemand::class)->getMock();
        $this->eventDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->will($this->returnValue($mockDemand));
        $mockDemand->expects($this->once())
            ->method('overwriteDemandObject')
            ->with(['demand' => 'dummy'], $this->settings);

        $this->eventDispatcher->expects($this->once())->method('dispatch')
            ->with($this->isInstanceOf(EventControllerListActionWasExecuted::class))
            ->willReturn(new EventControllerListActionWasExecuted(['foo' => 'bar']));

        $this->view->expects($this->once())
            ->method('assignMultiple')->with(['foo' => 'bar']);

        $this->session->expects($this->any())->method('has')->with('tx_t3events_overwriteDemand')->willReturn(true);
        $this->session->expects($this->any())->method('get')->with('tx_t3events_overwriteDemand')->willReturn(serialize(['demand' => 'dummy']));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->listAction();
    }
}
