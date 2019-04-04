<?php

declare(strict_types=1);

namespace Nattreid\Calendar\Helpers;

use DateTimeImmutable;
use DateTimeInterface;
use Iterator;
use Nette\SmartObject;

/**
 * Class DateIterator
 *
 * @property-read string $name
 * @property-read int $year
 *
 * @author Attreid <attreid@gmail.com>
 */
class Month implements Iterator
{
	use SmartObject;

	/** @var string */
	private $format = 'Y-m-d';

	/** @var Config */
	private $config;

	/** @var DateTimeImmutable */
	private $date, $first, $last, $current;

	public function __construct(Config $config, DateTimeImmutable $date)
	{
		$this->config = $config;
		$this->date = $date;

		$this->current = $this->first = $this->getFirstDay();
		$this->last = $this->getLastDay();
	}

	protected function getName(): string
	{
		$month = $this->date->format('n') - 1;
		return $this->config->translator->translate('nattreid.calendar.months.' . $month);
	}

	protected function getYear(): int
	{
		return (int) $this->date->format('Y');
	}

	private function getFirstDayOfWeek(): string
	{
		$days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
		return $days[$this->config->firstDayOfWeek];
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
		return new Day($this->config, $this->current, $this->date);
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