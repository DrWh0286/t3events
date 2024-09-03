<?php

namespace DWenzel\T3events\Tests\Unit\Controller;

use DWenzel\T3events\Controller\FlashMessageTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Service\ExtensionService;

/***************************************************************
 *  Copyright notice
 *  (c) 2016 Dirk Wenzel <dirk.wenzel@cps-it.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class FlashMessageTraitTest extends UnitTestCase
{
    /**
     * @var FlashMessageTrait
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new class () {
            use FlashMessageTrait;

            /**
             * @return FlashMessageService
             */
            public function getFlashMessageService(): FlashMessageService
            {
                return $this->flashMessageService;
            }

            public function setFlashMessageQueue($flashMessageQueue)
            {
                $this->flashMessageQueue = $flashMessageQueue;
            }

            public function setRequest($request)
            {
                $this->request = $request;
            }
        };
    }

    /**
     * @return ConfigurationManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockConfigurationManager()
    {
        $mockConfigurationManager = $this->getMockForAbstractClass(
            ConfigurationManagerInterface::class
        );
        $this->inject(
            $this->subject,
            'configurationManager',
            $mockConfigurationManager
        );

        return $mockConfigurationManager;
    }

    /**
     * @return FlashMessageService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockFlashMessageService()
    {
        $mockFlashMessageService = $this->getMockBuilder(FlashMessageService::class)
            ->setMethods(['getMessageQueueByIdentifier'])->getMock();
        $this->subject->injectFlashMessageService($mockFlashMessageService);

        return $mockFlashMessageService;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Mvc\Request|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockRequest()
    {
        $mockRequest = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\Request::class)
            ->getMock();

        $this->subject->setRequest($mockRequest);

        return $mockRequest;
    }

    /**
     * @return ExtensionService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockExtensionService()
    {
        $mockExtensionService = $this->getMockBuilder(ExtensionService::class)
            ->setMethods(['getPluginNamespace'])->getMock();

        $this->subject->injectExtensionService($mockExtensionService);

        return $mockExtensionService;
    }

    /**
     * @test
     */
    public function getFlashMessageQueueInstantiatesQueue(): void
    {
        $namespace = 'fooNamespace';
        $extensionName = 'barExtension';
        $pluginName = 'bazPlugin';

        $mockExtensionService = $this->mockExtensionService();
        $mockRequest = $this->mockRequest();

        $mockExtensionService->expects($this->once())
            ->method('getPluginNamespace')
            ->with($extensionName, $pluginName)
            ->will($this->returnValue($namespace));
        $mockRequest->expects($this->once())
            ->method('getControllerExtensionName')
            ->will($this->returnValue($extensionName));
        $mockRequest->expects($this->once())
            ->method('getPluginName')
            ->will($this->returnValue($pluginName));

        $mockFlashMessageQueue = $this->getMockFlashMessageQueue();
        $mockFlashMessageService = $this->mockFlashMessageService();
        $mockFlashMessageService->expects($this->once())
            ->method('getMessageQueueByIdentifier')
            ->with('extbase.flashmessages.' . $namespace)
            ->will($this->returnValue($mockFlashMessageQueue));

        $this->assertSame(
            $mockFlashMessageQueue,
            $this->subject->getFlashMessageQueue()
        );
    }

    /**
     * @test
     */
    public function addFlashMessageThrowsExceptionForMissingMessageBody(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1243258395);
        $this->subject->addFlashMessage(5);
    }

    /**
     * @test
     */
    public function addFlashMessageEnqueuesMessage(): void
    {
        $messageBody = 'foo';
        $messageTitle = 'bar';
        $severity = AbstractMessage::ERROR;
        $storeInSession = false;
        $expectedMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $messageBody,
            $messageTitle,
            $severity,
            $storeInSession
        );

        $mockMessageQueue = $this->getMockFlashMessageQueue(['enqueue']);
        $this->subject->setFlashMessageQueue($mockMessageQueue);

        $mockMessageQueue->expects($this->once())
            ->method('enqueue')
            ->with($this->equalTo($expectedMessage));
        $this->subject->addFlashMessage(
            $messageBody,
            $messageTitle,
            $severity,
            $storeInSession
        );
    }

    /**
     * @param array $methods Methods to mock
     * @return FlashMessageQueue|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockFlashMessageQueue(array $methods = [])
    {
        return $this->getMockBuilder(FlashMessageQueue::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
}
