<?php

namespace DWenzel\T3events\Tests\Controller;

use DWenzel\T3events\Controller\NotificationRepositoryTrait;
use DWenzel\T3events\Domain\Repository\NotificationRepository;
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
class NotificationRepositoryTraitTest extends UnitTestCase
{
    /**
     * @var NotificationRepositoryTrait
     */
    protected $subject;

    /**
     * set up
     */
    public function setUp()
    {
        $this->subject = $this->getMockForTrait(
            NotificationRepositoryTrait::class
        );
    }

    /**
     * @test
     */
    public function notificationRepositoryCanBeInjected()
    {
        /** @var NotificationRepository|\PHPUnit_Framework_MockObject_MockObject $notificationRepository */
        $notificationRepository = $this->getMockBuilder(NotificationRepository::class)
            ->disableOriginalConstructor()->getMock();

        $this->subject->injectNotificationRepository($notificationRepository);

        $this->assertAttributeSame(
            $notificationRepository,
            'notificationRepository',
            $this->subject
        );
    }
}
