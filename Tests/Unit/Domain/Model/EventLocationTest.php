<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *  (c) 2012 Dirk Wenzel <wenzel@webfox01.de>, Agentur Webfox
 *            Michael Kasten <kasten@webfox01.de>, Agentur Webfox
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

use DWenzel\T3events\Domain\Model\EventLocation;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case for class \DWenzel\T3events\Domain\Model\EventLocation.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package TYPO3
 * @subpackage Events
 * @author Dirk Wenzel <wenzel@webfox01.de>
 * @author Michael Kasten <kasten@webfox01.de>
 * @coversDefaultDefaultClass \DWenzel\T3events\Domain\Model\EventLocation
 */
class EventLocationTest extends UnitTestCase
{

    /**
     * @var \DWenzel\T3events\Domain\Model\EventLocation
     */
    protected $fixture;

    protected function setUp(): void
    {
        $this->fixture = $this->getMockBuilder(EventLocation::class)
            ->setMethods(['dummy'])->getMock();
    }

    protected function tearDown(): void
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString(): void
    {
        $this->assertNull($this->fixture->getName());
    }

    /**
     * @test
     */
    public function setNameForStringSetsName(): void
    {
        $this->fixture->setName('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getName()
        );
    }

    /**
     * @test
     */
    public function getAddressReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->fixture->getAddress()
        );
    }

    /**
     * @test
     */
    public function setAddressForStringSetsAddress(): void
    {
        $this->fixture->setAddress('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getAddress()
        );
    }

    /**
     * @test
     */
    public function getImageReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->fixture->getImage()
        );
    }

    /**
     * @test
     */
    public function setImageForStringSetsImage(): void
    {
        $this->fixture->setImage('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getImage()
        );
    }

    /**
     * @test
     */
    public function getZipReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->fixture->getZip()
        );
    }

    /**
     * @test
     */
    public function setZipForStringSetsZip(): void
    {
        $this->fixture->setZip('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getZip()
        );
    }

    /**
     * @test
     */
    public function getPlaceReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->fixture->getPlace()
        );
    }

    /**
     * @test
     */
    public function setPlaceForStringSetsPlace(): void
    {
        $this->fixture->setPlace('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getPlace()
        );
    }

    /**
     * @test
     */
    public function getDetailsReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->fixture->getDetails()
        );
    }

    /**
     * @test
     */
    public function setDetailsForStringSetsDetails(): void
    {
        $this->fixture->setDetails('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getDetails()
        );
    }

    /**
     * @test
     */
    public function getWwwReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->fixture->getWww()
        );
    }

    /**
     * @test
     */
    public function setWwwForStringSetsWww(): void
    {
        $this->fixture->setWww('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getWww()
        );
    }

    /**
     * @test
     */
    public function getCountryReturnsInitialValueForString(): void
    {
        $this->assertEquals(
            null,
            $this->fixture->getCountry()
        );
    }

    /**
     * @test
     */
    public function setCountryForCountrySetsCountry(): void
    {
        $this->fixture->setCountry('foo');

        $this->assertSame(
            'foo',
            $this->fixture->getCountry()
        );
    }

    /**
     * @test
     */
    public function getLatitudeReturnsInitiallyNull(): void
    {
        $this->assertNull(
            $this->fixture->getLatitude()
        );
    }

    /**
     * @test
     */
    public function setLatitudeForFloatSetsLatitude(): void
    {
        $this->fixture->setLatitude(1.23);
        $this->assertSame(
            1.23,
            $this->fixture->getLatitude()
        );
    }

    /**
     * @test
     */
    public function getLongitudeReturnsInitiallyNull(): void
    {
        $this->assertNull(
            $this->fixture->getLongitude()
        );
    }

    /**
     * @test
     */
    public function setLongitudeForFloatSetsLongitude(): void
    {
        $this->fixture->setLongitude(1.23);
        $this->assertSame(
            1.23,
            $this->fixture->getLongitude()
        );
    }
}
