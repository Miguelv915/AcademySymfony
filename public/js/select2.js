let Select2 = function () {
    $('.js-select2:not(.js-select2-enabled)').each((index, element) => {
        let el = $(element);

        el.addClass('js-select2-enabled').select2({
            allowClear: true,
            placeholder: el.data('placeholder') || false,
            dropdownAutoWidth : true,
            width: '100%'
        });
    });
}

$(document).ready(function() {
    Select2();
});