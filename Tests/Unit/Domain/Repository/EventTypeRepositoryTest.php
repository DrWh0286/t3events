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

use DWenzel\T3events\Domain\Model\Dto\DemandInterface;
use DWenzel\T3events\Domain\Repository\EventTypeRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Test case for class \DWenzel\T3events\Domain\Repository\EventTypeRepository.
 *
 * @author Dirk Wenzel <dirk.wenzel@cps-it.de>
 * @coversDefaultClass \DWenzel\T3events\Domain\Repository\EventTypeRepository
 */
class EventTypeRepositoryTest extends UnitTestCase
{
    /**
     * @var \DWenzel\T3events\Domain\Repository\EventTypeRepository
     */
    protected $fixture;

    protected function setUp(): void
    {
        $this->objectManager = $this->createMock(ObjectManagerInterface::class);
        $this->fixture = new EventTypeRepository($this->objectManager);
    }

    /**
     * @test
     * @covers ::createConstraintsFromDemand
     */
    public function createConstraintsFromDemandInitiallyReturnsEmptyArray(): void
    {
        /** @var DemandInterface|MockObject $demand */
        $demand = $this->getMockBuilder(DemandInterface::class)->getMockForAbstractClass();
        /** @var QueryInterface|MockObject $query */
        $query = $this->getMockBuilder(QueryInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->assertEquals(
            [],
            $this->fixture->createConstraintsFromDemand($query, $demand)
        );
    }
}
