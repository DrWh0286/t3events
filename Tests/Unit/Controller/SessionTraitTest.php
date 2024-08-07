<?php

namespace DWenzel\T3events\Tests\Unit\Controller;

/**
 * This file is part of the TYPO3 CMS project.
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * The TYPO3 project - inspiring people to share!
 */

use DWenzel\T3events\Controller\SessionTrait;
use DWenzel\T3events\Session\SessionInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DummyClassWithNamespace
{
    use SessionTrait;
}

/**
 * Class SessionTraitTest
 *
 * @package DWenzel\T3events\Tests\Unit\Session
 */
class SessionTraitTest extends UnitTestCase
{
    /**
     * @var SessionTrait
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new class
        {
            use SessionTrait;

            public function __construct()
            {
                $this->namespace = 'DummyNamespace';
            }

            public function getSession()
            {
                return $this->session;
            }
        };
    }

    /**
     * @test
     */
    public function sessionCanBeInjected(): void
    {
        $mockSession = $this->getMockSession();
        $this->subject->injectSession($mockSession);

        $this->assertSame(
            $mockSession,
            $this->subject->getSession()
        );
    }

    /**
     * @test
     */
    public function injectSessionSetsNamespace(): void
    {
        $mockSession = $this->getMockSession();

        $mockSession->expects($this->once())->method('setNamespace')->with('DummyNamespace');

        $this->subject->injectSession($mockSession);
    }

    /**
     * @return SessionInterface|MockObject
     */
    protected function getMockSession()
    {
        return $this->getMockBuilder(SessionInterface::class)
            ->setMethods(
                ['has', 'get', 'clean', 'set', 'setNamespace']
            )->getMockForAbstractClass();
    }
}
