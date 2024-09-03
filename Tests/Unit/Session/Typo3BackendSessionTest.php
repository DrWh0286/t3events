<?php

namespace DWenzel\T3events\Tests\Unit\Session;

/**
 * This file is part of the TYPO3 CMS project.
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use DWenzel\T3events\Session\Typo3BackendSession;

/**
 * Class Typo3BackendSessionTest
 *
 * @package DWenzel\T3events\Tests\Unit\Service
 */
class Typo3BackendSessionTest extends UnitTestCase
{
    public const SESSION_NAMESPACE = 'testNamespace';

    /**
     * @var \DWenzel\T3events\Session\Typo3BackendSession
     */
    protected $subject;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->subject = new Typo3BackendSession();
        $this->subject->setNamespace(self::SESSION_NAMESPACE);
    }

    /**
     * @test
     */
    public function constructorSetsNameSpace(): void
    {
        $namespace = 'foo';
        $subject = new Typo3BackendSession($namespace);
        $this->assertSame(
            $namespace,
            $subject->getNamespace()
        );
    }

    /**
     * @test
     */
    public function setNamespaceForStringSetsNamespace(): void
    {
        $namespace = 'foo';
        $this->subject->setNamespace($namespace);
        $this->assertSame(
            $namespace,
            $this->subject->getNamespace(),
        );
    }

    /**
     * @test
     */
    public function setSetsData(): void
    {
        $value = 'foo';
        $identifier = 'bar';
        $this->subject->set($identifier, $value);

        $this->assertSame(
            $value,
            $this->subject->get($identifier)
        );
    }

    /**
     * @test
     */
    public function hasReturnsInitiallyFalse(): void
    {
        $identifier = 'bar';
        $this->assertFalse(
            $this->subject->has($identifier)
        );
    }

    /**
     * @test
     */
    public function hasReturnsTrueIfIdentifierIsSet(): void
    {
        $value = 'foo';
        $identifier = 'bar';
        $this->subject->set($identifier, $value);

        $this->assertTrue(
            $this->subject->has($identifier)
        );
    }

    /**
     * @test
     */
    public function cleanEmptiesData(): void
    {
        $value = 'foo';
        $identifier = 'bar';
        $this->subject->set($identifier, $value);

        $this->subject->clean();
        $this->assertNull(
            $this->subject->get($identifier)
        );
    }
}
