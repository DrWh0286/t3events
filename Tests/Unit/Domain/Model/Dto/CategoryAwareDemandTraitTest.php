<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Model\Dto;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use DWenzel\T3events\Domain\Model\Dto\CategoryAwareDemandTrait;

/**
 * Test case for class \DWenzel\T3events\Domain\Model\Dto\CategoryAwareDemandTrait.
 */
class CategoryAwareDemandTraitTest extends UnitTestCase
{
    /**
     * @var \DWenzel\T3events\Domain\Model\Dto\CategoryAwareDemandTrait
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = $this->getMockForTrait(
            CategoryAwareDemandTrait::class
        );
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getCategoriesReturnsInitialValueForString(): void
    {
        $this->assertNull($this->subject->getCategories());
    }

    /**
     * @test
     */
    public function setCategoriesForStringSetsCategory(): void
    {
        $this->subject->setCategories('foo');
        $this->assertSame(
            'foo',
            $this->subject->getCategories()
        );
    }
}
