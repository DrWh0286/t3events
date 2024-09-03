<?php

namespace DWenzel\T3events\Tests\Unit\DataProvider\Legend;

use DWenzel\T3events\DataProvider\Legend\AbstractPeriodDataProvider;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
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
class AbstractPeriodDataProviderTest extends UnitTestCase
{
    /**
     * @var AbstractPeriodDataProvider
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = $this->getMockForAbstractClass(AbstractPeriodDataProvider::class);
    }

    /**
     * @test
     */
    public function getAllLayerIdsReturnsLayerIdsFromClassConstant(): void
    {
        $expectedLayerIds = GeneralUtility::trimExplode(',', AbstractPeriodDataProvider::ALL_LAYERS, true);
        $this->assertSame(
            $expectedLayerIds,
            $this->subject->getAllLayerIds()
        );
    }

    /**
     * @test
     */
    public function getVisibleLayerIdsReturnsInitialValue(): void
    {
        $expectedLayerIds = GeneralUtility::trimExplode(',', AbstractPeriodDataProvider::VISIBLE_LAYERS, true);
        $this->assertSame(
            $expectedLayerIds,
            $this->subject->getVisibleLayerIds()
        );
    }

    /**
     * @test
     */
    public function getVisibleLayersReturnsLayersRespectingEndDate(): void
    {
        $expectedLayers = ['bar', 'baz'];

        /** @var AbstractPeriodDataProvider|MockObject subject */
        $subject = new class (true) extends AbstractPeriodDataProvider {
            const VISIBLE_LAYERS = 'foo,bar';
            const LAYERS_TO_HIDE = 'foo';
            const LAYERS_TO_SHOW = 'baz';
        };

        $this->assertEquals(
            $expectedLayers,
            $subject->getVisibleLayerIds()
        );
    }
}
