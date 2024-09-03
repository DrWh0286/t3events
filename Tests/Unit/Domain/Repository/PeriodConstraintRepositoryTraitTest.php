<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *  (c) 2012 Dirk Wenzel <t3events@gmx.de>,
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
use DWenzel\T3events\Domain\Model\Dto\PeriodAwareDemandInterface;
use DWenzel\T3events\Domain\Repository\PeriodConstraintRepositoryTrait;
use DWenzel\T3events\Tests\Unit\Domain\Model\Dto\MockDemandTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use DWenzel\T3events\Utility\SettingsInterface as SI;

/**
 * Test case for class \DWenzel\T3events\Domain\Repository\PeriodConstraintRepositoryTrait.
 */
class PeriodConstraintRepositoryTraitTest extends UnitTestCase
{
    use MockDemandTrait;
    use MockQueryTrait;
    /**
     * mock start date field
     */
    public const START_DATE_FIELD = 'foo';
    /**
     * mock end date field
     */
    public const END_DATE_FIELD = 'bar';

    /**
     * @var PeriodConstraintRepositoryTrait|MockObject
     */
    protected $subject;

    /**
     * @var QueryInterface|MockObject
     */
    protected $query;

    /**
     * @var PeriodAwareDemandInterface|MockObject
     */
    protected $demand;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = $this->getMockForTrait(
            PeriodConstraintRepositoryTrait::class
        );
        $this->query = $this->getMockQuery();
        $this->demand = $this->getMockPeriodAwareDemand(
            [
                'getPeriod',
                'setPeriod',
                'getPeriodStart',
                'setPeriodStart',
                'getPeriodType',
                'setPeriodType',
                'getPeriodDuration',
                'setPeriodDuration',
                'getDate',
                'setDate',
                'getStartDate',
                'setStartDate',
                'getEndDate',
                'setEndDate',
                'getStartDateField',
                'getEndDateField',
                'isRespectEndDate',
                'setRespectEndDate',
            ]
        );
    }

    /**
     * @test
     */
    public function createPeriodConstraintsInitiallyReturnsEmptyArray(): void
    {
        $demand = $this->getMockPeriodAwareDemand();
        $this->assertSame(
            [],
            $this->subject->createPeriodConstraints(
                $this->query,
                $demand
            )
        );
    }

    /**
     * @test
     */
    public function createPeriodConstraintsAddsConstraintForFuture(): void
    {
        $period = SI::FUTURE_ONLY;
        $timezone = new \DateTimeZone(date_default_timezone_get());
        $startDate = new \DateTime('today', $timezone);

        $this->demand->expects($this->any())
            ->method('getPeriod')
            ->will($this->returnValue($period));
        $this->demand->expects($this->once())
            ->method('getStartDateField')
            ->will($this->returnValue(self::START_DATE_FIELD));
        $this->query->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with(self::START_DATE_FIELD, $startDate->getTimestamp());

        $this->subject->createPeriodConstraints($this->query, $this->demand);
    }

    /**
     * @test
     */
    public function createPeriodConstraintsAddsConstraintForPast(): void
    {
        $period = SI::PAST_ONLY;
        $timezone = new \DateTimeZone(date_default_timezone_get());
        $startDate = new \DateTime('today', $timezone);

        $this->demand->expects($this->any())
            ->method('getPeriod')
            ->will($this->returnValue($period));
        $this->demand->expects($this->once())
            ->method('getStartDateField')
            ->will($this->returnValue(self::START_DATE_FIELD));
        $this->query->expects($this->once())
            ->method('lessThanOrEqual')
            ->with(self::START_DATE_FIELD, $startDate->getTimestamp());

        $this->subject->createPeriodConstraints($this->query, $this->demand);
    }

    /**
     * Data provider for specific period
     */
    public function startDateByPeriodType()
    {
        $timeZone = new \DateTimeZone(date_default_timezone_get());
        $defaultStartDate = new \DateTime(
            'NOW',
            $timeZone
        );
        $defaultStartDate->setTime(0, 0);
        $year = $defaultStartDate->format('Y');

        return [
            'by day' => ['byDay', $defaultStartDate],
            'by month' => ['byMonth', new \DateTime('first day of this month  00:00:00', $timeZone)],
            'by year' => ['byYear', new \DateTime($year . '-01-01', $timeZone)],
        ];
    }

    /**
     * @test
     * @dataProvider startDateByPeriodType
     * @param string $periodType
     * @param \DateTime $expectedStartDate
     */
    public function createPeriodConstraintsSetsStartDateForSpecificPeriod($periodType, $expectedStartDate): void
    {
        $period = SI::SPECIFIC;
        $periodStart = 0;

        $this->demand->expects($this->any())
            ->method('getPeriodType')
            ->will($this->returnValue($periodType));
        $this->demand->expects($this->any())
            ->method('getPeriod')
            ->will($this->returnValue($period));
        $this->demand->expects($this->any())
            ->method('getPeriodStart')
            ->will($this->returnValue($periodStart));
        $this->demand->expects($this->any())
            ->method('getStartDateField')
            ->will($this->returnValue(self::START_DATE_FIELD));
        $this->query->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with(self::START_DATE_FIELD, $expectedStartDate->getTimestamp());

        $this->subject->createPeriodConstraints($this->query, $this->demand);
    }

    /**
     * @test
     */
    public function createPeriodConstraintsSetsStartDateForSpecificPeriodFromDemand(): void
    {
        $period = SI::SPECIFIC;
        $periodType = 'byDate';
        $expectedStartDate = new \DateTime('@' . 78910);
        $periodStart = 0;

        $this->demand->expects($this->any())
            ->method('getPeriodType')
            ->will($this->returnValue($periodType));
        $this->demand->expects($this->any())
            ->method('getPeriod')
            ->will($this->returnValue($period));
        $this->demand->expects($this->any())
            ->method('getPeriodStart')
            ->will($this->returnValue($periodStart));
        $this->demand->expects($this->any())
            ->method('getStartDate')
            ->will($this->returnValue($expectedStartDate));
        $this->demand->expects($this->any())
            ->method('getStartDateField')
            ->will($this->returnValue(self::START_DATE_FIELD));
        $this->query->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with(self::START_DATE_FIELD, $expectedStartDate->getTimestamp());

        $this->subject->createPeriodConstraints($this->query, $this->demand);
    }

    /**
     * @test
     */
    public function createPeriodConstraintsSetsEndDateForSpecificPeriodFromDemand(): void
    {
        $period = SI::SPECIFIC;
        $periodType = 'byDate';
        $expectedEndDate = new \DateTime('@' . 78910);
        $periodStart = 0;

        $this->demand->expects($this->any())
            ->method('getPeriodType')
            ->will($this->returnValue($periodType));
        $this->demand->expects($this->any())
            ->method('getPeriod')
            ->will($this->returnValue($period));
        $this->demand->expects($this->any())
            ->method('getPeriodStart')
            ->will($this->returnValue($periodStart));
        $this->demand->expects($this->any())
            ->method('getEndDate')
            ->will($this->returnValue($expectedEndDate));
        $this->demand->expects($this->any())
            ->method('getStartDateField')
            ->will($this->returnValue(self::START_DATE_FIELD));
        $this->query->expects($this->once())
            ->method('lessThanOrEqual')
            ->with(self::START_DATE_FIELD, $expectedEndDate->getTimestamp());

        $this->subject->createPeriodConstraints($this->query, $this->demand);
    }

    /**
     * @test
     */
    public function createPeriodConstraintsGetsRespectEndDateFromDemand(): void
    {
        $this->demand->expects($this->once())
            ->method('isRespectEndDate');

        $this->subject->createPeriodConstraints($this->query, $this->demand);
    }

    /**
     * @test
     */
    public function createPeriodConstraintsAddsConstraintForFutureRespectingEndDate(): void
    {
        $period = SI::FUTURE_ONLY;
        $timezone = new \DateTimeZone(date_default_timezone_get());
        $startDate = new \DateTime('today', $timezone);
        $endDate = clone $startDate;

        $this->demand->expects($this->any())
            ->method('getPeriod')
            ->will($this->returnValue($period));
        $this->demand->expects($this->once())
            ->method('isRespectEndDate')
            ->will($this->returnValue(true));
        $this->demand->expects($this->once())
            ->method('getStartDateField')
            ->will($this->returnValue(self::START_DATE_FIELD));
        $this->demand->expects($this->once())
            ->method('getEndDateField')
            ->will($this->returnValue(self::END_DATE_FIELD));
        $this->query->expects($this->exactly(2))
            ->method('greaterThanOrEqual')
            ->withConsecutive(
                [self::START_DATE_FIELD, $endDate->getTimestamp()],
                [self::END_DATE_FIELD, $startDate->getTimestamp()]
            );
        $this->query->expects($this->once())
            ->method('logicalOr')
            ->with();

        $this->subject->createPeriodConstraints($this->query, $this->demand);
    }

    /**
     * @test
     */
    public function createPeriodConstraintsAddsConstraintForPastRespectingEndDate(): void
    {
        $period = SI::PAST_ONLY;
        $timezone = new \DateTimeZone(date_default_timezone_get());
        $startDate = new \DateTime('today', $timezone);
        $endDate = clone $startDate;

        $this->demand->expects($this->any())
            ->method('getPeriod')
            ->will($this->returnValue($period));
        $this->demand->expects($this->once())
            ->method('isRespectEndDate')
            ->will($this->returnValue(true));
        $this->demand->expects($this->once())
            ->method('getStartDateField')
            ->will($this->returnValue(self::START_DATE_FIELD));
        $this->demand->expects($this->once())
            ->method('getEndDateField')
            ->will($this->returnValue(self::END_DATE_FIELD));
        $this->query->expects($this->exactly(2))
            ->method('lessThanOrEqual')
            ->withConsecutive(
                [self::END_DATE_FIELD, $endDate->getTimestamp()],
                [self::START_DATE_FIELD, $startDate->getTimestamp()]
            );
        $this->query->expects($this->once())
            ->method('logicalAnd')
            ->with();

        $this->subject->createPeriodConstraints($this->query, $this->demand);
    }

    /**
     * @test
     */
    public function createPeriodConstraintsAddsConstraintForSpecificRespectingEndDate(): void
    {
        $startDate = new \DateTime('@' . 7000);
        $endDate = new \DateTime('@' . 8000);

        $period = SI::SPECIFIC;
        $periodType = 'byDate';

        $this->demand->expects($this->any())
            ->method('getPeriod')
            ->will($this->returnValue($period));
        $this->demand->expects($this->any())
            ->method('getPeriodType')
            ->will($this->returnValue($periodType));
        $this->demand->expects($this->once())
            ->method('isRespectEndDate')
            ->will($this->returnValue(true));
        $this->demand->expects($this->once())
            ->method('getStartDateField')
            ->will($this->returnValue(self::START_DATE_FIELD));
        $this->demand->expects($this->once())
            ->method('getEndDateField')
            ->will($this->returnValue(self::END_DATE_FIELD));
        $this->demand->expects($this->any())
            ->method('getStartDate')
            ->will($this->returnValue($startDate));
        $this->demand->expects($this->any())
            ->method('getEndDate')
            ->will($this->returnValue($endDate));
        $this->query->expects($this->exactly(2))
            ->method('greaterThanOrEqual')
            ->withConsecutive(
                [self::END_DATE_FIELD, $startDate->getTimestamp()],
                [self::START_DATE_FIELD, $startDate->getTimestamp()]
            );
        $this->query->expects($this->exactly(3))
            ->method('lessThanOrEqual')
            ->withConsecutive(
                [self::END_DATE_FIELD, $endDate->getTimestamp()],
                [self::START_DATE_FIELD, $startDate->getTimestamp()],
                [self::END_DATE_FIELD, $endDate->getTimestamp()]
            );
        $this->query->expects($this->exactly(2))
            ->method('logicalAnd')
            ->with();

        $this->subject->createPeriodConstraints($this->query, $this->demand);
    }

    /**
     * @param array $methods Methods to mock
     * @return PeriodAwareDemandInterface|MockObject
     */
    protected function getMockPeriodAwareDemand(array $methods = [])
    {
        return $this->getMockBuilder(PeriodAwareDemandInterface::class)
            ->setMethods($methods)
            ->getMockForAbstractClass();
    }
}
