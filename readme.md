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
        ->setSelected(new DateTime, (new DateTime)->modify('+ 1 day'))
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

```javascript
function parseDate(date) {
    return date.getDate() + '.' + date.getMonth() + '.' + date.getFullYear();
}

var calendar = $('#calendar').nattreidCalendar({
    onSelected: function (selected) {
        $('form input[name="from"]').val(parseDate(selected[0]));
        $('form input[name="to"]').val(parseDate(selected[1]));
    }
});

var selectedDates = calendar.getSelected();
```
