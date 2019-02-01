<?php

namespace DWenzel\T3events\Tests\Controller;

use DWenzel\T3events\Controller\SearchTrait;
use DWenzel\T3events\Domain\Model\Dto\Search;
use DWenzel\T3events\Domain\Model\Dto\SearchFactory;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/***************************************************************
 *  Copyright notice
 *  (c) 2016 Dirk Wenzel <dirk.wenzel@cps-it.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class SearchTraitTest extends UnitTestCase
{
    /**
     * @var SearchTrait
     */
    protected $subject;

    /**
     * set up
     */
    public function setUp()
    {
        $this->subject = $this->getMockForTrait(
            SearchTrait::class
        );
    }

    /**
     * @test
     */
    public function searchFactoryCanBeInjected()
    {
        /** @var SearchFactory|MockObject $mockFactory */
        $mockFactory = $this->getMockBuilder(SearchFactory::class)->getMock();

        $this->subject->injectSearchFactory($mockFactory);

        $this->assertAttributeSame(
            $mockFactory,
            'searchFactory',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function createSearchObjectGetsSearchFromFactory()
    {
        $searchRequest = ['foo'];
        $settings = ['bar'];

        /** @var SearchFactory|MockObject $mockFactory */
        $mockFactory = $this->getMockBuilder(SearchFactory::class)
            ->setMethods(['get'])->getMock();

        /** @var Search|MockObject $mockSearch */
        $mockSearch = $this->getMockBuilder(Search::class)->getMock();
        $this->subject->injectSearchFactory($mockFactory);

        $mockFactory->expects($this->once())
            ->method('get')
            ->with($searchRequest, $settings)
            ->will($this->returnValue($mockSearch));

        $this->assertSame(
            $mockSearch,
            $this->subject->createSearchObject($searchRequest, $settings)
        );
    }
}
