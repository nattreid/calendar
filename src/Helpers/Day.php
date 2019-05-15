<?php

declare(strict_types=1);

namespace Nattreid\Calendar\Helpers;

use DateTimeImmutable;
use Nette\SmartObject;

/**
 * Class Date
 *
 * @property-read DateTimeImmutable $date
 * @property-read string $formatDate
 * @property-read int $day
 * @property-read bool $firstOfWeek
 * @property-read bool $lastOfWeek
 * @property-read bool $prevMonth
 * @property-read bool $nextMonth
 * @property-read bool $otherMonth
 * @property-read bool $current
 * @property-read bool $disabled
 * @property-read bool $hidden
 *
 * @author Attreid <attreid@gmail.com>
 */
class Day
{
	use SmartObject;

	/** @var Config */
	private $config;

	/** @var Month */
	private $month;

	/** @var Month */
	private $date;

	public function __construct(Config $config, Month $month, DateTimeImmutable $date)
	{
		$this->config = $config;
		$this->month = $month;
		$this->date = $date;
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
		return (int) $this->date->format('w') === $this->config->firstDayOfWeek;
	}

	protected function isLastOfWeek(): bool
	{
		return (int) $this->date->format('w') === ($this->config->firstDayOfWeek + 6) % 7;
	}

	protected function isPrevMonth(): bool
	{
		return (int) $this->date->format('n') < (int) $this->month->default->format('n');
	}

	protected function isNextMonth(): bool
	{
		return (int) $this->date->format('n') > (int) $this->month->default->format('n');
	}

	protected function isOtherMonth(): bool
	{
		return $this->prevMonth || $this->nextMonth;
	}

	protected function isCurrent(): bool
	{
		return $this->date->format('Y-m-d') === $this->config->current->format('Y-m-d');
	}

	protected function isDisabled(): bool
	{
		if ($this->config->disableBeforeCurrent && $this->config->current->format('Y-m-d') > $this->date->format('Y-m-d')) {
			return true;
		}
		return isset($this->config->disabled[$this->date->format('Y-m-d')]);
	}

	protected function isHidden(): bool
	{
		return $this->otherMonth && !$this->config->showOtherDays;
	}

	protected function getFormatDate(): string
	{
		return $this->date->format($this->config->format);
	}

	public function render(): string
	{
		if ($this->config->dayRenderer !== null) {
			$func = $this->config->dayRenderer;
			return $func($this);
		}
		return (string) $this->day;
	}
}