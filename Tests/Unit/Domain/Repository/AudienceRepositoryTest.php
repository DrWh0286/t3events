<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Repository;

use DWenzel\T3events\Domain\Model\Dto\DemandInterface;
use DWenzel\T3events\Domain\Repository\AudienceRepository;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Test case for class \DWenzel\T3events\Domain\Repository\AudienceRepository.
 *
 * @coversDefaultClass \DWenzel\T3events\Domain\Repository\AudienceRepository
 */
class AudienceRepositoryTest extends UnitTestCase
{
    /**
     * @var AudienceRepository
     */
    protected $fixture;

    protected function setUp(): void
    {
        $this->fixture = new AudienceRepository();
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandInitiallyReturnsEmptyArray(): void
    {
        /** @var DemandInterface|MockObject $demand */
        $demand = $this->getMockBuilder(DemandInterface::class)
            ->getMockForAbstractClass();

        /** @var QueryInterface|MockObject $query */
        $query = $this->getMockBuilder(QueryInterface::class)
            ->getMockForAbstractClass();

        $this->assertEquals(
            [],
            $this->fixture->createConstraintsFromDemand($query, $demand)
        );
    }
}
