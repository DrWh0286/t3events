<?php

namespace DWenzel\T3events\ViewHelpers\Format;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Dirk Wenzel <wenzel@cps-it.de>
 *  All rights reserved
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the text file GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

trait DateRangeTrait
{
    /**
     * Get date range from timestamps
     *
     * @param array $timestamps An ordered array of timestamps
     * @return array
     */
    protected function getDateRange(array $timestamps)
    {
        $format = static::DEFAULT_DATE_FORMAT;

        $endFormat = $this->arguments['endFormat'] ?? '';
        $startFormat = $this->arguments['startFormat'] ?? '';
        $glue = $this->arguments['glue'] ?? '';

        if (!empty($this->arguments['format'])) {
            $format = $this->arguments['format'];
        }
        if (empty($startFormat)) {
            $startFormat = $format;
        }
        if (empty($endFormat)) {
            $endFormat = $format;
        }
        if (empty($glue)) {
            $glue = static::DEFAULT_GLUE;
        }

        if (strpos($startFormat, '%') !== false
            && strpos($endFormat, '%') !== false
        ) {
            throw new \RuntimeException('Please don\'t use % in date format!');
        }

        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamps[0]);

        $dateRange = $dateTime->format($startFormat);

        if (count($timestamps) > 1) {
            $dateTimeLast = new \DateTime();
            $dateTimeLast->setTimestamp(end($timestamps));

            $dateRange .= $glue . $dateTimeLast->format($startFormat);
        }

        return $dateRange;
    }
}
