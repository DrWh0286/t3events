<?php

namespace DWenzel\T3events\Tests\Unit\Dto;

use DWenzel\T3events\Domain\Repository\GenreRepository;
use DWenzel\T3events\Dto\GenreFilter;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

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
 * Class GenreFilterTest
 */
class GenreFilterTest extends UnitTestCase
{
    /**
     * @var GenreFilter|MockObject
     */
    protected $subject;

    /**
     * @var GenreRepository|MockObject
     */
    protected $genreRepository;

    protected function setUp(): void
    {
        $this->subject = new GenreFilter();
        $this->genreRepository = $this->getMockBuilder(GenreRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->subject->injectGenreRepository($this->genreRepository);
    }

    public function testGetOptionsRepositoryReturnsGenreRepository(): void
    {
        $this->assertSame(
            $this->genreRepository,
            $this->subject->getOptionRepository()
        );
    }
}
