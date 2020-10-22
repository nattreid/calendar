(function ($, window) {
    if (window.jQuery === undefined) {
        console.error('Plugin "jQuery" required by "Nattreid/Calendar" is missing!');
        return;
    }

    $.fn.nattreidCalendar = function (options) {
        var _this = $(this);
        var handler = _this.find('.nattreid-calendar').data('handler');
        var disableBeforeCurrent = _this.find('.nattreid-calendar').data('disable-before-current');
        var offset = _this.find('.nattreid-calendar').data('offset');
        var selected = [];

        _this.find('.nattreid-calendar .day.selected').each(function (index, item) {
            var obj = $(item);
            if (!obj.hasClass('hiddenCell')) {
                selected.push(obj.data('date'));
            }
        });

        var opts = $.extend({}, {
            onSelected: function (selected) {
            }
        }, options);

        this.getSelected = getSelected;

        function getSelected() {
            var result = [];
            selected.forEach(function (date) {
                var parts = date.split('-');

                result.push(new Date(parseInt(parts[0], 10),
                    parseInt(parts[1], 10) - 1,
                    parseInt(parts[2], 10)));
            });

            return result;
        }

        function callAjax() {
            _this.find('.nattreid-calendar .spinner').addClass('active');
            
            $.nette.ajax({
                url: handler,
                data: {
                    nattreidCalendarOffset: offset
                }
            }).done(function () {
                selected.forEach(function (item) {
                    get(item).addClass('selected');
                });
                var selection = getSelection(selected);
                if (selection) {
                    selection.addClass('selection');
                }
            });
        }

        function get(selector) {
            return _this.find('.nattreid-calendar .day[data-date="' + selector + '"]');
        }

        function select(obj) {
            var date = obj.data('date');
            switch (selected.length) {
                default:
                case 2:
                    deselect();
                case 0:
                    selected.push(date);
                    obj.addClass('selected');
                    break;
                case 1:
                    var arr = selected.slice();
                    if (arr[0] > date) {
                        arr = [date, arr[0]];
                    } else {
                        arr.push(date);
                    }
                    var selection = getSelection(arr);
                    if (selection) {
                        selection.addClass('selection');
                        obj.addClass('selected');
                        selected = arr;
                    }
                    opts.onSelected(getSelected());
                    break;
            }
        }

        function getSelection(date) {
            var selection;
            var first = get(date[0]);
            var second = get(date[1]);

            if (first.parent().is(second.parent())) {
                selection = first.nextUntil(second);
            } else {
                var other = first.closest('.month').nextUntil(second.closest('.month')).find('.day');
                selection = first.nextAll().add(other).add(second.prevAll());
            }
            selection = selection.not('.hiddenCell');

            if (!selection.hasClass('disabled')) {
                return selection;
            }
            return false;
        }

        function deselect(obj) {
            _this.find('.nattreid-calendar .day').removeClass('selection');
            var items = [];

            if (obj != null) {
                selected.forEach(function (item) {
                    if (obj.is(get(item))) {
                        obj.removeClass('selected');
                    } else {
                        items.push(item);
                    }
                });
            } else {
                selected.forEach(function (item) {
                    get(item).removeClass('selected');
                });
            }

            selected = items;
        }

        $(this).on('click', '.nattreid-calendar a.prev', function () {
            if (!disableBeforeCurrent || offset > 0) {
                --offset;
                callAjax();
            }
            return false;
        });

        $(this).on('click', '.nattreid-calendar a.next', function () {
            offset++;
            callAjax();
            return false;
        });

        $(this).on('click', '.nattreid-calendar .day', function () {
            var obj = $(this);
            if (!obj.hasClass('disabled')) {
                if (obj.hasClass('selected')) {
                    deselect(obj);
                } else {
                    select(obj);
                }
            }
        });

        return this;
    };

})(jQuery, window);

