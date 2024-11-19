// JavaScript to toggle the menu box and close it when clicking outside
document.addEventListener("DOMContentLoaded", function () {
    const menuButton = document.getElementById("menuButton");
    const menuBox = document.getElementById("menuBox");

    // Toggle menu on button click
    menuButton.addEventListener("click", function (event) {
        event.stopPropagation();
        menuBox.style.display = menuBox.style.display === "none" ? "block" : "none";
    });

    // Close menu if clicked outside
    document.addEventListener("click", function () {
        menuBox.style.display = "none";
    });

    // Prevent menu from closing when clicking inside the menu box
    menuBox.addEventListener("click", function (event) {
        event.stopPropagation();
    });
});