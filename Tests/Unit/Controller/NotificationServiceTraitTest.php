<?php

namespace DWenzel\T3events\Tests\Unit\Controller;

/**
 * This file is part of the "Events" project.
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

use DWenzel\T3events\Controller\NotificationServiceTrait;
use DWenzel\T3events\Service\NotificationService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class NotificationServiceTraitTest
 */
class NotificationServiceTraitTest extends UnitTestCase
{
    /**
     * @var NotificationServiceTrait
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new class () {
            use NotificationServiceTrait;

            /**
             * @return NotificationService
             */
            public function getNotificationService(): NotificationService
            {
                return $this->notificationService;
            }
        };
    }

    /**
     * @test
     */
    public function notificationServiceCanBeInjected(): void
    {
        /** @var NotificationService|\PHPUnit_Framework_MockObject_MockObject $notificationService */
        $notificationService = $this->getMockBuilder(NotificationService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subject->injectNotificationService($notificationService);

        $this->assertSame(
            $notificationService,
            $this->subject->getNotificationService()
        );
    }
}
