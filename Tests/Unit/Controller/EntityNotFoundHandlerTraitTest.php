<?php

namespace DWenzel\T3events\Tests\Unit\Controller;

use DWenzel\T3events\Controller\EntityNotFoundHandlerTrait;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Frontend\Page\PageAccessFailureReasons;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class DummyParent
 */
class DummyParent extends ActionController
{
    /**
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response
     * @return void
     * @throws \Exception
     * @override \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
     */
    public function processRequest(RequestInterface $request): ResponseInterface
    {
        throw new TargetNotFoundException('foo', 1464634137);
    }
}

/**
 * Class DummyEntityNotFoundHandlerController
 */
class DummyEntityNotFoundHandlerController extends DummyParent
{
    use EntityNotFoundHandlerTrait;
}

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
class EntityNotFoundHandlerTraitTest extends UnitTestCase
{
    /**
     * @var EntityNotFoundHandlerTrait|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;
    /**
     * @var MockObject|(Request&MockObject)
     */
    private MockObject|Request $mockRequest;
    private MockObject|Dispatcher $mockDispatcher;

    /**
     * set up
     */
    protected function setUp(): void
    {
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $mockRequest */
        $this->mockRequest = $this->getMockBuilder(Request::class)->getMock();
        $this->mockDispatcher = $this->getMockDispatcher();
        $this->mockUriBuilder = $this->getAccessibleMock(
            UriBuilder::class
        );

        $this->subject = new class ($this->mockDispatcher, $this->mockRequest, $this->mockUriBuilder) extends DummyParent {
            use EntityNotFoundHandlerTrait;
            public function __construct(Dispatcher $signalSlotDispatcher, Request $request, UriBuilder $uriBuilder)
            {
                $this->request = $request;
                $this->signalSlotDispatcher = $signalSlotDispatcher;
                $this->uriBuilder = $uriBuilder;
            }

            public function setSettings(array $settings)
            {
                $this->settings = $settings;
            }

            public function isSSLEnabled()
            {
                return true;
            }

            public function forward($actionName, $controllerName = null, $extensionName = null, array $arguments = null)
            {
                throw new class (
                    'forward method called',
                    7864376
                ) extends \Exception {};
            }

            protected function redirect($actionName, $controllerName = null, $extensionName = null, array $arguments = null, $pageUid = null, $delay = 0, $statusCode = 303): void
            {
                throw new class (
                    'with ' . $actionName . '; redirect method called with action!',
                    7864377
                ) extends \Exception {};
            }

            protected function redirectToUri($uri, $delay = 0, $statusCode = 303): void
            {
                throw new class (
                    'with: ' . $uri . '; and delay: ' . $delay . '; and status code: ' . $statusCode . '; redirectToUri method was called',
                    7864378
                ) extends \Exception {};
            }
        };
    }

    /**
     * @test
     */
    public function emptyHandleEntityNotFoundErrorConfigurationReturns(): void
    {
        $this->subject->handleEntityNotFoundError('');
    }

    /**
     * @test
     */
    public function handleEntityNotFoundErrorConfigurationRedirectsToListView(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('redirect method called');
        $this->expectExceptionCode(7864377);

        $this->subject->handleEntityNotFoundError('redirectToListView');
    }

    /**
     * @test
     */
    public function handleEntityNotFoundErrorConfigurationCallsPageNotFoundHandler(): void
    {
        $errorController = $this->createMock(ErrorController::class);

        GeneralUtility::setContainer(new class ([ErrorController::class => $errorController]) implements ContainerInterface {
            public function __construct(private readonly array $classes)
            {
            }

            public function get(string $id)
            {
                if (isset($this->classes[$id])) {
                    return $this->classes[$id];
                }

                return null;
            }

            public function has(string $id)
            {
                if (isset($this->classes[$id])) {
                    return true;
                }

                return false;
            }
        });

        $errorController->expects($this->once())->method('pageNotFoundAction')
            ->with($this->mockRequest, $this->subject->getEntityNotFoundMessage(), ['code' => PageAccessFailureReasons::PAGE_NOT_FOUND])
            ->willReturn($mockResponse = $this->createMock(ResponseInterface::class));

        $this->expectException(ImmediateResponseException::class);

        $this->subject->handleEntityNotFoundError('pageNotFoundHandler');
    }

    /**
     * @test
     */
    public function handleEntityNotFoundErrorConfigurationWithTooFeeOptionsForRedirectToPageThrowsError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->handleEntityNotFoundError('redirectToPage');
    }


    /**
     * @test
     */
    public function handleEntityNotFoundErrorConfigurationWithTooManyOptionsForRedirectToPageThrowsError(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->handleEntityNotFoundError('redirectToPage, arg1, arg2, arg3');
    }

    /**
     * @test
     */
    public function handleEntityNotFoundErrorConfigurationRedirectsToCorrectPage(): void
    {
        $this->mockUriBuilder->expects(self::once())
            ->method('setTargetPageUid')
            ->with('55');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('with: ; and delay: 0; and status code: 303; redirectToUri method was called');
        $this->expectExceptionCode(7864378);

        $this->subject->handleEntityNotFoundError('redirectToPage, 55');
    }

    /**
     * @test
     */
    public function handleEntityNotFoundErrorConfigurationRedirectsToCorrectPageWithStatus(): void
    {
        $this->mockUriBuilder->expects(self::once())
            ->method('setTargetPageUid')
            ->with('1');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('with: ; and delay: 0; and status code: 301; redirectToUri method was called');
        $this->expectExceptionCode(7864378);

        $this->subject->handleEntityNotFoundError('redirectToPage, 1, 301');
    }

    /**
     * @test
     */
    public function handleEntityNotFoundErrorConfigurationRedirectsWithSSL(): void
    {
        $this->mockUriBuilder->expects(self::once())
            ->method('setAbsoluteUriScheme')
            ->with('https');
        $this->mockUriBuilder->expects(self::once())
                    ->method('build');
        //        'redirectToUri method called with: ; and delay: 0; and status code: 301'
        //
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('with: ; and delay: 0; and status code: 301; redirectToUri method was called');
        $this->expectExceptionCode(7864378);

        $this->subject->handleEntityNotFoundError('redirectToPage, 1, 301');
    }

    /**
     * @test
     */
    public function handleEntityNotFoundErrorRedirectsToUriIfSignalSetsRedirectUri(): void
    {
        $config = 'foo';
        $expectedParams = [
            SI::CONFIG => GeneralUtility::trimExplode(',', $config),
            'requestArguments' => [],
            SI::ACTION_NAME => null
        ];
        $slotResult = [
            [SI::REDIRECT_URI => 'foo']
        ];

        $this->mockDispatcher->expects(self::once())
            ->method('dispatch')
            ->with(
                \get_class($this->subject),
                'handleEntityNotFoundError',
                [$expectedParams]
            )
            ->will(self::returnValue($slotResult));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('with: foo; and delay: 0; and status code: 303; redirectToUri method was called');
        $this->expectExceptionCode(7864378);

        $this->subject->handleEntityNotFoundError($config);
    }

    /**
     * @test
     */
    public function handleEntityNotFoundErrorRedirectsIfSignalSetsRedirect(): void
    {
        $config = 'foo';
        $expectedParams = [
            SI::CONFIG => GeneralUtility::trimExplode(',', $config),
            'requestArguments' => [],
            SI::ACTION_NAME => null
        ];
        $slotResult = [
            [
                SI::REDIRECT => [
                    SI::ACTION_NAME => 'foo',
                    SI::CONTROLLER_NAME => 'Bar',
                    SI::KEY_EXTENSION_NAME => 'baz',
                    SI::ARGUMENTS => ['foo'],
                    'pageUid' => 5,
                    'delay' => 1,
                    'statusCode' => 300
                ]
            ]
        ];
        $this->mockDispatcher->expects(self::once())
            ->method('dispatch')
            ->with(
                \get_class($this->subject),
                'handleEntityNotFoundError',
                [$expectedParams]
            )
            ->will(self::returnValue($slotResult));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('redirect method called');
        $this->expectExceptionCode(7864377);

        $this->subject->handleEntityNotFoundError($config);
    }

    /**
     * @test
     */
    public function processRequestCallsEntityNotFoundHandler(): void
    {
        $errorHandlingConfig = 'fooHandling';
        $controllerName = 'foo';
        $actionName = 'bar';
        $settings = [
            $controllerName => [
                $actionName => [
                    SI::ERROR_HANDLING => $errorHandlingConfig
                ]
            ]
        ];

        $this->subject->setSettings($settings);
        $mockResponse = $this->getMockBuilder(ResponseInterface::class)->getMock();
        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $mockRequest */
        $mockRequest = $this->getMockBuilder(Request::class)
            ->setMethods(['getControllerName', 'getControllerActionName'])->getMock();
        $mockRequest->expects(self::once())
            ->method('getControllerName')
            ->will(self::returnValue($controllerName));
        $mockRequest->expects(self::once())
            ->method('getControllerActionName')
            ->will(self::returnValue($actionName));

        $this->mockDispatcher->expects(self::once())->method('dispatch')->willReturn([0 => ['result']]);

        $this->expectException(TargetNotFoundException::class);
        $this->expectExceptionCode(1464634137);

        $this->subject->processRequest($mockRequest, $mockResponse);
    }

    /**
     * @test
     */
    public function handleEntityNotFoundErrorForwardsIfSignalSetsForward(): void
    {
        $config = 'foo';
        $expectedParams = [
            SI::CONFIG => GeneralUtility::trimExplode(',', $config),
            'requestArguments' => [],
            SI::ACTION_NAME => null
        ];
        $slotResult = [
            [
                SI::FORWARD => [
                    SI::ACTION_NAME => 'foo',
                    SI::CONTROLLER_NAME => 'Bar',
                    SI::KEY_EXTENSION_NAME => 'baz',
                    SI::ARGUMENTS => ['foo']]
            ]
        ];
        $this->mockDispatcher->expects(self::once())
            ->method('dispatch')
            ->with(
                get_class($this->subject),
                'handleEntityNotFoundError',
                [$expectedParams]
            )
            ->will(self::returnValue($slotResult));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('forward method called');
        $this->expectExceptionCode(7864376);

        $this->subject->handleEntityNotFoundError($config);
    }

    /**
     * @return MockObject
     */
    protected function getMockDispatcher(): MockObject
    {
        $mockDispatcher = $this->getMockBuilder(Dispatcher::class)
            ->disableOriginalConstructor()
            ->setMethods(['dispatch'])
            ->getMock();
        return $mockDispatcher;
    }
}
