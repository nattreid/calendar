<?php

declare(strict_types=1);

namespace Nattreid\Calendar;

use DateTimeInterface;
use Nattreid\Calendar\Helpers\Config;
use Nattreid\Calendar\Helpers\Month;
use NAttreid\Calendar\Lang\Translator;
use Nette\Application\AbortException;
use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;

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
		$position = (int) $this->presenter->getRequest()->getParameter('nattreidCalendarPosition');
		if ($this->presenter->isAjax()) {
			if (!$this->config->disableBeforeCurrent || $position >= 0) {
				$this->config->offset = $position;
				$this->redrawControl('container');
			}
		} else {
			$this->presenter->terminate();
		}
	}

	public function render(): void
	{
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