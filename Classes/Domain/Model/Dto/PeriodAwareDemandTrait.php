<?php

namespace DWenzel\T3events\Domain\Model\Dto;

/**
 * Class PeriodAwareDemandTrait
 * Provides properties and methods for classes which
 * implement the PeriodAwareDemandInterface
 *
 * @package DWenzel\T3events\Domain\Model\Dto
 */
trait PeriodAwareDemandTrait
{
    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var string  A time period
     */
    protected $period;

    /**
     * @var string $periodType Type of period: month, day, year, specific
     */
    protected $periodType;

    /**
     * @var int $periodStart Start value used when constraining by day, month or year
     */
    protected $periodStart;

    /**
     * @var int $periodDuration Duration value used when constraining by day, month or year
     */
    protected $periodDuration;

    /**
     * @var \DateTime $startDate Start date when constraining by date
     */
    protected $startDate;

    /**
     * @var \DateTime $endDate End date when constraining by date
     */
    protected $endDate;

    /**
     * @var bool
     */
    protected $respectEndDate = false;

    /**
     * @return string The time limit for the demand
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param string $period A time limit for the demand
     * @return void
     */
    public function setPeriod($period = ''): void
    {
        $this->period = $period;
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
     * sets the date
     *
     * @param \DateTime $date
     * @return void
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getPeriodType()
    {
        return $this->periodType;
    }

    /**
     * @param string $type Type of period: day, month, year, date
     * @return void
     */
    public function setPeriodType($type): void
    {
        $this->periodType = $type;
    }

    /**
     * @return int
     */
    public function getPeriodStart()
    {
        return $this->periodStart;
    }

    /**
     * @param int $start $start Start value for time period (day, month or year)
     * @return void
     */
    public function setPeriodStart($start): void
    {
        $this->periodStart = (int) $start;
    }

    /**
     * @param int $duration Duration value for period (days, months, years)
     * @return void
     */
    public function setPeriodDuration($duration): void
    {
        $this->periodDuration = (int) $duration;
    }

    /**
     * @return int
     */
    public function getPeriodDuration()
    {
        return $this->periodDuration;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $date Start date
     * @return void
     */
    public function setStartDate($date): void
    {
        $this->startDate = $date;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $date End date
     * @return void
     */
    public function setEndDate($date): void
    {
        $this->endDate = $date;
    }

    /**
     * @return boolean
     */
    public function isRespectEndDate()
    {
        return $this->respectEndDate;
    }

    /**
     * @param boolean $respectEndDate
     */
    public function setRespectEndDate($respectEndDate): void
    {
        $this->respectEndDate = $respectEndDate;
    }
}
