<?php

namespace DWenzel\T3events\Tests\Unit\Controller;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use DWenzel\T3events\Controller\EventLocationRepositoryTrait;
use DWenzel\T3events\Domain\Repository\EventLocationRepository;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class EventLocationRepositoryTraitTest
 */
class EventLocationRepositoryTraitTest extends UnitTestCase
{
    /**
     * @var EventLocationRepositoryTrait
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new class () {
            use EventLocationRepositoryTrait;

            /**
             * @return EventLocationRepository
             */
            public function getEventLocationRepository(): EventLocationRepository
            {
                return $this->eventLocationRepository;
            }
        };
    }

    /**
     * @test
     */
    public function eventLocationRepositoryCanBeInjected(): void
    {
        /** @var EventLocationRepository|\PHPUnit_Framework_MockObject_MockObject $eventLocationRepository */
        $eventLocationRepository = $this->getMockBuilder(EventLocationRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->subject->injectEventLocationRepository($eventLocationRepository);

        $this->assertSame(
            $eventLocationRepository,
            $this->subject->getEventLocationRepository()
        );
    }
}
