<?php

namespace DWenzel\T3events\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Dirk Wenzel <wenzel@webfox01.de>, Agentur Webfox
 *  Michael Kasten <kasten@webfox01.de>, Agentur Webfox
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use DWenzel\T3events\Domain\Model\Event;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case for class \DWenzel\T3events\Domain\Model\Performance.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @package TYPO3
 * @subpackage Events
 * @author Dirk Wenzel <wenzel@webfox01.de>
 * @author Michael Kasten <kasten@webfox01.de>
 * @coversDefaultClass \DWenzel\T3events\Domain\Model\Performance
 */
class PerformanceTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \DWenzel\T3events\Domain\Model\Performance
     */
    protected $fixture;

    protected function setUp(): void
    {
        $this->fixture = new \DWenzel\T3events\Domain\Model\Performance();
    }

    protected function tearDown(): void
    {
        unset($this->fixture);
    }

    /**
     * @test
     * @covers ::getDate
     */
    public function getDateReturnsInitialValueForDateTime(): void
    {
        $this->assertNull($this->fixture->getDate());
    }

    /**
     * @test
     * @covers ::setDate
     */
    public function setDateForDateTimeSetsDate(): void
    {
        $date = new \DateTime();
        $this->fixture->setDate($date);
        $this->assertSame(
            $date,
            $this->fixture->getDate()
        );
    }

    /**
     * @test
     * @covers ::getEndDate
     */
    public function getEndDateReturnsInitialValueForDateTime(): void
    {
        $this->assertNull($this->fixture->getEndDate());
    }

    /**
     * @test
     * @covers ::setEndDate
     */
    public function setEndDateForDateTimeSetsEndDate(): void
    {
        $endDate = new \DateTime();
        $this->fixture->setEndDate($endDate);
        $this->assertSame(
            $endDate,
            $this->fixture->getEndDate()
        );
    }

    /**
     * @test
     * @covers ::getAdmission
     */
    public function getAdmissionReturnsInitialValueForInt(): void
    {
        $this->assertNull($this->fixture->getAdmission());
    }

    /**
     * @test
     * @covers ::setAdmission
     */
    public function setAdmissionForIntSetsAdmission(): void
    {
        $this->fixture->setAdmission(99);
        $this->assertSame(
            99,
            $this->fixture->getAdmission()
        );
    }

    /**
     * @test
     * @covers ::getBegin
     */
    public function getBeginReturnsInitialValueForInt(): void
    {
        $this->assertNull($this->fixture->getBegin());
    }

    /**
     * @test
     * @covers ::setBegin
     */
    public function setBeginForIntSetsBegin(): void
    {
        $this->fixture->setBegin(9999);
        $this->assertSame(
            9999,
            $this->fixture->getBegin()
        );
    }

    /**
     * @test
     * @covers ::getEnd
     */
    public function getEndReturnsInitialValueForInt(): void
    {
        $this->assertNull($this->fixture->getEnd());
    }

    /**
     * @test
     * @covers ::setEnd
     */
    public function setEndForIntSetsEnd(): void
    {
        $this->fixture->setEnd(123);
        $this->assertSame(
            123,
            $this->fixture->getEnd()
        );
    }

    /**
     * @test
     * @covers ::getStatusInfo
     */
    public function getStatusInfoReturnsInitialValueForString(): void
    {
        $this->assertNull($this->fixture->getStatusInfo());
    }

    /**
     * @test
     * @covers ::setStatusInfo
     */
    public function setStatusInfoForStringSetsStatusInfo(): void
    {
        $this->fixture->setStatusInfo('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getStatusInfo()
        );
    }

    /**
     * @test
     * @covers ::getExternalProviderLink
     */
    public function getExternalProviderLinkReturnsInitialValueForString(): void
    {
        $this->assertNull($this->fixture->getExternalProviderLink());
    }

    /**
     * @test
     * @covers ::setExternalProviderLink
     */
    public function setExternalProviderLinkForStringSetsExternalProviderLink(): void
    {
        $this->fixture->setExternalProviderLink('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getExternalProviderLink()
        );
    }

    /**
     * @test
     * @covers ::getAdditionalLink
     */
    public function getAdditionalLinkReturnsInitialValueForString(): void
    {
        $this->assertNull($this->fixture->getAdditionalLink());
    }

    /**
     * @test
     * @covers ::setAdditionalLink
     */
    public function setAdditionalLinkForStringSetsAdditionalLink(): void
    {
        $this->fixture->setAdditionalLink('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getAdditionalLink()
        );
    }

    /**
     * @test
     * @covers ::getProviderType
     */
    public function getProviderTypeReturnsInitialValueForInteger(): void
    {
        $this->assertSame(
            0,
            $this->fixture->getProviderType()
        );
    }

    /**
     * @test
     * @covers ::setProviderType
     */
    public function setProviderTypeForIntegerSetsProviderType(): void
    {
        $this->fixture->setProviderType(12);

        $this->assertSame(
            12,
            $this->fixture->getProviderType()
        );
    }

    /**
     * @test
     * @covers ::getImage
     */
    public function getImageReturnsInitialValueForString(): void
    {
        $this->assertNull($this->fixture->getImage());
    }

    /**
     * @test
     * @covers ::setImage
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
     * @covers ::getImages
     */
    public function getImagesReturnsInitialValueForObjectStorageContainingImages(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->fixture->getImages()
        );
    }

    /**
     * @test
     * @covers ::setImages
     */
    public function setImagesForObjectStorageContainingImagesSetsImages(): void
    {
        $images = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $objectStorageHoldingExactlyOneImage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneImage->attach($images);
        $this->fixture->setImages($objectStorageHoldingExactlyOneImage);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneImage,
            $this->fixture->getImages()
        );
    }

    /**
     * @test
     * @covers ::addImages
     */
    public function addImagesToObjectStorageHoldingImages(): void
    {
        $images = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $objectStorageHoldingExactlyOneImage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneImage->attach($images);
        $this->fixture->addImages($images);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneImage,
            $this->fixture->getImages()
        );
    }

    /**
     * @test
     * @covers ::removeImages
     */
    public function removeImagesFromObjectStorageHoldingImages(): void
    {
        $images = new \TYPO3\CMS\Extbase\Domain\Model\FileReference();
        $localObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $localObjectStorage->attach($images);
        $localObjectStorage->detach($images);
        $this->fixture->addImages($images);
        $this->fixture->removeImages($images);

        $this->assertEquals(
            $localObjectStorage,
            $this->fixture->getImages()
        );
    }

    /**
     * @test
     * @covers ::getPlan
     */
    public function getPlanReturnsInitialValueForString(): void
    {
        $this->assertEquals(new ObjectStorage(), $this->fixture->getPlan());
    }

    /**
     * @test
     * @covers ::setPlan
     */
    public function setPlanForStringSetsPlan(): void
    {
        $this->fixture->setPlan($objStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage());

        $this->assertSame(
            $objStorage,
            $this->fixture->getPlan()
        );
    }

    /**
     * @test
     * @covers ::getNoHandlingFee
     */
    public function getNoHandlingFeeReturnsInitialValueForBoolean(): void
    {
        $this->assertSame(
            false,
            $this->fixture->getNoHandlingFee()
        );
    }

    /**
     * @test
     * @covers ::isNoHandlingFee
     */
    public function isNoHandlingFeeReturnsInitialValueForBoolean(): void
    {
        $this->assertSame(
            false,
            $this->fixture->isNoHandlingFee()
        );
    }

    /**
     * @test
     * @covers ::isNoHandlingFee
     */
    public function isNoHandlingFeeForBooleanReturnsCorrectValueForBoolean(): void
    {
        $this->fixture->setNoHandlingFee(true);

        $this->assertSame(
            true,
            $this->fixture->isNoHandlingFee()
        );
    }

    /**
     * @test
     * @covers ::setNoHandlingFee
     */
    public function setNoHandlingFeeForBooleanSetsNoHandlingFee(): void
    {
        $this->fixture->setNoHandlingFee(true);

        $this->assertSame(
            true,
            $this->fixture->getNoHandlingFee()
        );
    }

    /**
     * @test
     * @covers ::getPriceNotice
     */
    public function getPriceNoticeReturnsInitialValueForString(): void
    {
        $this->assertNull($this->fixture->getPriceNotice());
    }

    /**
     * @test
     * @covers ::setPriceNotice
     */
    public function setPriceNoticeForStringSetsPriceNotice(): void
    {
        $this->fixture->setPriceNotice('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->fixture->getPriceNotice()
        );
    }

    /**
     * @test
     * @covers ::getEventLocation
     */
    public function getEventLocationReturnsInitialValueForEventLocation(): void
    {
        $this->assertEquals(
            null,
            $this->fixture->getEventLocation()
        );
    }

    /**
     * @test
     * @covers ::setEventLocation
     */
    public function setEventLocationForEventLocationSetsEventLocation(): void
    {
        $dummyObject = new \DWenzel\T3events\Domain\Model\EventLocation();
        $this->fixture->setEventLocation($dummyObject);

        $this->assertSame(
            $dummyObject,
            $this->fixture->getEventLocation()
        );
    }

    /**
     * @test
     * @covers ::getTicketClass
     */
    public function getTicketClassReturnsInitialValueForObjectStorageContainingTicketClass(): void
    {
        $newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->fixture->getTicketClass()
        );
    }

    /**
     * @test
     * @covers ::setTicketClass
     */
    public function setTicketClassForObjectStorageContainingTicketClassSetsTicketClass(): void
    {
        $ticketClas = new \DWenzel\T3events\Domain\Model\TicketClass();
        $objectStorageHoldingExactlyOneTicketClass = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneTicketClass->attach($ticketClas);
        $this->fixture->setTicketClass($objectStorageHoldingExactlyOneTicketClass);

        $this->assertSame(
            $objectStorageHoldingExactlyOneTicketClass,
            $this->fixture->getTicketClass()
        );
    }

    /**
     * @test
     * @covers ::addTicketClass
     */
    public function addTicketClassToObjectStorageHoldingTicketClass(): void
    {
        $ticketClass = new \DWenzel\T3events\Domain\Model\TicketClass();
        $objectStorageHoldingExactlyOneTicketClass = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorageHoldingExactlyOneTicketClass->attach($ticketClass);
        $this->fixture->addTicketClass($ticketClass);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneTicketClass,
            $this->fixture->getTicketClass()
        );
    }

    /**
     * @test
     * @covers ::removeTicketClass
     */
    public function removeTicketClassFromObjectStorageHoldingTicketClass(): void
    {
        $ticketClass = new \DWenzel\T3events\Domain\Model\TicketClass();
        $localObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $localObjectStorage->attach($ticketClass);
        $localObjectStorage->detach($ticketClass);
        $this->fixture->addTicketClass($ticketClass);
        $this->fixture->removeTicketClass($ticketClass);

        $this->assertEquals(
            $localObjectStorage,
            $this->fixture->getTicketClass()
        );
    }

    /**
     * @test
     * @covers ::getStatus
     */
    public function getStatusReturnsInitialValueForPerformanceStatus(): void
    {
        $this->assertEquals(
            null,
            $this->fixture->getStatus()
        );
    }

    /**
     * @test
     * @covers ::setStatus
     */
    public function setStatusForPerformanceStatusSetsStatus(): void
    {
        $dummyObject = new \DWenzel\T3events\Domain\Model\PerformanceStatus();
        $this->fixture->setStatus($dummyObject);

        $this->assertSame(
            $dummyObject,
            $this->fixture->getStatus()
        );
    }

    /**
     * @test
     * @covers ::getHidden
     */
    public function getHiddenForIntegerReturnsInitialNull(): void
    {
        $this->assertSame(
            null,
            $this->fixture->getHidden()
        );
    }

    /**
     * @test
     * @covers ::setHidden
     */
    public function setHiddenForIntegerSetsHidden(): void
    {
        $this->fixture->setHidden(1);
        $this->assertSame(1, $this->fixture->getHidden());
    }

    /**
     * @test
     * @covers ::getEvent
     */
    public function getEventForObjectInitiallyReturnsNull(): void
    {
        $this->assertNull(
            $this->fixture->getEvent()
        );
    }

    /**
     * @test
     * @covers ::setEvent
     */
    public function setEventForObjectSetsEvent(): void
    {
        $event = new Event();
        $this->fixture->setEvent($event);
        $this->assertSame(
            $event,
            $this->fixture->getEvent()
        );
    }
}
