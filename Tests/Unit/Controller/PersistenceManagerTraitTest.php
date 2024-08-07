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

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use DWenzel\T3events\Controller\PersistenceManagerTrait;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

/**
 * Class PersistenceManagerTraitTest
 *
 * @package DWenzel\T3events\Tests\Unit\Controller
 */
class PersistenceManagerTraitTest extends UnitTestCase
{
    /**
     * @var PersistenceManagerTrait
     */
    protected $subject;

    /**
     * set up
     */
    protected function setUp(): void
    {
        $this->subject = new class
        {
            use PersistenceManagerTrait;

            public function getPersistenceManager()
            {
                return $this->persistenceManager;
            }
        };
    }

    /**
     * @test
     */
    public function persistenceManagerCanBeInjected(): void
    {
        $persistenceManager = $this->getMockForAbstractClass(
            PersistenceManagerInterface::class
        );

        $this->subject->injectPersistenceManager($persistenceManager);

        $this->assertSame(
            $persistenceManager,
            $this->subject->getPersistenceManager()
        );
    }
}
