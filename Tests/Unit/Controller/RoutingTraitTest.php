<?php

namespace DWenzel\T3events\Tests\Unit\Controller;

/**
 * This file is part of the TYPO3 CMS project.
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * The TYPO3 project - inspiring people to share!
 */

use DWenzel\T3events\Controller\Routing\Route;
use DWenzel\T3events\Controller\Routing\RouterInterface;
use DWenzel\T3events\Controller\RoutingTrait;
use DWenzel\T3events\Controller\SignalInterface;
use DWenzel\T3events\Controller\SignalTrait;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use DWenzel\T3events\Utility\SettingsInterface as SI;

class MockSignalController implements SignalInterface
{
    use RoutingTrait;
    use SignalTrait;
}

/**
 * Class RouteTraitTest
 *
 * @package DWenzel\T3events\Tests\Unit\Controller
 */
class RoutingTraitTest extends UnitTestCase
{
    /**
     * @var RoutingTrait|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    /**
     * set up subject
     */
    protected function setUp(): void
    {
        $this->subject = new class () {
            use RoutingTrait;

            public function setRequest(Request $request)
            {
                $this->request = $request;
            }

            public function getRouter()
            {
                return $this->router;
            }
        };
    }

    /**
     * @test
     */
    public function routerCanBeInjected(): void
    {
        $router = $this->getMockRouter();
        $this->subject->injectRouter($router);

        $this->assertEquals(
            $router,
            $this->subject->getRouter()
        );
    }

    /**
     * @test
     */
    public function dispatchGetsIdentifierFromRequest(): void
    {
        $identifier = 'foo';

        $mockRequest = $this->getMockBuilder(Request::class)
            ->setMethods(['getControllerActionName', 'getControllerObjectName'])->getMock();

        $this->subject->setRequest($mockRequest);

        $mockRequest->expects($this->once())
            ->method('getControllerActionName');
        $mockRequest->expects($this->once())
            ->method('getControllerObjectName');
        $mockRoute = $this->getMockRoute(['getMethod'], [$identifier]);

        $mockRouter = $this->getMockRouter();
        $this->subject->injectRouter($mockRouter);
        $mockRouter->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue($mockRoute));

        $this->subject->dispatch();
    }

    /**
     * @test
     */
    public function dispatchGetsRouteForIdentifier(): void
    {
        $identifier = 'foo';
        $mockRoute = $this->getMockRoute([], [$identifier]);

        $mockRouter = $this->getMockRouter();

        $this->subject->injectRouter($mockRouter);
        $mockRouter->expects($this->once())
            ->method('getRoute')
            ->with($identifier)
            ->will($this->returnValue($mockRoute));

        $this->subject->dispatch(null, $identifier);
    }

    /**
     * @test
     */
    public function dispatchCallsMethodFromRoute(): void
    {
        $identifier = 'foo';
        $method = 'bam';
        $mockRoute = $this->getMockRoute(['getMethod'], [$identifier]);

        $this->subject = $this->getMockForTrait(
            RoutingTrait::class,
            [],
            '',
            true,
            true,
            true,
            [$method]
        );
        $mockRouter = $this->getMockRouter();

        $this->subject->injectRouter($mockRouter);
        $mockRouter->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue($mockRoute));
        $mockRoute->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));

        $this->subject->expects($this->once())
            ->method($method);
        $this->subject->dispatch(null, $identifier);
    }

    /**
     * @test
     */
    public function dispatchUsesArgumentsFromOrigin(): void
    {
        $arguments = ['foo' => 'bar'];
        $optionsFromRoute = [
            SI::ACTION_NAME => null,
            SI::CONTROLLER_NAME => null,
            SI::KEY_EXTENSION_NAME => null,
            SI::ARGUMENTS => null,
            'pageUid' => null,
            'delay' => 0,
            'statusCode' => 303,
        ];

        $expectedOptions = [
            null,
            null,
            null,
            $arguments,
            null,
            0,
            303
        ];
        $identifier = 'foo';
        $method = 'bam';
        $mockRoute = $this->getMockRoute(['getOptions', 'getMethod'], [$identifier]);

        $this->subject = $this->getMockForTrait(
            RoutingTrait::class,
            [],
            '',
            true,
            true,
            true,
            [$method]
        );

        $mockRouter = $this->getMockRouter();

        $this->subject->injectRouter($mockRouter);
        $mockRouter->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue($mockRoute));

        $mockRoute->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));
        $mockRoute->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($optionsFromRoute));

        $this->subject->expects($this->once())
            ->method($method)
            ->with(
                $expectedOptions[0],
                $expectedOptions[1],
                $expectedOptions[2],
                $expectedOptions[3],
                $expectedOptions[4],
                $expectedOptions[5],
                $expectedOptions[6]
            );

        $this->subject->dispatch($arguments, $identifier);
    }

    /**
     * @test
     */
    public function dispatchUsesDefaultArguments(): void
    {
        $defaultArguments = [
            'baz' => 'boom',
            'foo' => null
        ];
        $argumentsFromOrigin = ['foo' => 'bar'];
        $optionsFromRoute = [
            SI::ACTION_NAME => null,
            SI::CONTROLLER_NAME => null,
            SI::KEY_EXTENSION_NAME => null,
            SI::ARGUMENTS => $defaultArguments,
            'pageUid' => null,
            'delay' => 0,
            'statusCode' => 303,
        ];
        $expectedArguments = array_merge($defaultArguments, $argumentsFromOrigin);
        $expectedOptions = [
            null,
            null,
            null,
            $expectedArguments,
            null,
            0,
            303
        ];
        $identifier = 'foo';
        $method = 'bam';
        $mockRoute = $this->getMockRoute(['getOptions', 'getOption', 'getMethod'], [$identifier]);
        $this->subject = $this->getMockForTrait(
            RoutingTrait::class,
            [],
            '',
            true,
            true,
            true,
            [$method]
        );

        /** @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject $mockRouter */
        $mockRouter = $this->getMockRouter();

        $this->subject->injectRouter($mockRouter);
        $mockRouter->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue($mockRoute));

        $mockRoute->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue($method));
        $mockRoute->expects($this->once())
            ->method('getOptions')
            ->will($this->returnValue($optionsFromRoute));
        $mockRoute->expects($this->atLeastOnce())
            ->method('getOption')
            ->with(SI::ARGUMENTS)
            ->will($this->returnValue($defaultArguments));

        $this->subject->expects($this->once())
            ->method($method)
            ->with(
                $expectedOptions[0],
                $expectedOptions[1],
                $expectedOptions[2],
                $expectedOptions[3],
                $expectedOptions[4],
                $expectedOptions[5],
                $expectedOptions[6]
            );

        $this->subject->dispatch($argumentsFromOrigin, $identifier);
    }

    /**
     * @test
     */
    public function dispatchEmitsSignalDispatchBegin(): void
    {
        $this->subject = $this->getMockBuilder(MockSignalController::class)
            ->setMethods(['emitSignal'])->getMock();
        $arguments = ['foo'];
        $identifier = 'bar';
        $mockRoute = $this->getMockRoute([], [$identifier]);
        $mockRouter = $this->getMockRouter();
        $mockRouter->expects($this->once())
            ->method('getRoute')
            ->will($this->returnValue($mockRoute));

        $this->subject->injectRouter($mockRouter);

        $this->subject->expects($this->once())
            ->method('emitSignal')
            ->with(
                $this->equalTo(MockSignalController::class),
                $this->equalTo('dispatchBegin')
            );
        $this->subject->dispatch($arguments, $identifier);
    }

    /**
     * @param array $methods Methods to mock
     * @param array $constructorArguments
     * @return mixed
     */
    protected function getMockRoute(array $methods = [], array $constructorArguments = [])
    {
        return $this->getMockBuilder(Route::class)
            ->setConstructorArgs($constructorArguments)
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @param array $methods Methods to mock
     * @return RouterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockRouter(array $methods = ['getRoute'])
    {
        return $this->getMockBuilder(RouterInterface::class)
            ->setMethods($methods)->getMockForAbstractClass();
    }
}
