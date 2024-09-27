<?php

namespace DWenzel\T3events\Domain\Model;

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
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;

/**
 * Class Performance
 * @package DWenzel\T3events\Domain\Model
 */
class Performance extends AbstractEntity
{
    use EqualsTrait;

    /**
     * date
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * admission
     *
     * @var int
     */
    protected $admission;

    /**
     * begin
     *
     * @var int
     */
    protected $begin;

    /**
     * end
     *
     * @var int
     */
    protected $end;

    /**
     * statusInfo
     *
     * @var string
     */
    protected $statusInfo;

    /**
     * externalProviderLink
     *
     * @var string
     */
    protected $externalProviderLink;

    /**
     * additionalLink
     *
     * @var string
     */
    protected $additionalLink;

    /**
     * providerType
     *
     * @var integer
     */
    protected $providerType = 0;

    /**
     * image
     *
     * @var string
     */
    protected $image;

    /**
     * images
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    #[Lazy]
    protected $images;

    /**
     * plan
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    #[Lazy]
    protected $plan;

    /**
     * noHandlingFee
     *
     * @var boolean
     */
    protected $noHandlingFee = false;

    /**
     * priceNotice
     *
     * @var string
     */
    protected $priceNotice;

    /**
     * @var \DWenzel\T3events\Domain\Model\Event
     */
    #[Lazy]
    protected $event;

    /**
     * eventLocation
     *
     * @var \DWenzel\T3events\Domain\Model\EventLocation
     */
    #[Lazy]
    protected $eventLocation;

    /**
     * ticketClass
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\DWenzel\T3events\Domain\Model\TicketClass>
     */
    #[Lazy]
    protected $ticketClass;

    /**
     * status
     *
     * @var \DWenzel\T3events\Domain\Model\PerformanceStatus
     */
    #[Lazy]
    protected $status;

    /**
     * hidden
     *
     * @var integer
     */
    protected $hidden;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        /**
         * Do not modify this method!
         * It will be rewritten on each save in the extension builder
         * You may modify the constructor of this class instead
         */
        $this->images = new ObjectStorage();
        $this->ticketClass = new ObjectStorage();
        $this->plan = new ObjectStorage();
    }

    /**
     * Returns the eventLocation
     *
     * @return \DWenzel\T3events\Domain\Model\Event
     */
    public function getEvent()
    {
        if ($this->event instanceof LazyLoadingProxy) {
            $this->event->_loadRealInstance();
        }

        return $this->event;
    }

    /**
     * Sets the event
     *
     * @param \DWenzel\T3events\Domain\Model\Event $event
     */
    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    /**
     * Returns the eventLocation
     *
     * @return \DWenzel\T3events\Domain\Model\EventLocation eventLocation
     */
    public function getEventLocation()
    {
        if ($this->eventLocation instanceof LazyLoadingProxy) {
            $this->eventLocation->_loadRealInstance();
        }

        return $this->eventLocation;
    }

    /**
     * Sets the eventLocation
     *
     * @param \DWenzel\T3events\Domain\Model\EventLocation $eventLocation
     * @return \DWenzel\T3events\Domain\Model\EventLocation eventLocation
     */
    public function setEventLocation(\DWenzel\T3events\Domain\Model\EventLocation $eventLocation): void
    {
        $this->eventLocation = $eventLocation;
    }

    /**
     * Returns the date
     *
     * @return \DateTime $date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the date
     *
     * @param \DateTime $date
     * @return void
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * Gets the end date
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Sets the end date
     *
     * @param \DateTime $date
     */
    public function setEndDate($date): void
    {
        $this->endDate = $date;
    }

    /**
     * Returns the admission
     *
     * @return int $admission
     */
    public function getAdmission()
    {
        return $this->admission;
    }

    /**
     * Sets the admission
     *
     * @param int $admission
     * @return void
     */
    public function setAdmission($admission): void
    {
        $this->admission = $admission;
    }

    /**
     * Returns the begin
     *
     * @return int $begin
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * Sets the begin
     *
     * @param int $begin
     * @return void
     */
    public function setBegin($begin): void
    {
        $this->begin = $begin;
    }

    /**
     * Returns the end
     *
     * @return int $end
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Sets the end
     *
     * @param int $end
     * @return void
     */
    public function setEnd($end): void
    {
        $this->end = $end;
    }

    /**
     * Returns the statusInfo
     *
     * @return string $statusInfo
     */
    public function getStatusInfo()
    {
        return $this->statusInfo;
    }

    /**
     * Sets the statusInfo
     *
     * @param string $statusInfo
     * @return void
     */
    public function setStatusInfo($statusInfo): void
    {
        $this->statusInfo = $statusInfo;
    }

    /**
     * Returns the image
     *
     * @return string $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image
     *
     * @param string $image
     * @return void
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * Adds an image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image Image
     * @return void
     */
    public function addImages(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image): void
    {
        $this->images->attach($image);
    }

    /**
     * Removes an image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $imageToRemove Image
     * @return void
     */
    public function removeImages(\TYPO3\CMS\Extbase\Domain\Model\FileReference $imageToRemove): void
    {
        $this->images->detach($imageToRemove);
    }

    /**
     * Returns the images
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage $images
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Sets the images
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $images Images
     * @return void
     */
    public function setImages(ObjectStorage $images): void
    {
        $this->images = $images;
    }

    /**
     * Adds a plan
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $plan Plan
     * @return void
     */
    public function addPlan(\TYPO3\CMS\Extbase\Domain\Model\FileReference $plan): void
    {
        $this->plan->attach($plan);
    }

    /**
     * Removes a plan
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $planToRemove $planToRemove
     * @return void
     */
    public function removePlan(\TYPO3\CMS\Extbase\Domain\Model\FileReference $planToRemove): void
    {
        $this->plan->detach($planToRemove);
    }

    /**
     * Returns the plan
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage $plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Sets the plan
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $plan Plan
     * @return void
     */
    public function setPlan(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $plan): void
    {
        $this->plan = $plan;
    }

    /**
     * Returns the noHandlingFee
     *
     * @return boolean $noHandlingFee
     */
    public function getNoHandlingFee()
    {
        return $this->noHandlingFee;
    }

    /**
     * Sets the noHandlingFee
     *
     * @param boolean $noHandlingFee
     * @return void
     */
    public function setNoHandlingFee($noHandlingFee): void
    {
        $this->noHandlingFee = $noHandlingFee;
    }

    /**
     * Returns the boolean state of noHandlingFee
     *
     * @return boolean
     */
    public function isNoHandlingFee()
    {
        return $this->getNoHandlingFee();
    }

    /**
     * Returns the status
     *
     * @return \DWenzel\T3events\Domain\Model\PerformanceStatus $status
     */
    public function getStatus()
    {
        if ($this->status instanceof LazyLoadingProxy) {
            $this->status->_loadRealInstance();
        }

        return $this->status;
    }

    /**
     * Sets the status
     *
     * @param \DWenzel\T3events\Domain\Model\PerformanceStatus $status
     * @return void
     */
    public function setStatus(\DWenzel\T3events\Domain\Model\PerformanceStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Returns the priceNotice
     *
     * @return string $priceNotice
     */
    public function getPriceNotice()
    {
        return $this->priceNotice;
    }

    /**
     * Sets the priceNotice
     *
     * @param string $priceNotice
     * @return void
     */
    public function setPriceNotice($priceNotice): void
    {
        $this->priceNotice = $priceNotice;
    }

    /**
     * Adds a TicketClass
     *
     * @param \DWenzel\T3events\Domain\Model\TicketClass $ticketClass
     * @return void
     */
    public function addTicketClass(\DWenzel\T3events\Domain\Model\TicketClass $ticketClass): void
    {
        $this->ticketClass->attach($ticketClass);
    }

    /**
     * Removes a TicketClass
     *
     * @param \DWenzel\T3events\Domain\Model\TicketClass $ticketClassToRemove The TicketClass to be removed
     * @return void
     */
    public function removeTicketClass(\DWenzel\T3events\Domain\Model\TicketClass $ticketClassToRemove): void
    {
        $this->ticketClass->detach($ticketClassToRemove);
    }

    /**
     * Returns the ticketClass
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\DWenzel\T3events\Domain\Model\TicketClass> $ticketClass
     */
    public function getTicketClass()
    {
        return $this->ticketClass;
    }

    /**
     * Sets the ticketClass
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\DWenzel\T3events\Domain\Model\TicketClass> $ticketClass
     * @return void
     */
    public function setTicketClass(ObjectStorage $ticketClass): void
    {
        $this->ticketClass = $ticketClass;
    }

    /**
     * Returns the additionalLink
     *
     * @return string additionalLink
     */
    public function getAdditionalLink()
    {
        return $this->additionalLink;
    }

    /**
     * Sets the additionalLink
     *
     * @param string $additionalLink
     * @return string additionalLink
     */
    public function setAdditionalLink($additionalLink): void
    {
        $this->additionalLink = $additionalLink;
    }

    /**
     * Returns the externalProviderLink
     *
     * @return string externalProviderLink
     */
    public function getExternalProviderLink()
    {
        return $this->externalProviderLink;
    }

    /**
     * Sets the externalProviderLink
     *
     * @param string $externalProviderLink
     * @return string externalProviderLink
     */
    public function setExternalProviderLink($externalProviderLink): void
    {
        $this->externalProviderLink = $externalProviderLink;
    }

    /**
     * Returns the providerType
     *
     * @return integer $providerType
     */
    public function getProviderType()
    {
        return $this->providerType;
    }

    /**
     * Sets the providerType
     *
     * @param integer $providerType
     * @return void
     */
    public function setProviderType($providerType): void
    {
        $this->providerType = $providerType;
    }

    /**
     * Return hidden
     *
     * @return integer
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set hidden
     *
     * @var integer $hidden
     */
    public function setHidden($hidden): void
    {
        $this->hidden = ($hidden);
    }
}
