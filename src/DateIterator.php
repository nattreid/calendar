<?php

declare(strict_types=1);

namespace Nattreid\Calendar;

use DateTimeImmutable;
use Iterator;

/**
 * Class DateIterator
 *
 * @author Attreid <attreid@gmail.com>
 */
class DateIterator implements Iterator
{

	/** @var string */
	private $format = 'Y-m-d';

	/** @var DateTimeImmutable */
	private $date, $first, $last, $current;

	/** @var int */
	private $firstDayOfWeek;

	public function __construct(DateTimeImmutable $date, int $firstDayOfWeek)
	{
		$this->date = $date;
		$this->firstDayOfWeek = $firstDayOfWeek;

		$this->current = $this->first = $this->getFirstDay();
		$this->last = $this->getLastDay();
	}

	private function getFirstDayOfWeek(): string
	{
		$days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
		return $days[$this->firstDayOfWeek];
	}

	private function getDaysOfMonth(): int
	{
		return cal_days_in_month(CAL_GREGORIAN, (int) $this->date->format('m'), (int) $this->date->format('Y'));
	}

	private function getFirstDay(): DateTimeImmutable
	{
		$day = $this->getFirstDayOfWeek();
		$date = $this->date->modify("first $day of this month");
		if ((int) $date->format('j') !== 1) {
			$date = $this->date->modify("last $day of previous month");
		}
		return $date;
	}

	private function getLastDay(): DateTimeImmutable
	{
		$day = $this->getFirstDayOfWeek();
		$date = $this->date->modify("last $day of this month");
		if ((int) $date->format('j') !== $this->getDaysOfMonth()) {
			$date = $this->date->modify("first $day of next month");
		}
		return $date;
	}

	/**
	 * Return the current element
	 * @link https://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current()
	{
		return new Date($this->current, $this->date, $this->firstDayOfWeek);
	}

	/**
	 * Move forward to next element
	 * @link https://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function next()
	{
		$this->current = $this->current->modify('+ 1 day');
		if ($this->current->format($this->format) === $this->last->format($this->format)) {
			$this->current = null;
		}
	}

	/**
	 * Return the key of the current element
	 * @link https://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return $this->current->format($this->format) ?? null;
	}

	/**
	 * Checks if current position is valid
	 * @link https://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid()
	{
		return $this->current !== null;
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link https://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 */
	public function rewind()
	{
		$this->current = $this->first;
	}
}