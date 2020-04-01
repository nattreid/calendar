<?php

declare(strict_types=1);

namespace Nattreid\Calendar;

use DateTimeInterface;
use Nattreid\Calendar\Helpers\Config;
use Nattreid\Calendar\Helpers\Month;
use NAttreid\Calendar\Lang\Translator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\InvalidArgumentException;
use Nette\Localization\ITranslator;
use Nette\Utils\DateTime;

/**
 * Class Calendar
 *
 * @author Attreid <attreid@gmail.com>
 */
class Calendar extends Control
{
	/** @var Config */
	private $config;

	public function __construct()
	{
		parent::__construct();
		$this->config = new Config();
	}

	public function setCurrent(DateTimeInterface $date): self
	{
		$this->config->current = $date;
		return $this;
	}

	public function setFirstDayOfWeek(int $firstDayOfWeek): self
	{
		$this->config->firstDayOfWeek = $firstDayOfWeek;
		return $this;
	}

	public function disableBeforeCurrent(bool $disable = true): self
	{
		$this->config->disableBeforeCurrent = $disable;
		return $this;
	}

	public function setFormat(string $format): self
	{
		$this->config->format = $format;
		return $this;
	}

	public function setNumberOfMonths(int $numberOfMonths): self
	{
		$this->config->numberOfMonths = $numberOfMonths;
		return $this;
	}

	public function setPrev(string $text): self
	{
		$this->config->prev = $text;
		return $this;
	}

	public function setNext(string $text): self
	{
		$this->config->next = $text;
		return $this;
	}

	public function showMonth(bool $show = true): self
	{
		$this->config->showMonth = $show;
		return $this;
	}

	public function showYear(bool $show = true): self
	{
		$this->config->showYear = $show;
		return $this;
	}

	public function showOtherDays(bool $show = true): self
	{
		$this->config->showOtherDays = $show;
		return $this;
	}

	public function setTranslator(ITranslator $translator): self
	{
		$this->config->translator = $translator;
		return $this;
	}

	public function getTranslator(): Translator
	{
		return $this->config->translator;
	}

	/**
	 * @param DateTimeInterface[] $disabled
	 * @return $this
	 */
	public function setDisabledDays(array $disabled): self
	{
		$this->config->disabled = $disabled;
		return $this;
	}

	public function setDayRenderer(callable $renderer): self
	{
		$this->config->dayRenderer = $renderer;
		return $this;
	}

	public function setDayTemplate(string $template, array $args = []): self
	{
		$this->config->dayTemplate = $template;
		$this->config->dayTemplateArgs = $args;
		return $this;
	}

	public function setSelected(DateTimeInterface $from, DateTimeInterface $to): void
	{
		$format = 'Y-m-d';
		$now = new DateTime();
		if ($this->config->disableBeforeCurrent) {
			if ($from->format($format) < $now->format($format)) {
				throw new InvalidArgumentException('Invalid selected date');
			}
			if ($to->format($format) < $now->format($format)) {
				throw new InvalidArgumentException('Invalid selected date');
			}
		}
		foreach ($this->config->disabled as $disabled) {
			if ($from->format($format) <= $disabled->format($format) && $to->format($format) >= $disabled->format($format)) {
				throw new InvalidArgumentException('Invalid selected date');
			}
		}
		$this->config->from = $from;
		$this->config->to = $to;
	}

	private function getDaysOfWeek(): array
	{
		$days = [];
		for ($i = 0; $i < 7; $i++) {
			$days[] = $this->config->translator->translate('nattreid.calendar.days.' . (($i + $this->config->firstDayOfWeek) % 7));
		}
		return $days;
	}

	/**
	 * @throws AbortException
	 */
	public function handleChangeMonth(): void
	{
		$offset = (int) $this->presenter->getRequest()->getParameter('nattreidCalendarOffset');
		if ($this->presenter->isAjax()) {
			if (!$this->config->disableBeforeCurrent || $offset >= 0) {
				$this->config->offset = $offset;
				$this->redrawControl('container');
			}
		} else {
			$this->presenter->terminate();
		}
	}

	public function render(): void
	{
		$this->template->select = false;

		if (!$this->presenter->isAjax()) {
			$this->config->setOffsetToSelected();
			$this->template->select = true;
		}

		$this->template->name = $this->name;
		$this->template->addFilter('translate', [$this->config->translator, 'translate']);

		$this->template->config = $this->config;
		$this->template->daysOfWeek = $this->getDaysOfWeek();

		$months = [];
		for ($i = $this->config->offset; $i < $this->config->numberOfMonths + $this->config->offset; $i++) {
			$date = $this->config->current;
			if ($i !== 0) {
				$date = $date->modify($i . ' MONTH');
			}
			$months[] = new Month($this->config, $date);
		}
		$this->template->months = $months;

		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'calendar.latte');
		$this->template->render();
	}
}