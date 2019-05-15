<?php

declare(strict_types=1);

namespace Nattreid\Calendar\Helpers;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use NAttreid\Calendar\Lang\Translator;
use Nette\Localization\ITranslator;
use Nette\SmartObject;

/**
 * Class Config
 *
 * @property Translator $translator
 * @property DateTimeImmutable $current
 * @property int $firstDayOfWeek
 * @property int $numberOfMonths
 * @property int $offset
 * @property bool $showOtherDays
 * @property bool $showMonth
 * @property bool $showYear
 * @property bool $disableBeforeCurrent
 * @property string $prev
 * @property string $next
 * @property string $format
 * @property DateTimeInterface[] $disabled
 * @property callable|null $dayRenderer
 * @property string|null $dayTemplate
 * @property array $dayTemplateArgs
 *
 * @author Attreid <attreid@gmail.com>
 */
class Config
{
	use SmartObject;

	public function __construct()
	{
		$this->translator = new Translator;
		$this->current = new DateTimeImmutable();
	}

	/** @var ITranslator */
	private $translator;

	/** @var DateTimeInterface */
	private $current;

	/** @var int */
	private $firstDayOfWeek = 0;

	/** @var int */
	private $numberOfMonths = 1;

	/** @var int */
	private $offset = 0;

	/** @var bool */
	private $showOtherDays = false;

	/** @var bool */
	private $showMonth = false;

	/** @var bool */
	private $showYear = false;

	/** @var bool */
	private $disableBeforeCurrent = false;

	/** @var string */
	private $prev = '❮';

	/** @var string */
	private $next = '❯';

	/** @var string */
	private $format = 'Y-m-d';

	/** @var DateTimeInterface[] */
	private $disabled = [];

	/** @var callable|null */
	private $dayRenderer;

	/** @var string|null */
	private $dayTemplate;

	/** @var array */
	private $dayTemplateArgs = [];

	protected function getTranslator(): Translator
	{
		return $this->translator;
	}

	protected function setTranslator(ITranslator $translator): void
	{
		$this->translator = $translator;
	}

	protected function setCurrent(DateTimeInterface $date): void
	{
		if ($date instanceof DateTime) {
			$this->current = DateTimeImmutable::createFromMutable($date);
		} else {
			$this->current = $date;
		}
	}

	protected function getCurrent(): DateTimeImmutable
	{
		return $this->current;
	}

	protected function getFirstDayOfWeek(): int
	{
		return $this->firstDayOfWeek;
	}

	protected function setFirstDayOfWeek(int $firstDayOfWeek): void
	{
		if ($firstDayOfWeek < 0 || $firstDayOfWeek > 6) {
			throw new InvalidArgumentException();
		}
		$this->firstDayOfWeek = $firstDayOfWeek;
	}

	protected function getNumberOfMonths(): int
	{
		return $this->numberOfMonths;
	}

	protected function setNumberOfMonths(int $numberOfMonths): void
	{
		if ($numberOfMonths < 1) {
			throw new InvalidArgumentException();
		}
		$this->numberOfMonths = $numberOfMonths;
	}

	protected function getOffset(): int
	{
		return $this->offset;
	}

	protected function setOffset(int $offset): void
	{
		$this->offset = $offset;
	}

	protected function isShowOtherDays(): bool
	{
		return $this->showOtherDays;
	}

	protected function setShowOtherDays(bool $show): void
	{
		$this->showOtherDays = $show;
	}

	protected function isShowMonth(): bool
	{
		return $this->showMonth;
	}

	protected function setShowMonth(bool $show): void
	{
		$this->showMonth = $show;
	}

	protected function isShowYear(): bool
	{
		return $this->showYear;
	}

	protected function setShowYear(bool $show): void
	{
		$this->showYear = $show;
	}

	protected function isDisableBeforeCurrent(): bool
	{
		return $this->disableBeforeCurrent;
	}

	protected function setDisableBeforeCurrent(bool $disable): void
	{
		$this->disableBeforeCurrent = $disable;
	}

	protected function getPrev(): string
	{
		return $this->prev;
	}

	protected function setPrev(string $text): void
	{
		$this->prev = $text;
	}

	protected function getNext(): string
	{
		return $this->next;
	}

	protected function setNext(string $text): void
	{
		$this->next = $text;
	}

	protected function getFormat(): string
	{
		return $this->format;
	}

	protected function setFormat(string $format): void
	{
		$this->format = $format;
	}

	protected function getDisabled(): array
	{
		return $this->disabled;
	}

	protected function setDisabled(array $disabled): void
	{
		$this->disabled = [];
		foreach ($disabled as $date) {
			if (!$date instanceof DateTimeInterface) {
				throw new InvalidArgumentException('Disabled date must be array if DateTimeInterface');
			}
			$this->disabled[$date->format('Y-m-d')] = $date;
		}
	}

	protected function getDayRenderer(): ?callable
	{
		return $this->dayRenderer;
	}

	protected function setDayRenderer(?callable $dayRenderer): void
	{
		$this->dayRenderer = $dayRenderer;
	}

	protected function getDayTemplate(): ?string
	{
		return $this->dayTemplate;
	}

	protected function setDayTemplate(?string $template): void
	{
		if ($template !== null && !file_exists($template)) {
			throw new InvalidArgumentException("Template '$template' doesn't exists");
		}
		$this->dayTemplate = $template;
	}

	protected function getDayTemplateArgs(): array
	{
		return $this->dayTemplateArgs;
	}

	protected function setDayTemplateArgs(array $args): void
	{
		$this->dayTemplateArgs = $args;
	}

}