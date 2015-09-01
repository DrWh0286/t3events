<?php
namespace Webfox\T3events\Domain\Model;

use Webfox\T3events\Domain\Model\Dto\CalendarConfiguration;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Dirk Wenzel <dirk.wenzel@cps-it.de>
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
class Calendar {

	/**
	 * @var CalendarMonth
	 */
	protected $currentMonth;

	/**
	 * View Mode
	 *
	 * @var int
	 */
	protected $viewMode;

	/**
	 * Display Period
	 *
	 * @var int
	 */
	protected $displayPeriod;

	/**
	 * Gets the current month
	 *
	 * @return CalendarMonth
	 */
	public function getCurrentMonth() {
		return $this->currentMonth;
	}

	/**
	 * Sets the current month
	 *
	 * @param CalendarMonth $calendarMonth
	 */
	public function setCurrentMonth(CalendarMonth $calendarMonth) {
		$this->currentMonth = $calendarMonth;
	}

	/**
	 * Gets the view mode
	 *
	 * @return int
	 */
	public function getViewMode() {
		return $this->viewMode;
	}

	/**
	 * Sets the view mode
	 *
	 * @param int $viewMode
	 */
	public function setViewMode($viewMode) {
		$this->viewMode = $viewMode;
	}

	/**
	 * Gets the display period
	 *
	 * @return int
	 */
	public function getDisplayPeriod() {
		return $this->displayPeriod;
	}

	/**
	 * Sets the display period
	 *
	 * @param int $displayPeriod
	 */
	public function setDisplayPeriod($displayPeriod) {
		$this->displayPeriod = $displayPeriod;
	}

	/**
	 * Gets an array of week day labels according to current locale
	 *
	 * @return array
	 */
	public function getWeekDayLabels() {
		$weekDays = array();
		switch ($this->getViewMode()) {
			case CalendarConfiguration::VIEW_MODE_MINI_MONTH:
				$monthFormat = '%a';
				break;
			default:
				$monthFormat = '%A';
		}

		for ($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++) {
			$weekDays[] = strftime($monthFormat, strtotime('next Monday +' . $dayOfWeek . ' days'));
		}

		return $weekDays;
	}

	/**
	 * Gets an array of localized month names
	 * @return array
	 */
	public function getMonthLabels() {
		$monthNames = array();
		for ($month = 1 ; $month <= 12; $month++) {
			$monthNames[] = date('F', mktime(0, 0, 0, $month));
		}

		return $monthNames;
	}
}
