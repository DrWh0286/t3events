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
use DWenzel\T3events\Domain\Model\Audience;
use DWenzel\T3events\Domain\Model\Content;
use DWenzel\T3events\Domain\Model\Event;
use DWenzel\T3events\Domain\Model\EventType;
use DWenzel\T3events\Domain\Model\Genre;
use DWenzel\T3events\Domain\Model\Organizer;
use DWenzel\T3events\Domain\Model\Performance;
use DWenzel\T3events\Domain\Model\Venue;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case for class \DWenzel\T3events\Domain\Model\Event.
 */
class EventTest extends UnitTestCase
{
    /**
     * @var Event
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new Event();
    }

    /**
     * @test
     */
    public function getHeadlineReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->subject->getHeadline()
        );
    }

    /**
     * @test
     */
    public function setHeadlineForStringSetsHeadline(): void
    {
        $this->subject->setHeadline('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getHeadline()
        );
    }

    /**
     * @test
     */
    public function getSubtitleReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->subject->getSubtitle()
        );
    }

    /**
     * @test
     */
    public function setSubtitleForStringSetsSubtitle(): void
    {
        $this->subject->setSubtitle('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getSubtitle()
        );
    }

    /**
     * @test
     */
    public function getTeaserForStringReturnsInitiallyNull(): void
    {
        $this->assertNull(
            $this->subject->getTeaser()
        );
    }

    /**
     * @test
     */
    public function setTeaserForStringSetsTeaser(): void
    {
        $this->subject->setTeaser('foo');

        $this->assertSame(
            'foo',
            $this->subject->getTeaser()
        );
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription(): void
    {
        $this->subject->setDescription('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getDescription()
        );
    }

    /**
     * @test
     */
    public function getKeywordsReturnsInitialValueForString(): void
    {
        $this->assertNull(
            $this->subject->getKeywords()
        );
    }

    /**
     * @test
     */
    public function setKeywordsForStringSetsKeywords(): void
    {
        $this->subject->setKeywords('Conceived at T3CON10');

        $this->assertSame(
            'Conceived at T3CON10',
            $this->subject->getKeywords()
        );
    }

    /**
     * @test
     */
    public function getImagesReturnsInitialValueForObjectStorageContainingImages(): void
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getImages()
        );
    }

    /**
     * @test
     */
    public function setImagesForObjectStorageContainingImagesSetsImages(): void
    {
        $images = new FileReference();
        $objectStorageHoldingExactlyOneImage = new ObjectStorage();
        $objectStorageHoldingExactlyOneImage->attach($images);
        $this->subject->setImages($objectStorageHoldingExactlyOneImage);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneImage,
            $this->subject->getImages()
        );
    }

    /**
     * @test
     */
    public function addImagesToObjectStorageHoldingImages(): void
    {
        $images = new FileReference();
        $objectStorageHoldingExactlyOneImage = new ObjectStorage();
        $objectStorageHoldingExactlyOneImage->attach($images);
        $this->subject->addImages($images);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneImage,
            $this->subject->getImages()
        );
    }

    /**
     * @test
     */
    public function removeImagesFromObjectStorageHoldingImages(): void
    {
        $images = new FileReference();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($images);
        $localObjectStorage->detach($images);
        $this->subject->addImages($images);
        $this->subject->removeImages($images);

        $this->assertEquals(
            $localObjectStorage,
            $this->subject->getImages()
        );
    }

    /**
     * @test
     */
    public function getFilesReturnsInitialValueForObjectStorageContainingFiles(): void
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getFiles()
        );
    }

    /**
     * @test
     */
    public function setFilesForObjectStorageContainingFilesSetsFiles(): void
    {
        $files = new FileReference();
        $objectStorageHoldingExactlyOneImage = new ObjectStorage();
        $objectStorageHoldingExactlyOneImage->attach($files);
        $this->subject->setFiles($objectStorageHoldingExactlyOneImage);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneImage,
            $this->subject->getFiles()
        );
    }

    /**
     * @test
     */
    public function addFilesToObjectStorageHoldingFiles(): void
    {
        $files = new FileReference();
        $objectStorageHoldingExactlyOneImage = new ObjectStorage();
        $objectStorageHoldingExactlyOneImage->attach($files);
        $this->subject->addFiles($files);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneImage,
            $this->subject->getFiles()
        );
    }

    /**
     * @test
     */
    public function removeFilesFromObjectStorageHoldingFiles(): void
    {
        $files = new FileReference();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($files);
        $localObjectStorage->detach($files);
        $this->subject->addFiles($files);
        $this->subject->removeFiles($files);

        $this->assertEquals(
            $localObjectStorage,
            $this->subject->getFiles()
        );
    }

    /**
     * @test
     */
    public function getRelatedReturnsInitialValueForObjectStorageContainingRelated(): void
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getRelated()
        );
    }

    /**
     * @test
     */
    public function setRelatedForObjectStorageContainingRelatedSetsRelated(): void
    {
        $related = new Event();
        $objectStorageHoldingExactlyOneRelated = new ObjectStorage();
        $objectStorageHoldingExactlyOneRelated->attach($related);
        $this->subject->setRelated($objectStorageHoldingExactlyOneRelated);

        $this->assertSame(
            $objectStorageHoldingExactlyOneRelated,
            $this->subject->getRelated()
        );
    }

    /**
     * @test
     */
    public function addRelatedToObjectStorageHoldingRelated(): void
    {
        $related = new Event();
        $objectStorageHoldingExactlyOneRelated = new ObjectStorage();
        $objectStorageHoldingExactlyOneRelated->attach($related);
        $this->subject->addRelated($related);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneRelated,
            $this->subject->getRelated()
        );
    }

    /**
     * @test
     */
    public function removeRelatedFromObjectStorageHoldingRelated(): void
    {
        $related = new Event();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($related);
        $localObjectStorage->detach($related);
        $this->subject->addRelated($related);
        $this->subject->removeRelated($related);

        $this->assertEquals(
            $localObjectStorage,
            $this->subject->getRelated()
        );
    }

    /**
     * @test
     */
    public function getGenreReturnsInitialValueForObjectStorageContainingGenre(): void
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getGenre()
        );
    }

    /**
     * @test
     */
    public function setGenreForObjectStorageContainingGenreSetsGenre(): void
    {
        $genre = new Genre();
        $objectStorageHoldingExactlyOneGenre = new ObjectStorage();
        $objectStorageHoldingExactlyOneGenre->attach($genre);
        $this->subject->setGenre($objectStorageHoldingExactlyOneGenre);

        $this->assertSame(
            $objectStorageHoldingExactlyOneGenre,
            $this->subject->getGenre()
        );
    }

    /**
     * @test
     */
    public function addGenreToObjectStorageHoldingGenre(): void
    {
        $genre = new Genre();
        $objectStorageHoldingExactlyOneGenre = new ObjectStorage();
        $objectStorageHoldingExactlyOneGenre->attach($genre);
        $this->subject->addGenre($genre);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneGenre,
            $this->subject->getGenre()
        );
    }

    /**
     * @test
     */
    public function removeGenreFromObjectStorageHoldingGenre(): void
    {
        $genre = new Genre();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($genre);
        $localObjectStorage->detach($genre);
        $this->subject->addGenre($genre);
        $this->subject->removeGenre($genre);

        $this->assertEquals(
            $localObjectStorage,
            $this->subject->getGenre()
        );
    }

    /**
     * @test
     */
    public function getVenueReturnsInitialValueForObjectStorageContainingVenue(): void
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getVenue()
        );
    }

    /**
     * @test
     */
    public function setVenueForObjectStorageContainingVenueSetsVenue(): void
    {
        $venue = new Venue();
        $objectStorageHoldingExactlyOneVenue = new ObjectStorage();
        $objectStorageHoldingExactlyOneVenue->attach($venue);
        $this->subject->setVenue($objectStorageHoldingExactlyOneVenue);

        $this->assertSame(
            $objectStorageHoldingExactlyOneVenue,
            $this->subject->getVenue()
        );
    }

    /**
     * @test
     */
    public function addVenueToObjectStorageHoldingVenue(): void
    {
        $venue = new Venue();
        $objectStorageHoldingExactlyOneVenue = new ObjectStorage();
        $objectStorageHoldingExactlyOneVenue->attach($venue);
        $this->subject->addVenue($venue);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneVenue,
            $this->subject->getVenue()
        );
    }

    /**
     * @test
     */
    public function removeVenueFromObjectStorageHoldingVenue(): void
    {
        $venue = new Venue();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($venue);
        $localObjectStorage->detach($venue);
        $this->subject->addVenue($venue);
        $this->subject->removeVenue($venue);

        $this->assertEquals(
            $localObjectStorage,
            $this->subject->getVenue()
        );
    }

    /**
     * @test
     */
    public function getEventTypeReturnsInitialValueForEventType(): void
    {
        $this->assertEquals(
            null,
            $this->subject->getEventType()
        );
    }

    /**
     * @test
     */
    public function setEventTypeForEventTypeSetsEventType(): void
    {
        $dummyObject = new EventType();
        $this->subject->setEventType($dummyObject);

        $this->assertSame(
            $dummyObject,
            $this->subject->getEventType()
        );
    }

    /**
     * @test
     */
    public function getPerformancesReturnsInitialValueForObjectStorageContainingPerformance(): void
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getPerformances()
        );
    }

    /**
     * @test
     */
    public function setPerformancesForObjectStorageContainingPerformanceSetsPerformances(): void
    {
        $performance = new Performance();
        $objectStorageHoldingExactlyOnePerformances = new ObjectStorage();
        $objectStorageHoldingExactlyOnePerformances->attach($performance);
        $this->subject->setPerformances($objectStorageHoldingExactlyOnePerformances);

        $this->assertSame(
            $objectStorageHoldingExactlyOnePerformances,
            $this->subject->getPerformances()
        );
    }

    /**
     * @test
     */
    public function addPerformanceToObjectStorageHoldingPerformances(): void
    {
        $performance = new Performance();
        $objectStorageHoldingExactlyOnePerformance = new ObjectStorage();
        $objectStorageHoldingExactlyOnePerformance->attach($performance);
        $this->subject->addPerformance($performance);

        $this->assertEquals(
            $objectStorageHoldingExactlyOnePerformance,
            $this->subject->getPerformances()
        );
    }

    /**
     * @test
     */
    public function removePerformanceFromObjectStorageHoldingPerformances(): void
    {
        $performance = new Performance();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($performance);
        $localObjectStorage->detach($performance);
        $this->subject->addPerformance($performance);
        $this->subject->removePerformance($performance);

        $this->assertEquals(
            $localObjectStorage,
            $this->subject->getPerformances()
        );
    }

    /**
     * @test
     */
    public function getOrganizerReturnsInitialValueForOrganizer(): void
    {
        $this->assertEquals(
            null,
            $this->subject->getOrganizer()
        );
    }

    /**
     * @test
     */
    public function setOrganizerForOrganizerSetsOrganizer(): void
    {
        $dummyObject = new Organizer();
        $this->subject->setOrganizer($dummyObject);

        $this->assertSame(
            $dummyObject,
            $this->subject->getOrganizer()
        );
    }

    /**
     * @test
     */
    public function getEarliestDateReturnsInitiallyNull(): void
    {
        $this->assertNull($this->subject->getEarliestDate());
    }

    /**
     * @param array $methods Methods to mock
     * @return Performance|MockObject
     */
    protected function getMockPerformance(array $methods = []): \PHPUnit\Framework\MockObject\MockObject
    {
        return $this->getMockBuilder(Performance::class)
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * @test
     */
    public function getEarliestDateReturnsEarliestDate(): void
    {
        $earliestDate = new \DateTime('2024-01-01 08:00:00');
        $laterDate = new \DateTime('2024-01-02 08:00:00');
        $mockPerformanceA = $this->getMockPerformance(['getDate']);
        $mockPerformanceB = $this->getMockPerformance(['getDate']);
        $fixture = new Event();
        $fixture->addPerformance($mockPerformanceA);
        $fixture->addPerformance($mockPerformanceB);
        $mockPerformanceA->expects($this->once())->method('getDate')
            ->will($this->returnValue($earliestDate));
        $mockPerformanceB->expects($this->once())->method('getDate')
            ->will($this->returnValue($laterDate));
        $this->assertSame(
            $earliestDate->getTimestamp(),
            $fixture->getEarliestDate()
        );
    }

    /**
     * @test
     */
    public function getHiddenReturnsInitialyNull(): void
    {
        $this->assertNull(
            $this->subject->getHidden()
        );
    }

    /**
     * @test
     */
    public function setHiddenForIntegerSetsHidden(): void
    {
        $this->subject->setHidden(3);
        $this->assertSame(
            3,
            $this->subject->getHidden()
        );
    }

    /**
     * @test
     */
    public function getAudienceReturnsInitialValueForObjectStorageContainingAudience(): void
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getAudience()
        );
    }

    /**
     * @test
     */
    public function setAudienceForObjectStorageContainingAudienceSetsAudience(): void
    {
        $audience = new Audience();
        $objectStorageHoldingExactlyOneAudience = new ObjectStorage();
        $objectStorageHoldingExactlyOneAudience->attach($audience);
        $this->subject->setAudience($objectStorageHoldingExactlyOneAudience);

        $this->assertSame(
            $objectStorageHoldingExactlyOneAudience,
            $this->subject->getAudience()
        );
    }

    /**
     * @test
     */
    public function addAudienceToObjectStorageHoldingAudience(): void
    {
        $audience = new Audience();
        $objectStorageHoldingExactlyOneAudience = new ObjectStorage();
        $objectStorageHoldingExactlyOneAudience->attach($audience);
        $this->subject->addAudience($audience);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneAudience,
            $this->subject->getAudience()
        );
    }

    /**
     * @test
     */
    public function removeAudienceFromObjectStorageHoldingAudience(): void
    {
        $audience = new Audience();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($audience);
        $localObjectStorage->detach($audience);
        $this->subject->addAudience($audience);
        $this->subject->removeAudience($audience);

        $this->assertEquals(
            $localObjectStorage,
            $this->subject->getAudience()
        );
    }

    /**
     * @test
     */
    public function getNewUntilInitiallyReturnsNull(): void
    {
        $this->assertNull(
            $this->subject->getNewUntil()
        );
    }

    /**
     * @test
     */
    public function newUntilCanBeSet(): void
    {
        $date = new \DateTime();

        $this->subject->setNewUntil($date);
        $this->assertSame(
            $date,
            $this->subject->getNewUntil()
        );
    }

    /**
     * @test
     */
    public function getArchiveDateInitiallyReturnsNull(): void
    {
        $this->assertNull(
            $this->subject->getArchiveDate()
        );
    }

    /**
     * @test
     */
    public function archiveDateCanBeSet(): void
    {
        $date = new \DateTime();

        $this->subject->setArchiveDate($date);
        $this->assertSame(
            $date,
            $this->subject->getArchiveDate()
        );
    }

    /**
     * @test
     */
    public function getContentElementsReturnsInitialValueForObjectStorageContainingContentElements(): void
    {
        $newObjectStorage = new ObjectStorage();
        $this->assertEquals(
            $newObjectStorage,
            $this->subject->getContentElements()
        );
    }

    /**
     * @test
     */
    public function setContentElementsForObjectStorageContainingContentElementSetsContentElements(): void
    {
        $contentElements = new Content();
        $objectStorageHoldingExactlyOneContentElements = new ObjectStorage();
        $objectStorageHoldingExactlyOneContentElements->attach($contentElements);
        $this->subject->setContentElements($objectStorageHoldingExactlyOneContentElements);

        $this->assertSame(
            $objectStorageHoldingExactlyOneContentElements,
            $this->subject->getContentElements()
        );
    }

    /**
     * @test
     */
    public function addContentElementToObjectStorageHoldingContentElements(): void
    {
        $contentElements = new Content();
        $objectStorageHoldingExactlyOneContentElement = new ObjectStorage();
        $objectStorageHoldingExactlyOneContentElement->attach($contentElements);
        $this->subject->addContentElements($contentElements);

        $this->assertEquals(
            $objectStorageHoldingExactlyOneContentElement,
            $this->subject->getContentElements()
        );
    }

    /**
     * @test
     */
    public function removeContentElementFromObjectStorageHoldingContentElements(): void
    {
        $contentElements = new Content();
        $localObjectStorage = new ObjectStorage();
        $localObjectStorage->attach($contentElements);
        $localObjectStorage->detach($contentElements);
        $this->subject->addContentElements($contentElements);
        $this->subject->removeContentElements($contentElements);

        $this->assertEquals(
            $localObjectStorage,
            $this->subject->getContentElements()
        );
    }

    /**
     * @test
     */
    public function getRelatedSchedulesInitiallyReturnsEmptyObjectStorage(): void
    {
        $emptyObjectStorage = new ObjectStorage();

        $this->assertEquals(
            $emptyObjectStorage,
            $this->subject->getRelatedSchedules()
        );
        $this->assertEmpty(
            $this->subject->getRelatedSchedules()
        );
    }
}
