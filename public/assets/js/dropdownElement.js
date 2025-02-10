document.addEventListener("DOMContentLoaded", () => {
    const dropdownToggle = document.getElementById("dropdownToggle");
    const dropdownMenu = document.getElementById("dropdownMenu");
    const selectedCountry = document.getElementById("selectedCountry");

    dropdownToggle.addEventListener("click", () => {
        dropdownMenu.classList.toggle("show");
    });

    dropdownMenu.addEventListener("click", (event) => {
        if (event.target.tagName === "LI") {
            dropdownToggle.textContent = event.target.textContent;
            selectedCountry.value = event.target.getAttribute("data-value");
            dropdownMenu.classList.remove("show");
        }
    });

    document.addEventListener("click", (event) => {
        if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove("show");
        }
    });
});