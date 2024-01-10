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
            if (code === "Enter") event.preventDefault();
            if (code === " " || code === "Enter" || code === "," || code === ";") {
                window.location = generateRoute(route);
            }
        });

        document.addEventListener('click', function (event) {
            if (event.target.id === 'filter_text_icon') {
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

// let CRUDList = function () {
//     let generateRoute = function (route) {
//         route = route + "?" + 'n=' + $('#filter_size option:selected').val();
//         route = route + "&" + 'b=' + $('#filter_text').val();
//
//         return route
//     }
//
//     let execute = function (route) {
//         $(document).on('change', '#filter_size', function () {
//             window.location = generateRoute(route);
//         });
//
//         $(document).on('keyup', '#filter_text', function (e) {
//             let code = e.key; // recommended to use e.key, it's normalized across devices and languages
//             if(code==="Enter") e.preventDefault();
//             if(code===" " || code==="Enter" || code===","|| code===";"){
//                 window.location = generateRoute(route);
//             } // missing closing if brace
//         });
//
//         $(document).on('click', '.btn-send', function () {
//             window.location = route;
//         });
//
//         $(document).on('click', '.btn-clean', function () {
//             window.location.href = route;
//         });
//     };
//
//     return {
//         init: function (route) {
//             execute(route);
//         },
//     };
// }();