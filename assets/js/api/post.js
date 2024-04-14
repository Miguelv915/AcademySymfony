export default async function postData(url = "", data = {}) {
    const response = await fetch(url, {
        method: "POST", // *GET, POST, PUT, DELETE, etc.
        // mode: "cors", // no-cors, *cors, same-origin
        // cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        // credentials: "same-origin", // include, *same-origin, omit
        headers: {
            "Content-Type": "application/json",
            // 'Content-Type': 'application/x-www-form-urlencoded',
        },
        // redirect: "follow", // manual, *follow, error
        // referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        body: JSON.stringify(data), // body data type must match "Content-Type" header
    });

    return response.json(); // parses JSON response into native JavaScript objects
};

global.postData = postData;

// postData(url, data)
//     .then(result => {
//         // Operations
//     })
//     .catch(error => {
//         console.error('Error:', error);
//     });


// native
// fetch(`https://`)
//     .then(response => response.json())
//     .then(result => {
//         // Operations
//     })
//     .catch(error => {
//         console.error("Error:", error);
//     });
