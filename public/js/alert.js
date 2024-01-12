//== Class definition

let Alert = function () {

    let e = Swal.mixin({
        buttonsStyling: false,
        target: "#page-container",
        confirmButtonText: "Aceptar",
        customClass: {
            confirmButton: "btn btn-success m-1",
            cancelButton: "btn btn-danger m-1",
            input: "form-control"
        }
    });

    //== Private functions
    let basic = function (message, state, title) {
        e.fire(title || '', message, state)
    }

    return {
        info: function(message, title=undefined) {
            basic(message, 'info',title);
        },
        success: function(message, title=false) {
            basic(message, 'success',title);
        },
        warning: function(message, title=false) {
            basic(message, 'warning',title);
        },
        danger: function(message, title=false) {
            basic(message, 'error',title);
        },
    };
}();