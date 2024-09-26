<?php

namespace DWenzel\T3events\Tests\Unit\Controller;

use DWenzel\T3events\Controller\ModuleDataTrait;
use DWenzel\T3events\Domain\Model\Dto\ModuleData;
use DWenzel\T3events\Service\ModuleDataStorageService;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Dirk Wenzel <dirk.wenzel@cps-it.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class ModuleDataTraitTest extends TestCase
{
    use \DWenzel\T3events\Tests\Unit\Object\MockObjectManagerTrait;

    /**
     * @var ModuleDataTrait
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new class () {
            use ModuleDataTrait;

            /**
             * @return array
             */
            public function mergeSettings()
            {
                return ['foo'];
            }

            /**
             * @return string
             */
            public function getModuleKey()
            {
                return 'foo';
            }

            /**
             * Forwards the request to another action and / or controller.
             * Request is directly transfered to the other action / controller
             * without the need for a new request.
             *
             * @param string $actionName Name of the action to forward to
             * @param string $controllerName Unqualified object name of the controller to forward to. If not specified, the current controller is used.
             * @param string $extensionName Name of the extension containing the controller to forward to. If not specified, the current extension is assumed.
             * @param array $arguments Arguments to pass to the target action
             * @return void
             */
            public function forward(
                $actionName,
                $controllerName = null,
                $extensionName = null,
                array $arguments = null
            ): void {
                throw new class ('forward method called', 872634598237456) extends \Exception {
                };
            }

            /**
             * @return ModuleDataStorageService
             */
            public function getModuleDataStorageService(): ModuleDataStorageService
            {
                return $this->moduleDataStorageService;
            }

            public function getSettings()
            {
                return $this->settings;
            }
        };
    }

    /**
     * @test
     */
    public function moduleDataStorageServiceCanBeInjected(): void
    {
        $mockService = $this->getMockModuleDataStorageService();


        $this->subject->injectModuleDataStorageService($mockService);
        $this->assertSame(
            $mockService,
            $this->subject->getModuleDataStorageService()
        );
    }

    /**
     * @test
     */
    public function resetActionResetsAndPersistsModuleData(): void
    {
        $moduleKey = 'foo';

        $classes = [
            ModuleData::class => $mockModuleData = $this->getMockBuilder(ModuleData::class)->getMock()
        ];

        GeneralUtility::setContainer(new class ($classes) implements ContainerInterface {
            public function __construct(private array $classes)
            {
            }

            public function get(string $id)
            {
                if (isset($this->classes[$id])) {
                    return $this->classes[$id];
                }

                return null;
            }

            public function has(string $id): bool
            {
                if (isset($this->classes[$id])) {
                    return true;
                }

                return false;
            }
        });


        $moduleDataStorageService = $this->getMockModuleDataStorageService(['persistModuleData']);
        $this->subject->injectModuleDataStorageService($moduleDataStorageService);

        $moduleDataStorageService->expects($this->once())
            ->method('persistModuleData')
            ->with($mockModuleData, $moduleKey);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(872634598237456);
        $this->expectExceptionMessage('forward method called');

        $this->subject->resetAction();
    }

    /**
     * @test
     */
    public function initializeActionMergesSettings(): void
    {
        $expectedSettings = ['foo'];

        $this->subject->initializeAction();
        $this->assertSame(
            $expectedSettings,
            $this->subject->getSettings()
        );
    }

    /**
     * @param array $methods Methods to mock
     * @return ModuleDataStorageService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockModuleDataStorageService(array $methods = [])
    {
        return $this->getMockBuilder(ModuleDataStorageService::class)
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @test
     */
    public function getModuleDataInitiallyReturnsNull(): void
    {
        $this->assertNull(
            $this->subject->getModuleData()
        );
    }

    /**
     * @test
     */
    public function moduleDataCanBeSet(): void
    {
        $moduleData = $this->getMockBuilder(ModuleData::class)->getMock();
        $this->subject->setModuleData($moduleData);

        $this->assertSame(
            $moduleData,
            $this->subject->getModuleData()
        );
    }
}
