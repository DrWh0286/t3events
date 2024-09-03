<?php

namespace DWenzel\T3events\Domain\Repository;

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

use DWenzel\T3events\Domain\Model\Dto\PeriodAwareDemandInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use DWenzel\T3events\Utility\SettingsInterface as SI;

/**
 * Interface PeriodConstraintRepositoryInterface
 *
 * @package DWenzel\T3events\Domain\Repository
 */
interface PeriodConstraintRepositoryInterface
{
    public const PERIOD_ALL = SI::ALL;
    public const PERIOD_FUTURE = SI::FUTURE_ONLY;
    public const PERIOD_PAST = SI::PAST_ONLY;
    public const PERIOD_SPECIFIC = SI::SPECIFIC;
    public const PERIOD_TYPE = 'periodType';
    public const PERIOD_TYPE_DAY = 'byDay';
    public const PERIOD_TYPE_MONTH = 'byMonth';
    public const PERIOD_TYPE_YEAR = 'byYear';
    public const PERIOD_TYPE_DATE = 'byDate';
    public const PERIOD_END_DATE = 'periodEndDate';
    public const PERIOD_START_DATE = 'periodStartDate';

    /**
     * Create period constraints from demand (time restriction)
     *
     * @param \TYPO3\CMS\Extbase\Persistence\QueryInterface $query
     * @param \DWenzel\T3events\Domain\Model\Dto\PeriodAwareDemandInterface $demand
     * @return array<\TYPO3\CMS\Extbase\Persistence\QOM\Constraint>
     */
    public function createPeriodConstraints(QueryInterface $query, PeriodAwareDemandInterface $demand);
}
