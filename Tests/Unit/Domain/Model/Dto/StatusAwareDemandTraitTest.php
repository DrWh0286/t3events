<?php
namespace DWenzel\T3events\Tests\Unit\Domain\Model\Dto;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use DWenzel\T3events\Domain\Model\Dto\StatusAwareDemandTrait;

/**
 * Test case for class \DWenzel\T3events\Domain\Model\Dto\StatusAwareDemandTrait.
 */
class StatusAwareDemandTraitTest extends UnitTestCase
{

    /**
     * @var \DWenzel\T3events\Domain\Model\Dto\StatusAwareDemandTrait
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = $this->getMockForTrait(
            StatusAwareDemandTrait::class
        );
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getStatusReturnsInitialNull(): void
    {
        $this->assertSame(null, $this->subject->getStatus());
    }

    /**
     * @test
     */
    public function setStatusForPerformanceStatusSetsStatus(): void
    {
        $status = new \DWenzel\T3events\Domain\Model\PerformanceStatus();

        $this->subject->setStatus($status);

        $this->assertEquals($status, $this->subject->getStatus());
    }

    /**
     * @test
     */
    public function getStatusesReturnsInitialValueForString(): void
    {
        $this->assertNull($this->subject->getStatuses());
    }

    /**
     * @test
     */
    public function setStatusesForStringSetsStatuses(): void
    {
        $this->subject->setStatuses('foo');
        $this->assertSame(
            'foo',
            $this->subject->getStatuses()
        );
    }

    /**
     * @test
     */
    public function isExcludeSelectesStatusesInitiallyReturnsNull(): void
    {
        $this->assertNull(
            $this->subject->isExcludeSelectedStatuses()
        );
    }

    /**
     * @test
     */
    public function excludeSelectedStatusesCanBeSet(): void
    {
        $this->subject->setExcludeSelectedStatuses(true);
        $this->assertTrue(
            $this->subject->isExcludeSelectedStatuses()
        );
    }
}
