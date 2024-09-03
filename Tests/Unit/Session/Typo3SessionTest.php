<?php

namespace DWenzel\T3events\Tests\Unit\Session;

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

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use DWenzel\T3events\Session\Typo3Session;

/**
 * Class Typo3SessionTest
 *
 * @package DWenzel\T3events\Tests\Unit\Service
 */
class Typo3SessionTest extends UnitTestCase
{
    public const SESSION_NAMESPACE = 'testNamespace';

    /**
     * @var \DWenzel\T3events\Session\Typo3Session
     */
    protected $subject;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController|\TYPO3\CMS\Core\Tests\AccessibleObjectInterface
     */
    protected $tsfe = null;

    /**
     * @var FrontendUserAuthentication
     */
    protected $feUser;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->subject = new Typo3Session();
        $this->subject->setNamespace(self::SESSION_NAMESPACE);

        $this->tsfe = $this->getAccessibleMock(
            TypoScriptFrontendController::class,
            [],
            [],
            '',
            false
        );
        $this->feUser = $this->getAccessibleMock(
            FrontendUserAuthentication::class,
            [],
            [],
            '',
            false
        );
        $this->tsfe->fe_user = $this->feUser;
        $GLOBALS['TSFE'] = $this->tsfe;
    }

    /**
     * @test
     */
    public function constructorSetsNameSpace(): void
    {
        $namespace = 'foo';
        $subject = new Typo3Session($namespace);
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
            $this->subject->getNamespace()
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
    public function setSetsStoresDataInSession(): void
    {
        $value = 'foo';
        $identifier = 'bar';
        $this->feUser->expects($this->once())
            ->method('setKey')
            ->with('ses', self::SESSION_NAMESPACE, [$identifier => $value]);
        $this->feUser->expects($this->once())
            ->method('storeSessionData');
        $this->subject->set($identifier, $value);
    }

    /**
     * @test
     */
    public function getReturnsDataFromSessionIfDataIsEmptyAndKeyIsSet(): void
    {
        $value = 'foo';
        $identifier = 'bar';
        $expectedSessionValue = [$identifier => $value];
        $this->feUser->expects($this->once())
            ->method('getKey')
            ->with('ses', self::SESSION_NAMESPACE)
            ->will($this->returnValue($expectedSessionValue));

        $this->assertSame(
            $value,
            $this->subject->get($identifier)
        );
    }

    /**
     * @test
     */
    public function getReturnsNullIfDataIsEmptyAndKeyIsNotSetInSession(): void
    {
        $identifier = 'bar';
        $this->feUser->expects($this->once())
            ->method('getKey')
            ->with('ses', self::SESSION_NAMESPACE)
            ->will($this->returnValue(null));

        $this->assertNull(
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
    public function cleanEmptiesSession(): void
    {
        $this->feUser->expects($this->once())
            ->method('setKey')
            ->with('ses', self::SESSION_NAMESPACE, []);
        $this->feUser->expects($this->once())
            ->method('storeSessionData');

        $this->subject->clean();
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
