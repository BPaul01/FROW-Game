(function () {
  fetch("/admin/auth.php", {
    method: "GET",
    headers: new Headers({
      Authentication: `Bearer ` + localStorage.getItem("token"),
    }),
  }).then((response) => {
    if (response.status === 401) {
      location.assign("/home/index.html");
    }
  });
})();
