<?php

namespace DWenzel\T3events\Domain\Model\Dto;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Dirk Wenzel <dirk.wenzel@cps-it.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
class EmConfiguration
{
    /**
     * @var bool
     */
    protected $respectPerformanceStoragePage = false;

    /**
     * Constructor
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        foreach ($configuration as $key => $value) {
            if (property_exists(__CLASS__, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @return boolean
     */
    public function isRespectPerformanceStoragePage()
    {
        return $this->respectPerformanceStoragePage;
    }

    /**
     * @param boolean $respectPerformanceStoragePage
     */
    public function setRespectPerformanceStoragePage($respectPerformanceStoragePage): void
    {
        $this->respectPerformanceStoragePage = $respectPerformanceStoragePage;
    }
}
