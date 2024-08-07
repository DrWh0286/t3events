<?php
namespace DWenzel\T3events\Tests\Unit\Domain\Model\Dto;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use DWenzel\T3events\Domain\Model\Dto\EventLocationAwareDemandTrait;

/**
 * Test case for class \DWenzel\T3events\Domain\Model\Dto\EventLocationAwareDemandTrait.
 */
class EventLocationAwareDemandTraitTest extends UnitTestCase
{

    /**
     * @var \DWenzel\T3events\Domain\Model\Dto\EventLocationAwareDemandTrait
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = $this->getMockForTrait(
            EventLocationAwareDemandTrait::class
        );
    }

    /**
     * @test
     */
    public function getEventLocationsReturnsInitialNull(): void
    {
        $this->assertSame(null, $this->subject->getEventLocations());
    }

    /**
     * @test
     */
    public function setEventLocationForStringSetsEventLocation(): void
    {
        $eventLocation = 'foo';

        $this->subject->setEventLocations($eventLocation);

        $this->assertEquals($eventLocation, $this->subject->getEventLocations());
    }
}
