# Kalendář pro Nette Framework

### Použití
```php
protected function createComponentCalendar(): \Nattreid\Calendar\Calendar
{
    $calendar = new \Nattreid\Calendar\Calendar();
    $calendar
        ->showOtherDays()
        ->showMonth()
        ->showYear()
        ->setFirstDayOfWeek(1)
        ->setNumberOfMonths(3)
        ->setFormat('Y-m-d')
        ->disableBeforeCurrent()
        ->setDisabledDays([
            (new DateTime)->modify('+ 1 day'),
            (new DateTime)->modify('+ 7 day'),
            (new DateTime)->modify('+ 8 day'),
            (new DateTime)->modify('+ 14 day'),
        ])
        ->setDayRenderer(function (Day $day) {
            return 'Day is ' . $day->day;
        })
        ->setDayTemplate(__DIR__ . '/templates/day.latte', ['foo' => 5])
        ->getTranslator()->setLang('en');

    return $calendar;
}
```
