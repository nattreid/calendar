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

	public function __construct()
	{
		parent::__construct();
		$this->translator = new Translator;
		$this->current = new DateTimeImmutable();
	}

	public function setFirstDayOfWeek(int $day)
	{
		if ($day < 0 || $day > 6) {
			throw new InvalidArgumentException();
		}
		$this->firstDayOfWeek = $day;
	}

	public function setCurrentDate(DateTimeInterface $date): void
	{
		if ($date instanceof DateTime) {
			$this->current = DateTimeImmutable::createFromMutable($date);
		} else {
			$this->current = $date;
		}
	}

	public function setShowOtherDays($show = true): void
	{
		$this->showOtherDays = $show;
	}

	public function setTranslator(ITranslator $translator): void
	{
		$this->translator = $translator;
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
		$this->template->days = $this->getDays();
		$this->template->date = new DateIterator($this->current, $this->firstDayOfWeek);

		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'calendar.latte');
		$this->template->render();
	}
}