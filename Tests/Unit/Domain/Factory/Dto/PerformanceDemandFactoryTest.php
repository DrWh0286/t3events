<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Factory\Dto;

use DWenzel\T3events\Domain\Factory\Dto\PerformanceDemandFactory;
use DWenzel\T3events\Domain\Model\Dto\PerformanceDemand;
use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Tests\Unit\Object\MockObjectManagerTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use DWenzel\T3events\Utility\SettingsInterface as SI;

/**
 * This file is part of the TYPO3 CMS project.
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
class PerformanceDemandFactoryTest extends UnitTestCase
{
    use MockObjectManagerTrait;

    /**
     * @var PerformanceDemandFactory
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new PerformanceDemandFactory();
    }

    /**
     * @test
     */
    public function createFromSettingsReturnsPerformanceDemand(): void
    {
        $this->assertEquals(
            new PerformanceDemand(),
            $this->subject->createFromSettings([])
        );
    }

    /**
     * @return array
     */
    public function settablePropertiesDataProvider(): array
    {
        /** propertyName, $settingsValue, $expectedValue */
        return [
            [SI::GENRES, '1,2', '1,2'],
            ['statuses', '1,2', '1,2'],
            [SI::VENUES, '3,4', '3,4'],
            [SI::EVENT_TYPES, '5,6', '5,6'],
            ['eventLocations', '5,6', '5,6'],
            ['categories', '7,8', '7,8'],
            ['categoryConjunction', 'and', 'and'],
            ['limit', '50', 50],
            ['offset', '10', 10],
            ['uidList', '7,8,9', '7,8,9'],
            ['storagePages', '7,8,9', '7,8,9'],
            ['order', 'foo|bar,baz|asc', 'foo|bar,baz|asc'],
            ['sortBy', 'headline', 'event.headline'],
            ['sortBy', 'performances.date', 'date']
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
    public function mappedPropertiesDataProvider(): array
    {
        /** settingsKey, propertyName, $settingsValue, $expectedValue */
        return [
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
    public function skippedPropertiesDataProvider(): array
    {
        return [
            ['foo', ''],
            ['periodType', 'bar'],
            ['periodStart', 12],
            ['periodDuration', 23],
            ['search', new Search()]
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

        $expected = new PerformanceDemand();
        if ($propertyName !== 'search') {
            $expected->_setProperty($propertyName, $propertyValue);
        }

        $createdDemand = $this->subject->createFromSettings($settings);

        $this->assertEquals(
            $expected,
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
     * @test
     */
    public function createFromSettingsSetsOrderFromLegacySettings(): void
    {
        $settings = [
            'sortBy' => 'foo',
            SI::SORT_DIRECTION => 'bar'
        ];
        $expectedOrder = 'foo|bar';

        $createdDemand = $this->subject->createFromSettings($settings);

        $this->assertSame(
            $expectedOrder,
            $createdDemand->getOrder()
        );
    }

    /**
     * @return array
     */
    public function allowedValuesForCreateFormSettingsMapsOrderFormEventSettingsDataProvider(): array
    {
        return [
            'performance.date asc' => [
                'date|asc,begin|asc', 'performances.date|asc,performances.begin|asc'
            ],
            'performance.date desc' => [
                'date|desc,begin|desc', 'performances.date|desc,performances.begin|desc'
            ]
        ];
    }

    /**
     * @test
     * @dataProvider allowedValuesForCreateFormSettingsMapsOrderFormEventSettingsDataProvider
     * @param $expected
     * @param $order
     */
    public function createFromSettingsMapsOrderFromEventSettings($expected, $order): void
    {
        $settings = [
            'order' => $order,
        ];
        $expectedOrder = $expected;

        $createdDemand = $this->subject->createFromSettings($settings);

        $this->assertSame(
            $expectedOrder,
            $createdDemand->getOrder()
        );
    }

    /**
     * @param array $methods Methods to mock
     * @return PerformanceDemand|MockObject
     */
    protected function getMockPerformanceDemand(array $methods = []): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getMockBuilder(PerformanceDemand::class)
            ->setMethods($methods)
            ->getMock();
    }
}
