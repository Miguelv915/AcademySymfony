/** CRUD LIST JS */
const filterText = document.getElementById("filter_text");
const filterSize = document.getElementById("filter_size");

let CRUDList = function () {
    let generateRoute = function (route) {
        if (filterText) {
            route += '?b=' + filterText.value;
        }
        if (filterSize) {
            route += '&limit=' + filterSize.value;
        }

        return route;
    }

    let execute = function (route) {
        if (filterSize) {
            filterSize.addEventListener('change', function () {
                window.location = generateRoute(route);
            });
        }

        filterText.addEventListener('keyup', function (event) {
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