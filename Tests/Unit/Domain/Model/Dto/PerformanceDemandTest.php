<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Model\Dto;

/***************************************************************
 *  Copyright notice
 *  (c) 2012 Dirk Wenzel <wenzel@webfox01.de>, Agentur Webfox
 *  Michael Kasten <kasten@webfox01.de>, Agentur Webfox
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
use DWenzel\T3events\Domain\Model\Dto\PerformanceDemand;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for class \DWenzel\T3events\Domain\Model\Dto\PerformanceDemand.
 *
 * @author Dirk Wenzel <wenzel@webfox01.de>
 * @coversDefaultClass \DWenzel\T3events\Domain\Model\Dto\PerformanceDemand
 */
class PerformanceDemandTest extends UnitTestCase
{
    /**
     * @var \DWenzel\T3events\Domain\Model\Dto\PerformanceDemand
     */
    protected $subject;


    protected function setUp(): void
    {
        $this->subject = new PerformanceDemand();
    }

    /**
     * @test
     */
    public function getDateReturnsInitialNull(): void
    {
        $this->assertNull($this->subject->getDate());
    }

    /**
     * @test
     */
    public function setDateForDateTimeSetsDate(): void
    {
        $now = new \DateTime();
        $this->subject->setDate($now);

        $this->assertEquals($now, $this->subject->getDate());
    }

    /**
     * @test
     */
    public function getStartDateFieldForStringReturnsStartDateFieldConstant(): void
    {
        $this->assertSame(
            PerformanceDemand::START_DATE_FIELD,
            $this->subject->getStartDateField()
        );
    }

    /**
     * @test
     */
    public function getEndDateFieldForStringReturnsEndDateFieldConstant(): void
    {
        $this->assertSame(
            PerformanceDemand::END_DATE_FIELD,
            $this->subject->getEndDateField()
        );
    }

    /**
     * @test
     */
    public function getStatusFieldForStringReturnsStatusFieldConstant(): void
    {
        $this->assertSame(
            PerformanceDemand::STATUS_FIELD,
            $this->subject->getStatusField()
        );
    }

    /**
     * @test
     */
    public function getCategoryFieldForStringReturnsCategoryFieldConstant(): void
    {
        $this->assertSame(
            PerformanceDemand::CATEGORY_FIELD,
            $this->subject->getCategoryField()
        );
    }

    /**
     * @test
     */
    public function excludeSelectedStatusForBoolCanBeSet(): void
    {
        $this->subject->setExcludeSelectedStatuses(true);
        $this->assertTrue(
            $this->subject->isExcludeSelectedStatuses()
        );
    }

    /**
     * @test
     */
    public function getEventLocationFieldReturnsClassConstant(): void
    {
        $this->assertSame(
            PerformanceDemand::EVENT_LOCATION_FIELD,
            $this->subject->getEventLocationField()
        );
    }

    /**
     * @test
     */
    public function getAudienceFieldReturnsClassConstant(): void
    {
        $this->assertSame(
            PerformanceDemand::AUDIENCE_FIELD,
            $this->subject->getAudienceField()
        );
    }

    /**
     * @test
     */
    public function getGenreFieldReturnsClassConstant(): void
    {
        $this->assertSame(
            PerformanceDemand::GENRE_FIELD,
            $this->subject->getGenreField()
        );
    }

    /**
     * @test
     */
    public function getVenueFieldReturnsClassConstant(): void
    {
        $this->assertSame(
            PerformanceDemand::VENUE_FIELD,
            $this->subject->getVenueField()
        );
    }

    /**
     * @test
     */
    public function getEventTypeFieldReturnsClassConstant(): void
    {
        $this->assertSame(
            PerformanceDemand::EVENT_TYPE_FIELD,
            $this->subject->getEventTypeField()
        );
    }
}
