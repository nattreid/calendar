var NattreidCalendar = new function () {
    var position = 0;
    var selected = [];

    function callAjax() {
        $.nette.ajax({
            url: $('.nattreid-calendar').data('handler'),
            data: {
                nattreidCalendarPosition: position
            }
        });
    }

    function select(obj) {
        var date = obj.data('date');
        switch (selected.length) {
            default:
                selected = [];
            case 0:
                selected.push(obj.data('date'));
                break;
            case 1:
                if (selected[0] > date) {
                    selected = [date, selected[0]];
                } else {
                    selected.push(date);
                }
        }
        obj.addClass('selected');
    }

    function deselect(obj) {
        obj.removeClass('selected');
    }

    $(document).ready(function () {

        $(document).on('click', '.nattreid-calendar a.prev', function () {
            if (position > 0) {
                --position;
                callAjax();
            }
            return false;
        });

        $(document).on('click', '.nattreid-calendar a.next', function () {
            position++;
            callAjax();
            return false;
        });

        $(document).on('click', '.nattreid-calendar a.prev', function () {
            if (position > 0) {
                --position;
                callAjax();
            }
            return false;
        });

        $(document).on('click', '.nattreid-calendar .day', function () {
            var obj = $(this);
            if (!obj.hasClass('disabled')) {
                if (obj.hasClass('selected')) {
                    deselect(obj);
                } else {
                    select(obj);
                }
            }
        });
    });
};

