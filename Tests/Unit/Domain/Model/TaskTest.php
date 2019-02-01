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
use Nimut\TestingFramework\TestCase\UnitTestCase;

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

    public function setUp()
    {
        $this->subject = $this->getMockBuilder(Task::class)
            ->setMethods(['dummy'])->getMock();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        $this->assertNull(
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
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
    public function getActionReturnsInitialNull()
    {
        $this->assertNull(
            $this->subject->getAction()
        );
    }

    /**
     * @test
     */
    public function setActionForIntegerSetsAction()
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
    public function getPeriodReturnsInitialNull()
    {
        $this->assertNull(
            $this->subject->getPeriod()
        );
    }

    /**
     * @test
     */
    public function setPeriodForStringSetsPeriod()
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
    public function getPeriodDurationReturnsInitialNull()
    {
        $this->assertNull(
            $this->subject->getPeriodDuration()
        );
    }

    /**
     * @test
     */
    public function setPeriodDurationForIntegerSetsPeriodDuration()
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
    public function getOldStatusReturnsInitialNull()
    {
        $this->assertNull(
            $this->subject->getOldStatus()
        );
    }

    /**
     * @test
     */
    public function setOldStatusForPerformanceStatusSetsOldStatus()
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
    public function getNewStatusReturnsInitialNull()
    {
        $this->assertNull(
            $this->subject->getNewStatus()
        );
    }

    /**
     * @test
     */
    public function setNewStatusForPerformanceStatusSetsNewStatus()
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
    public function getFolderReturnsInitialNull()
    {
        $this->assertNull(
            $this->subject->getFolder()
        );
    }

    /**
     * @test
     */
    public function setFolderForStringSetsFolder()
    {
        $this->subject->setFolder('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getFolder()
        );
    }
}
