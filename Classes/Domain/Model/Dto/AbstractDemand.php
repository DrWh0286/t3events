<?php

namespace DWenzel\T3events\Domain\Model\Dto;

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

use DWenzel\T3events\Domain\Repository\PeriodConstraintRepositoryInterface;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class AbstractDemand
 * Parent class for demand objects
 *
 * @package DWenzel\T3events\Domain\Model\Dto
 * @deprecated use demand traits instead
 */
class AbstractDemand extends AbstractEntity implements DemandInterface
{
    /**
     * Category Conjunction
     *
     * @var string
     */
    protected $categoryConjunction;

    /**
     * @var int A Limit for the demand
     */
    protected $limit = 100;

    /**
     * @var int An offset
     */
    protected $offset;

    /**
     * @var string Orderings: comma separated list of sort fields and orderings ('fieldA|asc,fieldB|desc')
     */
    protected $order;

    /**
     * @var string Sort criteria
     */
    protected $sortBy;

    /**
     * @var string Sort direction
     */
    protected $sortDirection;

    /**
     * @var string Comma separated list of storage page
     */
    protected $storagePages;

    /**
     * @var string $uidList A list of record uids
     */
    protected $uidList;

    /**
     * @var string
     */
    protected $constraintsConjunction;

    /**
     * Returns the Category Conjunction
     *
     * @return string
     */
    public function getCategoryConjunction()
    {
        return $this->categoryConjunction;
    }

    /**
     * Sets the limit
     *
     * @param int $limit A limit for the demand. Only values > 0 are allowed. Default 100
     * @return void
     */
    public function setLimit($limit = 100): void
    {
        $validatedLimit = (int)$limit;

        if ($validatedLimit > 0) {
            $this->limit = $validatedLimit;
        }
    }

    /**
     * Returns the limit for a query
     *
     * @return int The limit for the demand
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets the offset for a query
     *
     * @param int $offset An offset for the demand
     * @return void
     */
    public function setOffset($offset = 0): void
    {
        $this->offset = (int)$offset;
    }

    /**
     * Gets the offset for a query
     *
     * @return int The offset of the demand
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Sets the sort field
     *
     * @param string $sortBy The sort criteria in dot notation
     * @return void
     * @deprecated use setOrder instead
     */
    public function setSortBy($sortBy): void
    {
        $this->sortBy = $sortBy;
    }

    /**
     * Gets the sort field
     *
     * @return string The sort criteria in dot notation
     * @deprecated use getOrder instead
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * Sets the sort direction
     *
     * @param string $sortDirection The sort direction
     * @return void
     * @deprecated use setOrder instead
     */
    public function setSortDirection($sortDirection): void
    {
        $this->sortDirection = $sortDirection;
    }

    /**
     * Gets the sort direction
     *
     * @return string The sort direction
     * @deprecated use getOrder instead
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * Sets the storage pages
     *
     * @param string $storagePages A comma separated list of storage page ids
     * @return void
     */
    public function setStoragePages($storagePages): void
    {
        $this->storagePages = $storagePages;
    }

    /**
     * Gets the storage pages
     *
     * @return string A comma separated list of storage page ids
     */
    public function getStoragePages()
    {
        return $this->storagePages;
    }

    /**
     * Gets a list of unique ids
     *
     * @return string|null
     */
    public function getUidList()
    {
        return $this->uidList;
    }

    /**
     * Sets the unique id list
     *
     * @param string $uidList A comma separated List of record uids
     * @return void
     */
    public function setUidList($uidList): void
    {
        $this->uidList = $uidList;
    }

    /**
     * Gets the orderings
     *
     * @return string|null
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets the orderings
     *
     * @param string $order A comma separated List of orderings
     * @return void
     */
    public function setOrder($order): void
    {
        $this->order = $order;
    }

    /**
     * Get Constraints Conjunction
     *
     * @return string
     */
    public function getConstraintsConjunction()
    {
        return $this->constraintsConjunction;
    }

    /**
     * Set Constraints Conjunction
     *
     * @param string $conjunction
     */
    public function setConstraintsConjunction($conjunction): void
    {
        $this->constraintsConjunction = $conjunction;
    }

    /**
     * Set Category Conjunction
     *
     * @param string $categoryConjunction
     * @return void
     */
    public function setCategoryConjunction($categoryConjunction): void
    {
        $this->categoryConjunction = $categoryConjunction;
    }

    public function overwriteDemandObject($overwriteDemand, $settings): void
    {
        if (!(bool)$overwriteDemand) {
            return;
        }

        $timeZone = new \DateTimeZone(date_default_timezone_get());

        foreach ($overwriteDemand as $propertyName => $propertyValue) {
            if (empty($propertyValue)) {
                continue;
            }

            $this->overwriteProperty($overwriteDemand, $propertyName, $propertyValue, $timeZone, $settings);
        }
    }

    /**
     * Overwrites a single property according to the setting in overwriteDemand
     * @param $demand
     * @param $overwriteDemand
     * @param $propertyName
     * @param $propertyValue
     * @param $timeZone
     */
    protected function overwriteProperty($overwriteDemand, $propertyName, $propertyValue, $timeZone, $settings)
    {
        switch ($propertyName) {
            case 'sortBy':
                $orderings = $propertyValue;
                if (isset($overwriteDemand[SI::SORT_DIRECTION])) {
                    $orderings .= '|' . $overwriteDemand[SI::SORT_DIRECTION];
                }

                $this->setOrder($orderings);
                $this->setSortBy($overwriteDemand['sortBy']);
                break;
            case 'search':
                if ($this instanceof SearchAwareDemandInterface) {
                    /** @var SearchFactory $factory */
                    $factory = GeneralUtility::makeInstance(SearchFactory::class);
                    $searchObj = $factory->get(
                        $propertyValue,
                        $settings['search']
                    );
                    $this->setSearch($searchObj);
                }

                break;
            case 'venue':
            case SI::VENUES:
                if ($this instanceof EventDemand) {
                    $this->setVenue($propertyValue);
                }

                if ($this instanceof VenueAwareDemandInterface) {
                    $this->setVenues($propertyValue);
                }

                break;
            case SI::LEGACY_KEY_GENRE:
                //fall through to 'genres'
            case SI::GENRES:
                if ($this instanceof EventDemand) {
                    $this->setGenre($propertyValue);
                }

                if ($this instanceof GenreAwareDemandInterface) {
                    $this->setGenres($propertyValue);
                }

                break;
            case 'eventType':
                // fall through to 'eventTypes
            case SI::EVENT_TYPES:
                if ($this instanceof EventDemand) {
                    $this->setEventType($propertyValue);
                }

                if ($this instanceof EventTypeAwareDemandInterface) {
                    $this->setEventTypes($propertyValue);
                }

                break;
            case 'eventLocation':
                if ($this instanceof EventLocationAwareDemandInterface) {
                    $this->setEventLocations($propertyValue);
                }

                break;
            case 'period':
                if ($propertyValue === PeriodConstraintRepositoryInterface::PERIOD_SPECIFIC
                    && empty($overwriteDemand[SI::START_DATE])) {
                    $this->setPeriod(PeriodConstraintRepositoryInterface::PERIOD_ALL);
                    break;
                }

                $this->setPeriod($propertyValue);
                break;
            case 'periodType':
                if ($propertyValue === 'byDate' && empty($overwriteDemand[SI::START_DATE])) {
                    break;
                }

                $this->setPeriodType($propertyValue);
                break;
            case SI::START_DATE:
                $this->setStartDate(new \DateTime($propertyValue, $timeZone));
                break;
            case SI::END_DATE:
                $this->setEndDate(new \DateTime($propertyValue, $timeZone));
                break;
            case SI::SORT_DIRECTION:
                if ($propertyValue !== 'desc') {
                    $propertyValue = 'asc';
                }
                // fall through to default
                // no break
            default:
                if (ObjectAccess::isPropertySettable($this, $propertyName)) {
                    ObjectAccess::setProperty($this, $propertyName, $propertyValue);
                }
        }
    }
}
