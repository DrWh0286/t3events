<?php

namespace DWenzel\T3events\Tests\Unit\Controller\Backend;

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

use DWenzel\T3events\Controller\Backend\EventController;
use DWenzel\T3events\Domain\Factory\Dto\EventDemandFactory;
use DWenzel\T3events\Domain\Model\Dto\DemandInterface;
use DWenzel\T3events\Domain\Model\Dto\EventDemand;
use DWenzel\T3events\Domain\Model\Dto\ModuleData;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use DWenzel\T3events\Domain\Repository\EventRepository;
use DWenzel\T3events\Domain\Repository\VenueRepository;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\FormProtection\FormProtectionFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class EventControllerTest
 */
class EventControllerTest extends UnitTestCase
{
    /**
     * @var EventController|\PHPUnit_Framework_MockObject_MockObject|AccessibleMockObjectInterface
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
     * @var EventDemandFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventDemandFactory;

    /** @var EventDemand|\PHPUnit_Framework_MockObject_MockObject */
    protected $eventDemand;

    /**
     * @var QueryResultInterface|MockObject
     */
    protected $queryResult;

    /**
     * @var FormProtectionFactory|MockObject
     */
    protected $formProtectionFactory;

    /**
     * @var ConfigurationManagerInterface|MockObject
     */
    protected $configurationManager;

    /**
     * @var UriBuilder|MockObject
     */
    protected $uriBuilder;
    /**
     * @var (object&MockObject)|MockObject|ModuleTemplateFactory|(ModuleTemplateFactory&object&MockObject)|(ModuleTemplateFactory&MockObject)
     */
    private ModuleTemplateFactory|MockObject $moduleTemplateFactory;
    /**
     * @var VenueRepository|(VenueRepository&object&MockObject)|(VenueRepository&MockObject)|(object&MockObject)|MockObject
     */
    private MockObject|VenueRepository $venueRepository;
    private RequestInterface|MockObject $request;
    /**
     * @var MockObject|(StreamFactoryInterface&MockObject)
     */
    private StreamFactoryInterface|MockObject $streamFactory;
    /**
     * @var MockObject|(ResponseFactoryInterface&MockObject)
     */
    private MockObject|ResponseFactoryInterface $responseFactory;
    private MockObject|SearchFactory $searchFactory;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->moduleTemplateFactory = $this->createMock(ModuleTemplateFactory::class);
        $this->venueRepository = $this->createMock(VenueRepository::class);
        $this->searchFactory = $this->createMock(SearchFactory::class);

        $this->subject = $this->getAccessibleMock(
            EventController::class,
            ['emitSignal', 'getFilterOptions', 'overwriteDemandObject', 'addFlashMessage', 'translate', 'callStatic'],
            [$this->moduleTemplateFactory, $this->venueRepository, $this->searchFactory]
        );
        $this->view = $this->getMockForAbstractClass(
            ViewInterface::class
        );
        $this->queryResult = $this->getMockBuilder(QueryResultInterface::class)->getMockForAbstractClass();
        $this->moduleData = $this->getMockBuilder(ModuleData::class)->getMock();
        /** @var EventRepository|\PHPUnit_Framework_MockObject_MockObject $mockEventRepository */
        $mockEventRepository = $this->getMockBuilder(EventRepository::class)
            ->disableOriginalConstructor()->getMock();
        $mockEventRepository->method('findDemanded')->willReturn($this->queryResult);
        /** @var ConfigurationManagerInterface|\PHPUnit_Framework_MockObject_MockObject $mockConfigurationManager */
        $this->configurationManager = $this->getMockForAbstractClass(ConfigurationManagerInterface::class);
        /** @var EventDemandFactory|\PHPUnit_Framework_MockObject_MockObject $mockDemandFactory */
        $this->eventDemandFactory = $this->getMockBuilder(EventDemandFactory::class)
            ->setMethods(['createFromSettings'])->getMock();
        $this->subject->injectEventDemandFactory($this->eventDemandFactory);
        $this->subject->injectConfigurationManager($this->configurationManager);
        $this->subject->_set(
            'view',
            $this->view
        );
        $this->subject->_set(
            SI::SETTINGS,
            []
        );
        $this->subject->setModuleData($this->moduleData);
        $this->subject->injectEventRepository($mockEventRepository);
        $this->eventDemand = $this->getMockBuilder(EventDemand::class)
            ->getMock();

        $this->formProtectionFactory = $this->getMockBuilder(FormProtectionFactory::class)
            ->setMethods(['generateToken'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->uriBuilder = $this->getMockBuilder(UriBuilder::class)
            ->disableOriginalConstructor()->getMock();

        $this->request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
        $this->subject->_set('request', $this->request);

        $this->responseFactory = $this->getMockBuilder(ResponseFactoryInterface::class)->getMock();
        $this->subject->injectResponseFactory($this->responseFactory);

        $this->streamFactory = $this->getMockBuilder(StreamFactoryInterface::class)->getMock();
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
        $mockDemand = $this->getMockForAbstractClass(
            DemandInterface::class
        );

        $this->subject->_set(SI::SETTINGS, $settings);

        /** @var EventDemandFactory| \PHPUnit_Framework_MockObject_MockObject $demandFactory */
        $demandFactory = $this->subject->_get('eventDemandFactory');
        $demandFactory->expects($this->once())
            ->method('createFromSettings')
            ->with($settings)
            ->will($this->returnValue($mockDemand));

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

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

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->listAction();
    }

    /**
     * @return DemandInterface |\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockCreateDemandFromSettings()
    {
        $mockDemand = $this->getMockForAbstractClass(
            DemandInterface::class
        );

        /** @var EventDemandFactory| \PHPUnit_Framework_MockObject_MockObject $demandFactory */
        $demandFactory = $this->subject->_get('eventDemandFactory');
        $demandFactory->expects($this->once())
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

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

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

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

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
        $this->eventDemandFactory->expects($this->once())
            ->method('createFromSettings')
            ->will($this->returnValue($this->eventDemand));
        // can not match expectedTemplateVariables as soon as method 'emitSignal' is called.
        $this->view->expects($this->once())
            ->method('assignMultiple');

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->listAction();
    }

    /**
     * @test
     */
    public function newActionRedirectsToModuleEditRecord(): void
    {
        $tableName = 'tx_t3events_domain_model_event';
        $pageId = '14';
        $returnUrl = 'bazBar.html';

        $this->subject->_set('pageUid', $pageId);

        $expectedUriBuilderParameters = [
            SI::ROUTE_EDIT_RECORD_MODULE,
            [
                SI::EDIT => [
                    $tableName => [
                        $pageId => 'new'
                    ]
                ],
                SI::RETURN_URL => $returnUrl
            ]
        ];
        $redirectUrl = 'fakeUrl';

        $this->subject->expects($this->exactly(2))
            ->method('callStatic')
            ->withConsecutive(
                [GeneralUtility::class, 'makeInstance', UriBuilder::class],
                [HttpUtility::class, SI::REDIRECT]
            )->willReturnOnConsecutiveCalls(
                $this->uriBuilder,
                null
            );

        $this->uriBuilder->expects($this->exactly(2))
            ->method('buildUriFromRoute')
            ->withConsecutive(
                [SI::ROUTE_EVENT_MODULE],
                $expectedUriBuilderParameters
            )
            ->willReturnOnConsecutiveCalls(
                $returnUrl,
                $redirectUrl
            );

        $response = $this->createMock(ResponseInterface::class);
        $this->responseFactory->expects($this->once())->method('createResponse')->willReturn($response);
        $response->expects($this->any())->method('withHeader')->willReturn($response);
        $response->expects($this->any())->method('withBody')->willReturn($response);

        $this->subject->newAction();
    }

    /**
     * @test
     */
    public function initializeNewActionSetsPageUidFromFrameworkConfiguration(): void
    {
        $pageIdFromFrameWorkConfiguration = 678;

        $configuration = [
            'persistence' => [
                'storagePid' => $pageIdFromFrameWorkConfiguration
            ]
        ];

        $this->configurationManager->expects($this->once())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)
            ->will($this->returnValue($configuration));
        $this->subject->initializeNewAction();

        $pageUid = $this->subject->_get('pageUid');

        $this->assertSame(
            $pageIdFromFrameWorkConfiguration,
            $pageUid
        );
    }

    /**
     * @test
     */
    public function initializeNewActionSetPageUidFromModuleSettings(): void
    {
        $pageIdFromFrameWorkConfiguration = 678;
        $pageIdFromModuleSettings = 888;
        $configuration = [
            // framework setting
            SI::PERSISTENCE => [
                SI::STORAGE_PID => $pageIdFromFrameWorkConfiguration
            ],
            // module settings
            SI::SETTINGS => [
                SI::PERSISTENCE => [
                    SI::STORAGE_PID => $pageIdFromModuleSettings
                ]
            ]
        ];

        $this->configurationManager->expects($this->once())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)
            ->will($this->returnValue($configuration));
        $this->subject->initializeNewAction();

        $pageUid = $this->subject->_get('pageUid');


        $this->assertSame(
            $pageIdFromModuleSettings,
            $pageUid
        );
    }

    /**
     * @test
     */
    public function getConfigurationManagerReturnsConfigurationManager(): void
    {
        $this->assertEquals(
            $this->configurationManager,
            $this->subject->getConfigurationManager()
        );
    }
}
