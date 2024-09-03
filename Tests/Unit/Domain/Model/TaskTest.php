<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Model;

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
use DWenzel\T3events\Domain\Model\PerformanceStatus;
use DWenzel\T3events\Domain\Model\Task;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for class \DWenzel\T3events\Domain\Model\Task.
 *
 */
class TaskTest extends UnitTestCase
{
    /**
     * @var \DWenzel\T3events\Domain\Model\Task
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new Task();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName(): void
    {
        $this->subject->setName('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function getActionReturnsInitialNull(): void
    {
        $this->assertNull(
            $this->subject->getAction()
        );
    }

    /**
     * @test
     */
    public function setActionForIntegerSetsAction(): void
    {
        $this->subject->setAction(12);

        $this->assertSame(
            12,
            $this->subject->getAction()
        );
    }

    /**
     * @test
     */
    public function getPeriodReturnsInitialNull(): void
    {
        $this->assertNull(
            $this->subject->getPeriod()
        );
    }

    /**
     * @test
     */
    public function setPeriodForStringSetsPeriod(): void
    {
        $period = 'foo';
        $this->subject->setPeriod($period);
        $this->assertSame(
            $period,
            $this->subject->getPeriod()
        );
    }

    /**
     * @test
     */
    public function getPeriodDurationReturnsInitialNull(): void
    {
        $this->assertNull(
            $this->subject->getPeriodDuration()
        );
    }

    /**
     * @test
     */
    public function setPeriodDurationForIntegerSetsPeriodDuration(): void
    {
        $this->subject->setPeriodDuration(-30000);

        $this->assertSame(
            -30000,
            $this->subject->getPeriodDuration()
        );
    }

    /**
     * @test
     */
    public function getOldStatusReturnsInitialNull(): void
    {
        $this->assertNull(
            $this->subject->getOldStatus()
        );
    }

    /**
     * @test
     */
    public function setOldStatusForPerformanceStatusSetsOldStatus(): void
    {
        $status = new PerformanceStatus();
        $this->subject->setOldStatus($status);

        $this->assertSame(
            $status,
            $this->subject->getOldStatus()
        );
    }

    /**
     * @test
     */
    public function getNewStatusReturnsInitialNull(): void
    {
        $this->assertNull(
            $this->subject->getNewStatus()
        );
    }

    /**
     * @test
     */
    public function setNewStatusForPerformanceStatusSetsNewStatus(): void
    {
        $status = new PerformanceStatus();
        $this->subject->setNewStatus($status);

        $this->assertSame(
            $status,
            $this->subject->getNewStatus()
        );
    }

    /**
     * @test
     */
    public function getFolderReturnsInitialNull(): void
    {
        $this->assertNull(
            $this->subject->getFolder()
        );
    }

    /**
     * @test
     */
    public function setFolderForStringSetsFolder(): void
    {
        $this->subject->setFolder('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getFolder()
        );
    }
}
