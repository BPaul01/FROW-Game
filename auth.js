(function () {
    fetch("/validate", {
        method: "GET",
        headers: new Headers({
            Authentication: `Bearer ` + localStorage.getItem("token"),
        }),
    }).then((response) => {
        //debugger;
        if (response.status === 401) {
            location.assign("/home/index.html");
        }
    });
})();
