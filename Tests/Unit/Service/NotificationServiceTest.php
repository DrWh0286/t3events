<?php

namespace DWenzel\T3events\Tests\Unit\Service;

use DWenzel\T3events\Tests\Unit\Object\MockObjectManagerTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvokedCount as InvokedCountMatcher;
use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Fluid\View\StandaloneView;
use DWenzel\T3events\Domain\Model\Notification;
use DWenzel\T3events\Service\NotificationService;

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

// @ToDo: Das ist Grütze!
class NotificationServiceTest extends UnitTestCase
{
    use MockObjectManagerTrait;

    /**
     * @var NotificationService
     */
    protected $subject;
    /**
     * @var MockObject|(MailerInterface&MockObject)
     */
    private MailerInterface|MockObject $mailer;
    /**
     * @var (object&MockObject)|MockObject|ConfigurationManagerInterface|(ConfigurationManagerInterface&object&MockObject)|(ConfigurationManagerInterface&MockObject)
     */
    private MockObject|ConfigurationManagerInterface $configurationManager;

    /**
     * set up subject
     */
    protected function setUp(): void
    {
        $this->configurationManager = $this->createMock(ConfigurationManagerInterface::class);
        $this->mailer = $this->getMockBuilder(MailerInterface::class)->onlyMethods(['send'])->addMethods(['getSentMessage'])->getMock();

        $this->subject = new NotificationService($this->configurationManager, $this->mailer);
    }

    /**
     * Provides recipients
     *
     * @return array
     */
    public function recipientDataProvider()
    {
        return [
            [ 'foo@bar.baz', ['foo@bar.baz']],
            ['foo@dummy.tld,bar@dummy.tld', ['foo@dummy.tld', 'bar@dummy.tld']]
        ];
    }
    /**
     * @test
     * @param string $recipientArgument
     * @param array $expectedRecipients
     * @dataProvider recipientDataProvider
     */
    public function sendSetsRecipients($recipientArgument, $expectedRecipients): void
    {
        $notification = new Notification(
            $recipientArgument,
            'dummy sender',
            'dummy@sender.tld'
        );

        $sentMessage = $this->getMockBuilder(SentMessage::class)->disableOriginalConstructor()->getMock();

        $this->mailer->expects($this->once())->method('send');
        $this->mailer->expects($this->once())->method('getSentMessage')->willReturn($sentMessage);

        $this->subject->send($notification);

        $this->assertEquals((new \DateTime())->format('Y-m-d H:i'), $notification->getSentAt()->format('Y-m-d H:i'));
    }

    /**
     * @test
     * @param string $recipient
     * @param array $expectedRecipients
     * @dataProvider recipientDataProvider
     */
    public function notifySetsRecipients($recipient, $expectedRecipients): void
    {
        $mockTemplateView = $this->getMockBuilder(StandaloneView::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mailMessage = new MailMessage();

        $classes = [
            StandaloneView::class => $mockTemplateView,
            MailMessage::class => $mailMessage
        ];

        GeneralUtility::setContainer(new class ($classes) implements ContainerInterface {
            public function __construct(private array $classes = [])
            {
            }

            public function get(string $id)
            {
                return $this->classes[$id];
            }

            public function has(string $id)
            {
                return isset($this->classes[$id]);
            }
        });

        $this->configurationManager
            ->expects($this->any())
            ->method('getConfiguration')
            ->with(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK)
            ->willReturn([
                'view' => [
                    'templateRootPath' => 'dummy/path/to/template',
                    'templateRootPaths' => ['dummy/path/to/template'],
                    'partialRootPaths' => ['dummy/path/to/partial'],
                    'layoutRootPaths' => ['dummy/path/to/layout'],
                ]
            ]);

        $this->subject->notify(
            $recipient,
            'bar@baz.foo',
            'foo',
            'bar',
            'baz',
            [],
            null,
            null
        );

        $expectedAddresses = [];
        foreach ($expectedRecipients as $expectedRecipient) {
            $expectedAddresses[] = new Address($expectedRecipient);
        }
        $this->assertEquals($expectedAddresses, $mailMessage->getTo());
    }

    /**
     * @return MailMessage|MockObject
     */
    protected function getMockMailMessage()
    {
        $message = $this->getMockBuilder(MailMessage::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setTo',
                    'setBody',
                    'send',
                    'setFrom',
                    'setSubject'
                ]
            )->getMock();
        $message->method('setTo')->willReturn($message);
        $message->method('send')->willReturn(true);
        $message->method('setFrom')->willReturn($message);
        $message->method('setSubject')->willReturn($message);

        return $message;
    }
}
