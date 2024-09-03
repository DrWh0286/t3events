<?php

namespace DWenzel\T3events\Tests\Unit\Configuration;

use DWenzel\T3events\Configuration\PeriodConstraintLegend;
use DWenzel\T3events\DataProvider\Legend\LayeredLegendDataProviderInterface;
use DWenzel\T3events\DataProvider\Legend\PeriodDataProviderFactory;
use DWenzel\T3events\InvalidConfigurationException;
use DWenzel\T3events\MissingFileException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
class PeriodConstraintLegendTest extends TestCase
{
    /**
     * @var PeriodConstraintLegend|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $subject;

    /**
     * @var PeriodDataProviderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $periodDataProviderFactory;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new PeriodConstraintLegend();

        $this->periodDataProviderFactory = $this->getMockBuilder(PeriodDataProviderFactory::class)
           ->getMock();

        $classes = [
            PeriodDataProviderFactory::class => $this->periodDataProviderFactory
        ];

        GeneralUtility::setContainer(new class ($classes) implements ContainerInterface {
            public function __construct(private readonly array $classes)
            {
            }

            public function get(string $id)
            {
                if (array_key_exists($id, $this->classes)) {
                    return $this->classes[$id];
                }
            }

            public function has(string $id)
            {
                if (array_key_exists($id, $this->classes)) {
                    return true;
                }
            }
        });

    }

    /**
     * @throws InvalidConfigurationException
     */
    public function testInitializeThrowsMissingFileException(): void
    {
        $params = [
            PeriodConstraintLegend::PARAM_XML_FILE_PATH => 'fooPath'
        ];
        $this->expectException(MissingFileException::class);
        $this->expectExceptionCode(1462887081);
        $this->subject->initialize($params);
    }

    /**
     * @test
     * @throws \DWenzel\T3events\MissingFileException
     */
    public function initializeSetsDataProvider(): void
    {
        $this->subject = new PeriodConstraintLegend();

        $params = [PeriodConstraintLegend::PARAM_XML_FILE_PATH => __DIR__ . '/Fixture/dummy.xml'];

        $mockDataProvider = $this->getMockLayeredLegendDataProvider();

        $this->periodDataProviderFactory->expects($this->once())
            ->method('get')
            ->willReturn($mockDataProvider);

        $this->subject->initialize($params);

    }

    /**
     * @test
     */
    public function getDataProviderFactoryReturnsFactory(): void
    {
        $this->assertInstanceOf(
            PeriodDataProviderFactory::class,
            $this->subject->getDataProviderFactory()
        );
    }

    /**
     * @test
     * @todo: Check test!
     */
    public function renderUpdatesLayers(): void
    {
        $params = [PeriodConstraintLegend::PARAM_XML_FILE_PATH => __DIR__ . '/Fixture/dummy.xml'];
        $allLayers = ['foo'];
        $visibleLayers = ['bar'];
        $GLOBALS['LANG'] = $this->createMock(LanguageService::class);

        $mockDataProvider = $this->getMockLayeredLegendDataProvider(['getAllLayerIds', 'getVisibleLayerIds']);

        $this->periodDataProviderFactory->expects($this->once())
            ->method('get')
            ->willReturn($mockDataProvider);

        $mockDataProvider->expects($this->once())
            ->method('getAllLayerIds')
            ->willReturn($allLayers);
        $mockDataProvider->expects($this->once())
            ->method('getVisibleLayerIds')
            ->willReturn($visibleLayers);

        //        $this->subject->expects($this->once())
        //            ->method('hideElements')
        //            ->with($allLayers);
        //        $this->subject->expects($this->once())
        //            ->method('showElements')
        //            ->with($visibleLayers);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->render($params);
    }

    /**
     * @test
     */
    public function renderSetsLabels(): void
    {
        $this->subject = $this->getMockBuilder(PeriodConstraintLegend::class)
            ->onlyMethods(
                ['initialize', 'updateLayers', 'saveXML', 'getLanguageService', 'replaceNodeText']
            )
            ->getMock();
        $params = ['foo'];

        $mockLanguageService = $this->getMockBuilder(LanguageService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['sl'])
            ->getMock();
        $this->subject
            ->method('getLanguageService')
            ->willReturn($mockLanguageService);
        $mockLanguageService->expects($this->exactly(2))
            ->method('sL')
            ->withConsecutive(
                [PeriodConstraintLegend::LANGUAGE_FILE . PeriodConstraintLegend::START_POINT_KEY],
                [PeriodConstraintLegend::LANGUAGE_FILE . PeriodConstraintLegend::END_POINT_KEY]
            )
            ->willReturn('foo');

        $this->subject->expects($this->exactly(2))
            ->method('replaceNodeText')
            ->withConsecutive(
                [PeriodConstraintLegend::START_TEXT_LAYER_ID, 'foo'],
                [PeriodConstraintLegend::END_TEXT_LAYER_ID, 'foo']
            );
        $this->subject->render($params);
    }

    /**
     * @param array $methods Methods to mock
     */
    protected function getMockLayeredLegendDataProvider(array $methods = []): LayeredLegendDataProviderInterface|MockObject
    {
        return $this->getMockBuilder(LayeredLegendDataProviderInterface::class)
            ->onlyMethods($methods)
            ->getMockForAbstractClass();
    }
}
