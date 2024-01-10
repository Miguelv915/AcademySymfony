//== Class definition
let Notify = function () {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        // "positionClass": "toastr-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        'body-output-type': 'trustedHtml'
    };
    return {
        info: function(message, title = null) {
            toastr.options.timeOut = 5000;
            toastr.info(message, title);
        },
        success: function(message, title = null) {
            toastr.options.timeOut = 5000;
            toastr.success(message, title);
        },
        warning: function(message, title = null) {
            toastr.options.timeOut = 10000;
            toastr.warning(message, title);
        },
        danger: function(message, title = null) {
            toastr.options.timeOut = 20000;
            toastr.error(message, title);
        },
        error: function(message, title = null) {
            toastr.options.timeOut = 0;
            toastr.error(message, title);
        },
    };
}();