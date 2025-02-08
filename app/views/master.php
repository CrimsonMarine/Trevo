<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/styles1.css">
    <link rel="stylesheet" href="/assets/css/dropdownstyle.css">
    <link rel="stylesheet" href="/assets/css/sprite/famfamfam-silk.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <script>
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
    </script>
    <title><?php echo $this->e($title)?></title>
</head>
<body>
    <?php $this->insert('layouts/partials/header')?>
    <main>
        <div id="container">
            <?php echo $this->section('content')?>
        </div>
    </main>
    <?php $this->insert('layouts/partials/footer')?>
</body>
</html>