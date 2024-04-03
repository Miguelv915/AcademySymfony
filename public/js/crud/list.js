/** CRUD LIST JS */
let CRUDList = function () {
    let generateRoute = function (route) {
        route = route + "?" + 'limit=' + document.querySelector('#filter_size option:checked').value;
        route = route + "&" + 'b=' + document.querySelector('#filter_text').value;

        return route;
    }

    let execute = function (route) {
        document.addEventListener('change', function (event) {
            if (event.target.id === 'filter_size') {
                window.location = generateRoute(route);
            }
        });

        document.addEventListener('keyup', function (event) {
            let code = event.key;
            if (code === "Enter") {
                event.preventDefault();
                window.location = generateRoute(route);
            }
        });

        document.addEventListener('click', function (event) {
            if (event.target.id === 'filter_text_icon' || event.target.parentElement.id === 'filter_text_icon') {
                window.location = generateRoute(route);
            }else if (event.target.classList.contains('btn-send')) {
                window.location = route;
            } else if (event.target.classList.contains('btn-clean')) {
                window.location.href = route;
            }
        });
    };

    return {
        init: function (route) {
            execute(route);
        },
    };
}();