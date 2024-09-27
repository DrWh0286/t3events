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

use DWenzel\T3events\Domain\Model\Dto\AbstractDemand;
use DWenzel\T3events\Domain\Repository\AbstractDemandedRepository;
use DWenzel\T3events\Tests\Unit\Domain\Model\Dto\MockDemandTrait;
use Nimut\TestingFramework\MockObject\AccessibleMockObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Test case for class \DWenzel\T3events\Domain\Repository\AbstractDemandedRepository.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package TYPO3
 * @subpackage Events
 * @author Dirk Wenzel <dirk.wenzel@cps-it.de>
 * @coversDefaultClass AbstractDemandedRepository
 */
class AbstractDemandedRepositoryTest extends UnitTestCase
{
    use MockConstraintsTrait;
    use MockDemandTrait;
    use MockQueryTrait;
    use MockQuerySettingsTrait;

    /**
     * @var AbstractDemandedRepository|AccessibleMockObjectInterface|MockObject
     */
    protected $fixture;

    protected function setUp(): void
    {
        $this->fixture = $this->getAccessibleMock(
            AbstractDemandedRepository::class,
            ['createConstraintsFromDemand', 'createQuery'],
            [],
            '',
            false
        );
    }

    /**
     * @test
     */
    public function createOrderingsFromDemandReturnsInitiallyEmptyArray(): void
    {
        $expectedResult = [];
        $demand = $this->getMockDemand();
        $this->assertEquals(
            $expectedResult,
            $this->fixture->createOrderingsFromDemand($demand)
        );
    }

    /**
     * @test
     */
    public function createOrderingsFromDemandReturnsEmptyArrayForEmptyOrderList(): void
    {
        $expectedResult = [];
        $mockDemand = $this->getMockDemand(['getOrder']);
        $emptyOrderList = '';
        $mockDemand->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($emptyOrderList));

        $this->assertEquals(
            $expectedResult,
            $this->fixture->createOrderingsFromDemand($mockDemand)
        );
    }

    /**
     * @test
     */
    public function createOrderingsFromDemandReturnsOrderingsForFieldWithoutOrder(): void
    {
        $fieldName = 'foo';
        $expectedResult = [$fieldName => QueryInterface::ORDER_ASCENDING];
        $mockDemand = $this->getMockDemand(['getOrder']);

        $mockDemand->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($fieldName));

        $this->assertEquals(
            $expectedResult,
            $this->fixture->createOrderingsFromDemand($mockDemand)
        );
    }

    /**
     * @test
     */
    public function createOrderingsFromDemandReturnsOrderingsForFieldWithDescendingOrder(): void
    {
        $fieldWithDescendingOrder = 'foo|desc';
        $expectedResult = ['foo' => QueryInterface::ORDER_DESCENDING];
        $mockDemand = $this->getMockDemand(['getOrder']);

        $mockDemand->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($fieldWithDescendingOrder));

        $this->assertEquals(
            $expectedResult,
            $this->fixture->createOrderingsFromDemand($mockDemand)
        );
    }

    /**
     * @test
     */
    public function createOrderingsFromDemandReturnsOrderingsForMultipleFieldsWithDifferentOrder(): void
    {
        $fieldsWithDifferentOrder = 'foo|desc,bar|asc';
        $expectedResult = ['foo' => QueryInterface::ORDER_DESCENDING, 'bar' => QueryInterface::ORDER_ASCENDING];
        $mockDemand = $this->getMockDemand(['getOrder']);

        $mockDemand->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($fieldsWithDifferentOrder));

        $this->assertEquals(
            $expectedResult,
            $this->fixture->createOrderingsFromDemand($mockDemand)
        );
    }

    /**
     * @test
     */
    public function findDemandedGeneratesAndExecutesQuery(): void
    {
        /** @var AbstractDemandedRepository|MockObject $fixture */
        $fixture = $this->getMockBuilder(AbstractDemandedRepository::class)
            ->setMethods(['createConstraintsFromDemand', 'generateQuery'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mockDemand = $this->getMockDemand();
        $mockQuery = $this->getMockQuery(['execute']);
        $expectedResult = 'foo';
        $respectEnableFields = false;

        $fixture->expects($this->once())
            ->method('generateQuery')
            ->with($mockDemand, $respectEnableFields)
            ->will($this->returnValue($mockQuery));
        $mockQuery->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($expectedResult));

        $this->assertEquals(
            $expectedResult,
            $fixture->findDemanded($mockDemand, $respectEnableFields)
        );
    }

    /**
     * @test
     */
    public function generateQueryCreatesQueryAndConstraints(): void
    {
        /** @var AbstractDemandedRepository|MockObject $fixture */
        $fixture = $this->getAccessibleMock(
            AbstractDemandedRepository::class,
            ['createConstraintsFromDemand', 'createQuery'],
            [],
            '',
            false
        );
        $mockDemand = $this->getMockDemand();
        $mockQuery = $this->getMockForAbstractClass(
            \TYPO3\CMS\Extbase\Persistence\QueryInterface::class
        );

        $fixture->expects($this->once())
            ->method('createQuery')
            ->with()
            ->will($this->returnValue($mockQuery));
        $fixture->expects($this->once())
            ->method('createConstraintsFromDemand')
            ->with($mockQuery, $mockDemand)
            ->will($this->returnValue([]));

        $this->assertSame(
            $mockQuery,
            $fixture->generateQuery($mockDemand)
        );
    }

    /**
     * @test
     */
    public function generateQueryReturnsQueryMatchingConstraints(): void
    {
        /** @var AbstractDemandedRepository|MockObject $fixture */
        $fixture = $this->getAccessibleMock(
            AbstractDemandedRepository::class,
            ['createConstraintsFromDemand', 'createQuery'],
            [],
            '',
            false
        );
        $mockDemand = $this->getMockDemand();
        $mockQuery = $this->getMockQuery(['matching', 'logicalAnd']);
        $mockConstraints = ['foo'];

        $fixture->expects($this->once())
            ->method('createQuery')
            ->with()
            ->will($this->returnValue($mockQuery));
        $fixture->expects($this->once())
            ->method('createConstraintsFromDemand')
            ->with($mockQuery, $mockDemand)
            ->will($this->returnValue($mockConstraints));
        $mockQuery->expects($this->once())
            ->method('matching')
            ->with($mockQuery);
        $mockQuery->expects($this->once())
            ->method('logicalAnd')
            ->with($mockConstraints)
            ->will($this->returnValue($mockQuery));

        $fixture->generateQuery($mockDemand);
    }

    /**
     * @test
     */
    public function generateQuerySetsOrderings(): void
    {
        /** @var AbstractDemandedRepository|MockObject|AccessibleMockObjectInterface $fixture */
        $fixture = $this->getAccessibleMock(
            AbstractDemandedRepository::class,
            ['createQuery', 'createConstraintsFromDemand', 'createOrderingsFromDemand'],
            [],
            '',
            false
        );
        $mockDemand = $this->getMockDemand();
        $mockQuery = $this->getMockQuery(['setOrderings']);
        $mockOrderings = ['foo' => 'bar'];

        $fixture->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($mockQuery));
        $fixture->expects($this->once())
            ->method('createConstraintsFromDemand');
        $fixture->expects($this->once())
            ->method('createOrderingsFromDemand')
            ->will($this->returnValue($mockOrderings));
        $mockQuery->expects($this->once())
            ->method('setOrderings')
            ->with($mockOrderings);
        $fixture->generateQuery($mockDemand);
    }

    /**
     * @test
     */
    public function generateQuerySetsIgnoreEnableFields(): void
    {
        /** @var AbstractDemandedRepository|AccessibleMockObjectInterface|MockObject $fixture */
        $fixture = $this->getAccessibleMock(
            AbstractDemandedRepository::class,
            ['createQuery', 'createConstraintsFromDemand', 'createOrderingsFromDemand'],
            [],
            '',
            false
        );
        $mockDemand = $this->getMockDemand();
        $mockQuerySettings = $this->getMockQuerySettings();
        $mockQuery = $this->getMockQuery(['setOrderings', 'getQuerySettings']);

        $fixture->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($mockQuery));
        $fixture->expects($this->once())
            ->method('createConstraintsFromDemand');
        $fixture->expects($this->once())
            ->method('createOrderingsFromDemand');
        $mockQuery->expects($this->once())
            ->method('getQuerySettings')
            ->will($this->returnValue($mockQuerySettings));
        $mockQuerySettings->expects($this->once())
            ->method('setIgnoreEnableFields')
            ->with(true);

        $fixture->generateQuery($mockDemand, false);
    }


    /**
     * @test
     */
    public function generateQuerySetsOffsetFromDemand(): void
    {
        /** @var AbstractDemandedRepository|AccessibleMockObjectInterface|MockObject $fixture */
        $fixture = $this->getAccessibleMock(
            AbstractDemandedRepository::class,
            ['createQuery', 'createConstraintsFromDemand'],
            [],
            '',
            false
        );
        /** @var AbstractDemand|MockObject|AccessibleMockObjectInterface $mockDemand */
        $mockDemand = $this->getAccessibleMockForAbstractClass(\DWenzel\T3events\Domain\Model\Dto\AbstractDemand::class);
        $offset = 3;
        $mockDemand->setOffset($offset);
        $mockQuery = $this->getMockQuery(['setOffset']);
        $fixture->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($mockQuery));
        $fixture->expects($this->once())
            ->method('createConstraintsFromDemand');

        $mockQuery->expects($this->once())
            ->method('setOffset')
            ->with($offset);
        $fixture->generateQuery($mockDemand);
    }


    /**
     * @test
     */
    public function combineConstraintsInitiallyCombinesLogicalAnd(): void
    {
        $fixture = $this->getAccessibleMock(
            AbstractDemandedRepository::class,
            ['createConstraintsFromDemand'],
            [],
            '',
            false
        );
        $constraints = [];
        $mockQuery = $this->getMockQuery(['logicalAnd']);
        $additionalConstraint = [$this->getMockConstraint()];

        $mockQuery->expects($this->once())
            ->method('logicalAnd')
            ->with(...$additionalConstraint);
        $fixture->combineConstraints(
            $mockQuery,
            $constraints,
            $additionalConstraint
        );
    }

    /**
     * @test
     */
    public function combineConstraintsCombinesLogicalOr(): void
    {
        $fixture = $this->getAccessibleMock(
            AbstractDemandedRepository::class,
            ['createConstraintsFromDemand'],
            [],
            '',
            false
        );
        $constraints = [];
        $conjunction = 'or';
        $mockQuery = $this->getMockQuery(['logicalOr']);
        $additionalConstraint = [$this->getMockConstraint()];

        $mockQuery->expects($this->once())
            ->method('logicalOr')
            ->with(...$additionalConstraint);
        $fixture->combineConstraints(
            $mockQuery,
            $constraints,
            $additionalConstraint,
            $conjunction
        );
    }

    /**
     * @test
     */
    public function combineConstraintsCombinesLogicalNotAnd(): void
    {
        $fixture = $this->getAccessibleMock(
            AbstractDemandedRepository::class,
            ['createConstraintsFromDemand'],
            [],
            '',
            false
        );
        $constraints = [];
        $conjunction = 'NotAnd';
        $mockQuery = $this->getMockQuery(['logicalNot', 'logicalAnd']);
        $mockConstraint = $this->getMockConstraint();
        $additionalConstraint = [$mockConstraint];

        $mockQuery->expects($this->once())
            ->method('logicalAnd')
            ->with($mockConstraint)
            ->will($this->returnValue($mockConstraint));
        $mockQuery->expects($this->once())
            ->method('logicalNot')
            ->with($mockConstraint);
        $fixture->combineConstraints(
            $mockQuery,
            $constraints,
            $additionalConstraint,
            $conjunction
        );
    }

    /**
     * @test
     */
    public function combineConstraintsCombinesLogicalNotOr(): void
    {
        $fixture = $this->getMockBuilder(AbstractDemandedRepository::class)->disableOriginalConstructor()->getMockForAbstractClass();
        $constraints = [];
        $conjunction = 'NotOr';
        $mockQuery = $this->getMockQuery(['logicalNot', 'logicalOr']);
        $mockConstraint = $this->getMockConstraint();
        $additionalConstraint = [$mockConstraint];

        $mockQuery->expects($this->once())
            ->method('logicalOr')
            ->with($mockConstraint)
            ->will($this->returnValue($mockConstraint));
        $mockQuery->expects($this->once())
            ->method('logicalNot')
            ->with($mockConstraint);

        $fixture->combineConstraints(
            $mockQuery,
            $constraints,
            $additionalConstraint,
            $conjunction
        );
    }

    /**
     * @test
     */
    public function findMultipleByUidReturnsQuery(): void
    {
        $mockQuery = $this->getMockQuery();

        $mockResult = $this->getMockBuilder(QueryResultInterface::class)->getMockForAbstractClass();
        $mockQuery->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($mockResult));

        $this->fixture->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($mockQuery));

        $this->assertSame(
            $mockResult,
            $this->fixture->findMultipleByUid(
                '1,2',
                null
            )
        );
    }

    /**
     * @test
     */
    public function findMultipleByUidMatchesUidList(): void
    {
        $uidList = '1,2';
        /** @var QueryInterface $mockQuery */
        $mockQuery = $this->getMockQuery(['matching', 'in']);
        $mockQuery->expects($this->once())
            ->method('matching')
            ->will($this->returnValue($mockQuery));
        $mockQuery->expects($this->once())
            ->method('in')
            ->with('uid', [1, 2])
            ->will($this->returnValue($mockQuery));

        $this->fixture->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($mockQuery));

        $this->fixture->findMultipleByUid($uidList, null);
    }

    /**
     * @test
     */
    public function findMultipleByUidSetsDefaultOrderings(): void
    {
        $uidList = '';
        /** @var QueryInterface $mockQuery */
        $mockQuery = $this->getMockQuery();

        $this->fixture->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($mockQuery));
        $mockQuery->expects($this->once())
            ->method('setOrderings')
            ->with(['uid' => QueryInterface::ORDER_ASCENDING]);

        $this->fixture->findMultipleByUid($uidList);
    }

    /**
     * @test
     */
    public function findMultipleByUidSetsOrderings(): void
    {
        $sortField = 'foo';
        $order = QueryInterface::ORDER_DESCENDING;

        $uidList = '';
        $mockQuery = $this->getMockQuery();

        $this->fixture->expects($this->once())
            ->method('createQuery')
            ->will($this->returnValue($mockQuery));
        $mockQuery->expects($this->once())
            ->method('setOrderings')
            ->with([$sortField => QueryInterface::ORDER_DESCENDING]);

        $this->fixture->findMultipleByUid($uidList, $sortField, $order);
    }
}
