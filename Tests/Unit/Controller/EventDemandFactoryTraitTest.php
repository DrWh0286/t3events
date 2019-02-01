<?php
namespace DWenzel\T3events\Tests\Unit\Controller;

use DWenzel\T3events\Controller\EventDemandFactoryTrait;
use DWenzel\T3events\Domain\Factory\Dto\EventDemandFactory;
use Nimut\TestingFramework\TestCase\UnitTestCase;

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
class EventDemandFactoryTraitTest extends UnitTestCase
{
    /**
     * @var EventDemandFactoryTrait
     */
    protected $subject;

    /**
     * set up the subject
     */
    public function setUp()
    {
        $this->subject = $this->getMockForTrait(
            EventDemandFactoryTrait::class
        );
    }

    /**
     * @test
     */
    public function eventDemandFactoryCanBeInjected()
    {
        /** @var EventDemandFactory|\PHPUnit_Framework_MockObject_MockObject $eventDemandFactory */
        $eventDemandFactory = $this->getMockBuilder(EventDemandFactory::class)
            ->getMock();

        $this->subject->injectEventDemandFactory($eventDemandFactory);

        $this->assertAttributeSame(
            $eventDemandFactory,
            'eventDemandFactory',
            $this->subject
        );
    }
}
