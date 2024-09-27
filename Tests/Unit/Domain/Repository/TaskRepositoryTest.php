<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Repository;

use DWenzel\T3events\Domain\Repository\TaskRepository;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

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
class TaskRepositoryTest extends UnitTestCase
{
    /**
     * @var TaskRepository|MockObject
     */
    protected $subject;

    /**
     * set up subject
     */
    protected function setUp(): void
    {
        $this->subject = new TaskRepository();
    }

    /**
     * @test
     */
    public function initializeObjectsSetsDefaultQuerySettings(): void
    {
        $mockQuerySettings = $this->getMockBuilder(Typo3QuerySettings::class)
            ->disableOriginalConstructor()->getMock();
        $mockQuerySettings->expects($this->once())
            ->method('setRespectStoragePage')
            ->with(false);
        $this->subject->setDefaultQuerySettings($mockQuerySettings);

        $this->subject->initializeObject();

        $this->assertSame($mockQuerySettings, $this->subject->getDefaultQuerySettings());
    }
}
