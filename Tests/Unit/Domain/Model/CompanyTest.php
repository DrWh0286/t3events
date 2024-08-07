<?php

namespace CPSIT\T3events\Tests\Unit\Domain\Model;

/***************************************************************
     *  Copyright notice
     *  (c) 2014 Dirk Wenzel <wenzel@cps-it.de>, CPS IT
     *           Boerge Franck <franck@cps-it.de>, CPS IT
     *  All rights reserved
     *  This script is part of the TYPO3 project. The TYPO3 project is
     *  free software; you can redistribute it and/or modify
     *  it under the terms of the GNU General Public License as published by
     *  the Free Software Foundation; either version 2 of the License, or
     *  (at your option) any later version.
     *  The GNU General Public License can be found at
     *  http://www.gnu.org/copyleft/gpl.html.
     *  This script is distributed in the hope that it will be useful,
     *  but WITHOUT ANY WARRANTY; without even the implied warranty of
     *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *  GNU General Public License for more details.
     *  This copyright notice MUST APPEAR in all copies of the script!
     ***************************************************************/

use DWenzel\T3events\Domain\Model\Company;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class CompanyTest
 *
 * @package CPSIT\T3events\Tests\Unit\Domain\Model
 * @coversDefaultClass Company
 */
class CompanyTest extends UnitTestCase
{

    /**
     * @var Company
     */
    protected $subject = null;

    protected function setUp(): void
    {
        $this->subject = new Company();
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString(): void
    {
        $this->assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName(): void
    {
        $this->subject->setName('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function getAddressReturnsInitialValueForString(): void
    {
        $this->assertSame(
            '',
            $this->subject->getAddress()
        );
    }

    /**
     * @test
     */
    public function setAddressForStringSetsAddress(): void
    {
        $this->subject->setAddress('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getAddress()
        );
    }

    /**
     * @test
     */
    public function getZipReturnsInitialValueForString(): void
    {
        $this->assertSame(
            '',
            $this->subject->getZip()
        );
    }

    /**
     * @test
     */
    public function setZipForStringSetsZip(): void
    {
        $this->subject->setZip('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getZip()
        );
    }

    /**
     * @test
     */
    public function getCityReturnsInitialValueForString(): void
    {
        $this->assertSame(
            '',
            $this->subject->getCity()
        );
    }

    /**
     * @test
     */
    public function setCityForStringSetsCity(): void
    {
        $this->subject->setCity('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getCity()
        );
    }

    /**
     * @test
     */
    public function getCountryReturnsInitialValueForString(): void
    {
        $this->assertSame(
            '',
            $this->subject->getCountry()
        );
    }

    /**
     * @test
     */
    public function setCountryForStringSetsCountry(): void
    {
        $this->subject->setCountry('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getCountry()
        );
    }
}
