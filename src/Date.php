<?php

declare(strict_types=1);

namespace Nattreid\Calendar;

use DateTimeImmutable;
use Nette\SmartObject;

/**
 * Class Date
 *
 * @property-read DateTimeImmutable $date
 * @property-read int $day
 * @property-read bool $firstOfWeek
 * @property-read bool $lastOfWeek
 * @property-read bool $prevMonth
 * @property-read bool $nextMonth
 *
 * @author Attreid <attreid@gmail.com>
 */
class Date
{
	use SmartObject;

	/** @var DateTimeImmutable */
	private $date, $default;

	/** @var int */
	private $firstDayOfWeek;

	public function __construct(DateTimeImmutable $date, DateTimeImmutable $default, int $firstDayOfWeek)
	{
		$this->date = $date;
		$this->default = $default;
		$this->firstDayOfWeek = $firstDayOfWeek;
	}

	protected function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	protected function getDay(): int
	{
		return (int) $this->date->format('j');
	}

	protected function isFirstOfWeek(): bool
	{
		return (int) $this->date->format('w') === $this->firstDayOfWeek;
	}

	protected function isLastOfWeek(): bool
	{
		return (int) $this->date->format('w') === ($this->firstDayOfWeek + 6) % 7;
	}

	protected function isPrevMonth(): bool
	{
		return (int) $this->date->format('n') < (int) $this->default->format('n');
	}

	protected function isNextMonth(): bool
	{
		return (int) $this->date->format('n') > (int) $this->default->format('n');
	}
}