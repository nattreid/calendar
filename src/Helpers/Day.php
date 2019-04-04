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

	/** @var DateTimeImmutable */
	private $date, $default;

	public function __construct(Config $config, DateTimeImmutable $date, DateTimeImmutable $default)
	{
		$this->config = $config;
		$this->date = $date;
		$this->default = $default;
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

	protected function isOtherMonth(): bool
	{
		return (int) $this->date->format('n') !== (int) $this->default->format('n');
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