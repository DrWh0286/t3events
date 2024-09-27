<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Factory\Dto;

use DWenzel\T3events\Domain\Factory\Dto\EventDemandFactory;
use DWenzel\T3events\Domain\Model\Dto\EventDemand;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use DWenzel\T3events\Tests\Unit\Object\MockObjectManagerTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

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
class EventDemandFactoryTest extends UnitTestCase
{
    use MockObjectManagerTrait;

    /**
     * @var EventDemandFactory
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new EventDemandFactory();

    }

    /**
     * @test
     */
    public function createFromSettingsReturnsEventDemand(): void
    {
        $this->assertEquals(
            new EventDemand(),
            $this->subject->createFromSettings([])
        );
    }

    /**
     * @return array
     */
    public function settablePropertiesDataProvider()
    {
        /** propertyName, $settingsValue, $expectedValue */
        return [
            [SI::LEGACY_KEY_GENRE, '1,2', '1,2'],
            ['venue', '3,4', '3,4'],
            ['eventType', '5,6', '5,6'],
            ['categories', '7,8', '7,8'],
            ['categoryConjunction', 'and', 'and'],
            ['limit', '50', 50],
            ['offset', '10', 10],
            ['uidList', '7,8,9', '7,8,9'],
            ['storagePages', '7,8,9', '7,8,9'],
            ['order', 'foo|bar,baz|asc', 'foo|bar,baz|asc']
        ];
    }

    /**
     * @test
     * @dataProvider settablePropertiesDataProvider
     * @param string $propertyName
     * @param string|int $settingsValue
     */
    public function createFromSettingsSetsSettableProperties($propertyName, $settingsValue, mixed $expectedValue): void
    {
        $settings = [
            $propertyName => $settingsValue
        ];

        $createdDemand = $this->subject->createFromSettings($settings);
        $this->assertSame(
            $expectedValue,
            $createdDemand->_getProperty($propertyName)
        );
    }

    /**
     * @return array
     */
    public function mappedPropertiesDataProvider()
    {
        /** settingsKey, propertyName, $settingsValue, $expectedValue */
        return [
            [SI::GENRES, SI::LEGACY_KEY_GENRE, '1,2', '1,2'],
            [SI::VENUES, 'venue', '3,4', '3,4'],
            ['eventType', 'eventType', '5,6', '5,6'],
            ['maxItems', 'limit', '50', 50],
        ];
    }

    /**
     * @test
     * @dataProvider mappedPropertiesDataProvider
     * @param string $settingsKey
     * @param string $propertyName
     * @param string|int $settingsValue
     */
    public function createFromSettingsSetsMappedProperties($settingsKey, $propertyName, $settingsValue, mixed $expectedValue): void
    {
        $settings = [
            $settingsKey => $settingsValue
        ];

        $createdDemand = $this->subject->createFromSettings($settings);
        $this->assertSame(
            $expectedValue,
            $createdDemand->_getProperty($propertyName)
        );
    }

    /**
     * @return array
     */
    public function skippedPropertiesDataProvider()
    {
        return [
            ['foo', ''],
            ['periodType', 'bar'],
            ['periodStart', 'bar'],
            ['periodDuration', 'bar'],
            ['search', 'bar']
        ];
    }

    /**
     * @test
     * @dataProvider skippedPropertiesDataProvider
     * @param $propertyName
     * @param $propertyValue
     */
    public function createFromSettingsDoesNotSetSkippedValues($propertyName, $propertyValue): void
    {
        $settings = [
            $propertyName => $propertyValue
        ];

        $createdDemand = $this->subject->createFromSettings($settings);

        $this->assertEquals(
            new EventDemand(),
            $createdDemand
        );
    }

    /**
     * @test
     */
    public function createFromSettingsSetsPeriodTypeForSpecificPeriod(): void
    {
        $periodType = 'foo';
        $settings = [
            'period' => SI::SPECIFIC,
            'periodType' => $periodType
        ];

        $createdDemand = $this->subject->createFromSettings($settings);

        $this->assertSame(
            $periodType,
            $createdDemand->getPeriodType()
        );
    }

    /**
     * @test
     */
    public function createFromSettingsSetsPeriodStartAndDurationIfPeriodTypeIsNotByDate(): void
    {
        $periodType = 'fooPeriodType-notByDate';
        $periodStart = '30';
        $periodDuration = '20';
        $settings = [
            'periodType' => $periodType,
            'periodStart' => $periodStart,
            'periodDuration' => $periodDuration
        ];

        $createdDemand = $this->subject->createFromSettings($settings);

        $this->assertSame(
            (int)$periodStart,
            $createdDemand->getPeriodStart()
        );

        $this->assertSame(
            (int)$periodDuration,
            $createdDemand->getPeriodDuration()
        );
    }

    /**
     * @test
     */
    public function createFromSettingsSetsStartDateForPeriodTypeByDate(): void
    {
        $periodType = 'byDate';
        $startDate = '2012-10-10';
        $settings = [
            'periodType' => $periodType,
            'periodStartDate' => $startDate
        ];

        $createdDemand = $this->subject->createFromSettings($settings);

        $timeZone = new \DateTimeZone(date_default_timezone_get());
        $expectedStartDate = new \DateTime($startDate, $timeZone);

        $this->assertEquals(
            $expectedStartDate,
            $createdDemand->_getProperty(SI::START_DATE)
        );
    }

    /**
     * @test
     */
    public function createFromSettingsSetsEndDateForPeriodTypeByDate(): void
    {
        $periodType = 'byDate';
        $endDate = '2012-10-10';
        $settings = [
            'periodType' => $periodType,
            'periodEndDate' => $endDate
        ];

        $createdDemand = $this->subject->createFromSettings($settings);

        $timeZone = new \DateTimeZone(date_default_timezone_get());
        $expectedStartDate = new \DateTime($endDate, $timeZone);

        $this->assertEquals(
            $expectedStartDate,
            $createdDemand->_getProperty(SI::END_DATE)
        );
    }

    /**
     * @return EventDemand|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockEventDemand()
    {
        /** @var EventDemand|\PHPUnit_Framework_MockObject_MockObject $mockDemand */
        $mockDemand = $this->getMockBuilder(EventDemand::class)
            ->setMethods([])->getMock();
        return $mockDemand;
    }
}
