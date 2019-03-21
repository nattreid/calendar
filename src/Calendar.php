<?php

declare(strict_types=1);

namespace Nattreid\Calendar;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use NAttreid\Calendar\Lang\Translator;
use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;

/**
 * Class Calendar
 *
 * @author Attreid <attreid@gmail.com>
 */
class Calendar extends Control
{
	/** @var ITranslator */
	private $translator;

	/** @var DateTimeInterface */
	private $current;

	/** @var int */
	private $firstDayOfWeek = 0;

	/** @var bool */
	private $showOtherDays = false;

	/** @var int */
	private $numberOfMonths = 1;

	public function __construct()
	{
		parent::__construct();
		$this->translator = new Translator;
		$this->current = new DateTimeImmutable();
	}

	public function setFirstDayOfWeek(int $day): self
	{
		if ($day < 0 || $day > 6) {
			throw new InvalidArgumentException();
		}
		$this->firstDayOfWeek = $day;
		return $this;
	}

	public function setNumberOfMonths(int $months): self
	{
		if ($months < 1) {
			throw new InvalidArgumentException();
		}
		$this->numberOfMonths = $months;
		return $this;
	}

	public function setCurrentDate(DateTimeInterface $date): self
	{
		if ($date instanceof DateTime) {
			$this->current = DateTimeImmutable::createFromMutable($date);
		} else {
			$this->current = $date;
		}
		return $this;
	}

	public function setShowOtherDays($show = true): self
	{
		$this->showOtherDays = $show;
		return $this;
	}

	public function setTranslator(ITranslator $translator): self
	{
		$this->translator = $translator;
		return $this;
	}

	public function getTranslator(): Translator
	{
		return $this->translator;
	}

	private function getDays(): array
	{
		$days = [];
		for ($i = 0; $i < 7; $i++) {
			$days[] = $this->translator->translate('nattreid.calendar.days.' . (($i + $this->firstDayOfWeek) % 7));
		}
		return $days;
	}

	public function render(): void
	{
		$this->template->addFilter('translate', [$this->translator, 'translate']);

		$this->template->showOtherDays = $this->showOtherDays;
		$this->template->numberOfMonths = $this->numberOfMonths;
		$this->template->days = $this->getDays();

		$dateIterator = [];
		for ($i = 0; $i < $this->numberOfMonths; $i++) {
			$date = $this->current;
			if ($i > 0) {
				$date = $date->modify($i . ' MONTH');
			}
			$dateIterator[] = new DateIterator($date, $this->firstDayOfWeek);
		}
		$this->template->dateIterator = $dateIterator;

		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'calendar.latte');
		$this->template->render();
	}
}