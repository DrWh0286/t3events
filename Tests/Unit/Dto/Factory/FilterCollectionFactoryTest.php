<?php

namespace DWenzel\T3events\Tests\Unit\Dto\Factory;

use DWenzel\T3events\Dto\Factory\FilterCollectionFactory;
use DWenzel\T3events\Dto\Factory\FilterFactory;
use DWenzel\T3events\Dto\FilterCollection;
use DWenzel\T3events\Dto\FilterInterface;
use DWenzel\T3events\Tests\Unit\Object\MockObjectManagerTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Dirk Wenzel
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class FilterCollectionFactoryTest
 */
class FilterCollectionFactoryTest extends UnitTestCase
{
    use MockObjectManagerTrait;

    /**
     * @var FilterCollectionFactory|MockObject
     */
    protected $subject;

    /**
     * @var ObjectManager|MockObject
     */
    protected $objectManager;

    /**
     * @var FilterFactory|MockObject
     */
    protected $filterFactory;

    /**
     * @var FilterCollection|MockObject
     */
    protected $filterCollection;

    protected function setUp(): void
    {
        $this->filterFactory = $this->getMockBuilder(FilterFactory::class)->disableOriginalConstructor()->getMock();
        $this->subject = new FilterCollectionFactory($this->filterFactory);

        $this->filterCollection = new FilterCollection();
    }

    public function testCreateReturnsFilterCollection(): void
    {
        $configuration = [];

        $this->assertInstanceOf(
            FilterCollection::class,
            $this->subject->create($configuration)
        );
    }

    public function testCreateAddsFilterFromFactory(): void
    {
        $filterKey = 'fooKey';
        $filterConfiguration = 'barValue';

        $configuration = [
            $filterKey => $filterConfiguration
        ];
        $mockFilter = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();

        $this->filterFactory->expects($this->once())
            ->method('get')
            ->with($filterKey, [$filterConfiguration])
            ->willReturn($mockFilter);

        $collection = $this->subject->create($configuration);
        $this->assertTrue(
            $collection->contains($mockFilter)
        );
    }
}
