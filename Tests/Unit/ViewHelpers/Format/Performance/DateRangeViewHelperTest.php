<?php

namespace DWenzel\T3events\Tests\Unit\ViewHelpers\Format\Performance;

/**
 * This file is part of the "Events" project.
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

use DWenzel\T3events\Domain\Model\Event;
use DWenzel\T3events\Domain\Model\Performance;
use DWenzel\T3events\ViewHelpers\Format\Performance\DateRangeViewHelper;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class DateRangeViewHelperTest
 */
class DateRangeViewHelperTest extends UnitTestCase
{
    /**
     * @var DateRangeViewHelper|\PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = $this->getAccessibleMock(
            DateRangeViewHelper::class, ['initialize']
        );
    }

    /**
     * arguments data provider
     */
    public function argumentsDataProvider()
    {
        $yesterdaysDate = new \DateTime('yesterday');
        $todaysDate = new \DateTime('today');
        $tomorrowsDate = new \DateTime('tomorrow');

        $performanceWithStartDateOnly = new Performance();
        $performanceWithStartDateOnly->setDate($yesterdaysDate);

        $performanceWithDifferentStartAndEndDate = new Performance();
        $performanceWithDifferentStartAndEndDate->setDate($yesterdaysDate);
        $performanceWithDifferentStartAndEndDate->setEndDate($todaysDate);

        $performanceWithSameStartAndEndDate = new Performance();
        $performanceWithSameStartAndEndDate->setDate($todaysDate);
        $performanceWithSameStartAndEndDate->setEndDate($todaysDate);


        $customGlue = ' till ';

        return [
            // performance with start date only, default date format and glue
            [
                // arguments
                [
                    'performance' => $performanceWithStartDateOnly
                ],
                // expected
                $yesterdaysDate->format(DateRangeViewHelper::DEFAULT_DATE_FORMAT)
            ],// performance with different start and end date only, default date format and glue
            [
                // arguments
                [
                    'performance' => $performanceWithDifferentStartAndEndDate
                ],
                // expected
                $yesterdaysDate->format(DateRangeViewHelper::DEFAULT_DATE_FORMAT)
                . DateRangeViewHelper::DEFAULT_GLUE
                . $todaysDate->format(DateRangeViewHelper::DEFAULT_DATE_FORMAT)
            ],// performance with different start and end date only, custom start format and glue
            [
                // arguments
                [
                    'performance' => $performanceWithDifferentStartAndEndDate,
                    'glue' => $customGlue
                ],
                // expected
                $yesterdaysDate->format(DateRangeViewHelper::DEFAULT_DATE_FORMAT)
                . $customGlue
                . $todaysDate->format(DateRangeViewHelper::DEFAULT_DATE_FORMAT)
            ],// performance with same start and end date, default date format and glue
            [
                // arguments
                [
                    'performance' => $performanceWithSameStartAndEndDate
                ],
                // expected
                $todaysDate->format(DateRangeViewHelper::DEFAULT_DATE_FORMAT)
            ],
        ];
    }

    /**
     * @test
     */
    public function initializeArgumentsRegistersArguments(): void
    {
        $this->subject = $this->getMockBuilder(DateRangeViewHelper::class)
            ->setMethods(['registerArgument'])->getMock();

        $this->subject->expects($this->exactly(5))
            ->method('registerArgument')
            ->withConsecutive(
                ['performance', Performance::class, DateRangeViewHelper::ARGUMENT_PERFORMANCE_DESCRIPTION, true, null],
                ['format', 'string', DateRangeViewHelper::ARGUMENT_FORMAT_DESCRIPTION, false, 'd.m.Y'],
                ['startFormat', 'string', DateRangeViewHelper::ARGUMENT_STARTFORMAT_DESCRIPTION, false, 'd.m.Y'],
                ['endFormat', 'string', DateRangeViewHelper::ARGUMENT_ENDFORMAT_DESCRIPTION, false, 'd.m.Y'],
                ['glue', 'string', DateRangeViewHelper::ARGUMENT_GLUE_DESCRIPTION, false, ' - ']
            );
        $this->subject->initializeArguments();
    }

    /**
     * @test
     * @dataProvider argumentsDataProvider
     * @param array $arguments
     * @param $expected
     */
    public function renderReturnsExpectedString($arguments, $expected): void
    {
        $this->subject->setArguments($arguments);
        $this->subject->expects($this->once())->method('initialize');

        $this->assertSame(
            $expected,
            $this->subject->render()
        );
    }

    /**
     * @test
     */
    public function formatWithPercentageThrowsAnException(): void
    {
        $yesterdaysDate = new \DateTime('yesterday');
        $todaysDate = new \DateTime('today');
        $tomorrowsDate = new \DateTime('tomorrow');

        $performanceYesterday = new Performance();
        $performanceToday = new Performance();
        $performanceTomorrow = new Performance();
        $performanceYesterday->setDate($yesterdaysDate);
        $performanceToday->setDate($todaysDate);
        $performanceTomorrow->setDate($tomorrowsDate);

        $eventWithOnePerformance = new Event();
        $eventWithOnePerformance->addPerformance($performanceYesterday);

        $eventWithTwoPerformances = new Event();
        $eventWithTwoPerformances->addPerformance($performanceYesterday);
        $eventWithTwoPerformances->addPerformance($performanceToday);

        $eventWithThreePerformances = new Event();
        $eventWithThreePerformances->addPerformance($performanceYesterday);
        $eventWithThreePerformances->addPerformance($performanceToday);

        $customFormatRequiringStrftime = '%A %e %B %Y';

        $performanceWithStartDateOnly = new Performance();
        $performanceWithStartDateOnly->setDate($yesterdaysDate);

        $arguments = [
            'performance' => $performanceWithStartDateOnly,
            'event' => $eventWithThreePerformances,
            'startFormat' => $customFormatRequiringStrftime,
            'endFormat' => $customFormatRequiringStrftime,
            'glue' => 'till',
        ];

        $this->subject->setArguments($arguments);
        $this->subject->expects($this->once())->method('initialize');

        $this->expectException(\RuntimeException::class);
        $this->subject->render();
    }
}
