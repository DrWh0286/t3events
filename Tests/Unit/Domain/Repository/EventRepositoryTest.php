<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Dirk Wenzel <dirk.wenzel@cps-it.de>
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

use DWenzel\T3events\Domain\Model\Dto\EventDemand;
use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Repository\EventRepository;
use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use DWenzel\T3events\Utility\SettingsInterface as SI;


/**
 * Test case for class \DWenzel\T3events\Domain\Repository\EventRepository.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package TYPO3
 * @subpackage Events
 * @author Dirk Wenzel <dirk.wenzel@cps-it.de>
 * @coversDefaultClass \DWenzel\T3events\Domain\Repository\EventRepository
 */
class EventRepositoryTest extends UnitTestCase
{
    use MockQueryTrait;

    /**
     * @var \DWenzel\T3events\Domain\Repository\EventRepository|MockObject|AccessibleMockObjectInterface
     */
    protected $fixture;

    protected function setUp(): void
    {
        $this->fixture = $this->getAccessibleMock(EventRepository::class,
            array('dummy'), array(), '', false);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandInitiallyReturnsEmptyArray(): void
    {
        $demand = $this->getMockEventDemand();
        $query = $this->getMockQuery();

        $this->assertEquals(
            array(),
            $this->fixture->createConstraintsFromDemand($query, $demand)
        );
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCallsCreatePeriodConstraints(): void
    {
        $this->fixture = $this->getAccessibleMock(
            EventRepository::class,
            array('createPeriodConstraints'), array(), '', false);
        $demand = $this->getMockEventDemand();
        $query = $this->getMockQuery();

        $this->fixture->expects($this->once())
            ->method('createPeriodConstraints')
            ->with($query, $demand);
        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCallsCreateCategoryConstraints(): void
    {
        $this->fixture = $this->getAccessibleMock(EventRepository::class,
            array('createCategoryConstraints'), array(), '', false);
        $demand = $this->getMockEventDemand();
        $query = $this->getMockQuery();

        $this->fixture->expects($this->once())
            ->method('createCategoryConstraints')
            ->with($query, $demand);
        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCallsCreateSearchConstraints(): void
    {
        $this->fixture = $this->getAccessibleMock(
            EventRepository::class,
            array('createSearchConstraints'), array(), '', false);
        $demand = $this->getMockEventDemand();
        $query = $this->getMockQuery();

        $this->fixture->expects($this->once())
            ->method('createSearchConstraints')
            ->with($query, $demand);
        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCallsCreateLocationConstraints(): void
    {
        $this->fixture = $this->getAccessibleMock(
            EventRepository::class,
            array('createLocationConstraints'), array(), '', false);
        $demand = $this->getMockEventDemand();
        $query = $this->getMockQuery();

        $this->fixture->expects($this->once())
            ->method('createLocationConstraints')
            ->with($query, $demand);
        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCallsCreateAudienceConstraints(): void
    {
        $this->fixture = $this->getAccessibleMock(
            EventRepository::class,
            ['createAudienceConstraints'], [], '', false);
        $demand = $this->getMockEventDemand();
        $query = $this->getMockQuery();

        $this->fixture->expects($this->once())
            ->method('createAudienceConstraints')
            ->with($query, $demand);
        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCombinesSearchConstraintsLogicalOr(): void
    {
        $this->fixture = $this->getAccessibleMock(
            EventRepository::class,
            array('createSearchConstraints', 'combineConstraints'), array(), '', false);
        $demand = $this->getMockEventDemand();
        $constraints = array();
        $query = $this->getMockQuery();
        $mockSearchConstraints = array('foo');

        $this->fixture->expects($this->once())
            ->method('createSearchConstraints')
            ->with($query, $demand)
            ->will($this->returnValue($mockSearchConstraints)
            );
        $this->fixture->expects($this->once())
            ->method('combineConstraints')
            ->with($query, $constraints, $mockSearchConstraints, 'OR');

        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCombinesLocationConstraintsLogicalAnd(): void
    {
        $this->fixture = $this->getAccessibleMock(
            EventRepository::class,
            array('createLocationConstraints', 'combineConstraints'), array(), '', false);
        $demand = $this->getMockEventDemand();
        $constraints = array();
        $query = $this->getMockQuery();
        $mockLocationConstraints = array('foo');

        $this->fixture->expects($this->once())
            ->method('createLocationConstraints')
            ->with($query, $demand)
            ->will($this->returnValue($mockLocationConstraints)
            );
        $this->fixture->expects($this->once())
            ->method('combineConstraints')
            ->with($query, $constraints, $mockLocationConstraints, 'AND');

        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCombinesAudienceConstraintsLogicalAnd(): void
    {
        $this->fixture = $this->getAccessibleMock(
            EventRepository::class,
            ['createAudienceConstraints', 'combineConstraints'], [], '', false);
        $demand = $this->getMockEventDemand();
        $constraints = [];
        $query = $this->getMockQuery();
        $mockAudienceConstraints = ['foo'];

        $this->fixture->expects($this->once())
            ->method('createAudienceConstraints')
            ->with($query, $demand)
            ->will($this->returnValue($mockAudienceConstraints)
            );
        $this->fixture->expects($this->once())
            ->method('combineConstraints')
            ->with($query, $constraints, $mockAudienceConstraints, 'AND');

        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCombinesCategoryConstraints(): void
    {
        $this->fixture = $this->getAccessibleMock(
            EventRepository::class,
            array('createCategoryConstraints', 'combineConstraints'), array(), '', false);
        $demand = $this->getMockEventDemand();
        $constraints = array();
        $query = $this->getMockQuery();
        $mockCategoryConstraints = array('foo');

        $this->fixture->expects($this->once())
            ->method('createCategoryConstraints')
            ->with($query, $demand)
            ->will($this->returnValue($mockCategoryConstraints)
            );
        $this->fixture->expects($this->once())
            ->method('combineConstraints')
            ->with($query, $constraints, $mockCategoryConstraints, null);

        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandCombinesPeriodConstraintsLogicalAnd(): void
    {
        $this->fixture = $this->getAccessibleMock(
            EventRepository::class,
            array('createPeriodConstraints', 'combineConstraints'), array(), '', false);
        $demand = $this->getMockEventDemand();
        $constraints = array();
        $query = $this->getMockQuery();
        $mockPeriodConstraints = array('foo');

        $this->fixture->expects($this->once())
            ->method('createPeriodConstraints')
            ->with($query, $demand)
            ->will($this->returnValue($mockPeriodConstraints)
            );
        $this->fixture->expects($this->once())
            ->method('combineConstraints')
            ->with($query, $constraints, $mockPeriodConstraints, 'AND');

        $this->fixture->createConstraintsFromDemand($query, $demand);
    }

    /**
     * @test
     * @covers ::createSearchConstraints
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function createSearchConstraintsInitiallyReturnsEmptyArray(): void
    {
        $demand = $this->getMockEventDemand();
        $query = $this->getMockQuery();

        $this->assertEquals(
            array(),
            $this->fixture->createSearchConstraints($query, $demand)
        );
    }

    /**
     * @test
     * @covers ::createSearchConstraints
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function createSearchConstraintsReturnsEmptyArrayForEmptySubject(): void
    {
        $demand = $this->getMockEventDemand(['getSearch']);
        $mockSearch = $this->getMockSearch(['getSubject']);
        $query = $this->getMockQuery();

        $demand->expects($this->once())
            ->method('getSearch')
            ->will($this->returnValue($mockSearch)
            );
        $mockSearch->expects($this->once())
            ->method('getSubject')
            ->will($this->returnValue('')
            );

        $this->assertEquals(
            array(),
            $this->fixture->createSearchConstraints($query, $demand)
        );
    }

    /**
     * @test
     * @covers ::createSearchConstraints
     * @expectedException \UnexpectedValueException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function createSearchConstraintsThrowsExceptionForMissingSearchFields(): void
    {
        $demand = $this->getMockEventDemand(['getSearch']);
        $mockSearch = $this->getMockSearch(['getSubject']);
        $query = $this->getMockQuery();

        $demand->expects($this->once())
            ->method('getSearch')
            ->will($this->returnValue($mockSearch)
            );
        $mockSearch->expects($this->once())
            ->method('getSubject')
            ->will($this->returnValue('foo')
            );

        $this->assertEquals(
            array(),
            $this->fixture->createSearchConstraints($query, $demand)
        );
    }

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

        $demand->expects($this->once())
            ->method('getSearch')
            ->will($this->returnValue($mockSearch)
            );
        $mockSearch->expects($this->once())
            ->method('getSubject')
            ->will($this->returnValue($subject)
            );
        $mockSearch->expects($this->once())
            ->method('getFields')
            ->will($this->returnValue($searchFields)
            );
        $query->expects($this->exactly(2))
            ->method('like')
            ->withConsecutive(
                array('bar', '%' . $subject . '%'),
                array('baz', '%' . $subject . '%')
            )
            ->will($this->returnValue($query)
            );

        $expectedResult = array(
            $query,
            $query
        );
        $this->assertEquals(
            $expectedResult,
            $this->fixture->createSearchConstraints($query, $demand)
        );
    }

    /**
     * @test
     */
    public function createCategoryConstraintsInitiallyReturnsEmptyArray(): void
    {
        $query = $this->getMockQuery();
        $demand = $this->getMockEventDemand();
        $this->assertSame(
            [],
            $this->fixture->createConstraintsFromDemand($query, $demand)
        );
    }

    /**
     * @test
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function createCategoryConstraintsCreatesGenreConstraints(): void
    {
        $genreList = '1,2';
        $query = $this->getMockQuery(['contains']);
        $demand = $this->getMockEventDemand();
        $mockConstraint = 'fooConstraint';

        $demand->expects($this->any())
            ->method('getGenre')
            ->will($this->returnValue($genreList));
        $query->expects($this->exactly(2))
            ->method('contains')
            ->withConsecutive(
                [SI::LEGACY_KEY_GENRE, 1],
                [SI::LEGACY_KEY_GENRE, 2]
            )
            ->will($this->returnValue($mockConstraint));
        $this->assertSame(
            [$mockConstraint, $mockConstraint],
            $this->fixture->createCategoryConstraints($query, $demand)
        );
    }

    /**
     * @test
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function createCategoryConstraintsCreatesVenueConstraints(): void
    {
        $venueList = '1,2';
        $query = $this->getMockQuery(['contains']);
        $demand = $this->getMockEventDemand();
        $mockConstraint = 'fooConstraint';

        $demand->expects($this->any())
            ->method('getVenue')
            ->will($this->returnValue($venueList));
        $query->expects($this->exactly(2))
            ->method('contains')
            ->withConsecutive(
                ['venue', 1],
                ['venue', 2]
            )
            ->will($this->returnValue($mockConstraint));
        $this->assertSame(
            [$mockConstraint, $mockConstraint],
            $this->fixture->createCategoryConstraints($query, $demand)
        );
    }

    /**
     * @test
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function createCategoryConstraintsCreatesEventTypeConstraints(): void
    {
        $eventTypeList = '1,2';
        $query = $this->getMockQuery(['equals']);
        $demand = $this->getMockEventDemand();
        $mockConstraint = 'fooConstraint';

        $demand->expects($this->any())
            ->method('getEventType')
            ->will($this->returnValue($eventTypeList));
        $query->expects($this->exactly(2))
            ->method('equals')
            ->withConsecutive(
                ['eventType.uid', 1],
                ['eventType.uid', 2]
            )
            ->will($this->returnValue($mockConstraint));
        $this->assertSame(
            [$mockConstraint, $mockConstraint],
            $this->fixture->createCategoryConstraints($query, $demand)
        );
    }

    /**
     * @test
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function createCategoryConstraintsCreatesCategoryConstraints(): void
    {
        $categoryList = '1,2';
        $query = $this->getMockQuery(['contains']);
        $demand = $this->getMockEventDemand();
        $mockConstraint = 'fooConstraint';

        $demand->expects($this->any())
            ->method('getCategories')
            ->will($this->returnValue($categoryList));
        $query->expects($this->exactly(2))
            ->method('contains')
            ->withConsecutive(
                ['categories', 1],
                ['categories', 2]
            )
            ->will($this->returnValue($mockConstraint));
        $this->assertSame(
            [$mockConstraint, $mockConstraint],
            $this->fixture->createCategoryConstraints($query, $demand)
        );
    }

    /**
     * @param array $methods
     * @return EventDemand|MockObject
     */
    protected function getMockEventDemand(array $methods = [])
    {
        return $this->getMockBuilder(EventDemand::class)
            ->setMethods($methods)->getMock();
    }

    /**
     * @param array $methods Methods to mock
     * @return Search|MockObject
     */
    protected function getMockSearch(array $methods = [])
    {
        return $this->getMockBuilder(Search::class)
            ->setMethods($methods)->getMock();
    }
}
