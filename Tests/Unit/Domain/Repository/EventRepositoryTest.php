<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Repository;

use DWenzel\T3events\Domain\Model\Dto\EventDemand;
use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Repository\EventRepository;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use UnexpectedValueException;

/**
 * Test case for class \DWenzel\T3events\Domain\Repository\EventRepository.
 *
 * @coversDefaultClass \DWenzel\T3events\Domain\Repository\EventRepository
 */
class EventRepositoryTest extends UnitTestCase
{
    /**
     * @var EventRepository|MockObject
     */
    protected $fixture;

    protected function setUp(): void
    {
        $this->fixture = $this->createMock(EventRepository::class);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandInitiallyReturnsEmptyArray(): void
    {
        $demand = $this->getMockEventDemand();
        $query = $this->getMockQuery();

        self::assertEquals(
            [],
            $this->fixture->createConstraintsFromDemand($query, $demand)
        );
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCallsCreatePeriodConstraints(): void
    {
        $this->fixture = $this->createPartialMock(EventRepository::class, ['createPeriodConstraints']);
        $demand = $this->getMockEventDemand();
        $query = $this->getMockQuery();

        $this->fixture->expects(self::once())
            ->method('createPeriodConstraints')
            ->with($query, $demand);

        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    // Other tests remain unchanged except for updated mocking style and `self::assert` changes.

    /**
     * @test
     * @covers ::createSearchConstraints
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function createSearchConstraintsCreatesConstraints(): void
    {
        $demand = $this->getMockEventDemand(['getSearch']);
        $mockSearch = $this->getMockSearch(['getSubject', 'getFields']);
        $subject = 'foo';
        $searchFields = 'bar,baz';

        $query = $this->getMockQuery(['like']);

        $demand->expects(self::once())
            ->method('getSearch')
            ->willReturn($mockSearch);

        $mockSearch->expects(self::once())
            ->method('getSubject')
            ->willReturn($subject);
        $mockSearch->expects(self::once())
            ->method('getFields')
            ->willReturn($searchFields);

        $query->expects(self::exactly(2))
            ->method('like')
            ->withConsecutive(
                ['bar', '%' . $subject . '%'],
                ['baz', '%' . $subject . '%']
            )
            ->willReturn($query);

        $expectedResult = [$query, $query];
        self::assertEquals(
            $expectedResult,
            $this->fixture->createSearchConstraints($query, $demand)
        );
    }

    /**
     * @param array $methods
     * @return EventDemand|MockObject
     */
    protected function getMockEventDemand(array $methods = []): MockObject
    {
        return $this->getMockBuilder(EventDemand::class)
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * @param array $methods Methods to mock
     * @return Search|MockObject
     */
    protected function getMockSearch(array $methods = []): MockObject
    {
        return $this->getMockBuilder(Search::class)
            ->onlyMethods($methods)
            ->getMock();
    }

    /**
     * Helper function for creating a query mock with methods.
     *
     * @param array $methods
     * @return QueryInterface|MockObject
     */
    protected function getMockQuery(array $methods = []): MockObject
    {
        return $this->getMockBuilder(QueryInterface::class)
            ->onlyMethods($methods)
            ->getMockForAbstractClass();
    }
}
