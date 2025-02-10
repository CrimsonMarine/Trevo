<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/styles1.css">
    <link rel="stylesheet" href="/assets/css/dropdownstyle.css">
    <link rel="stylesheet" href="/assets/css/sprite/famfamfam-silk.css">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <script src="/assets/js/postStyle.js"></script>
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