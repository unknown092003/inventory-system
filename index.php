<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/inventory-system/public/scripts/html5-qrcode.min.js"></script>
    <link rel="stylesheet" href="/inventory-system/public/styles/qr.css">
    <title>Scan</title>
</head>

<body>
    <div class="bg-wrapper">
        <img class="logo-bg" src="/inventory-system/public/img/ocd.png" alt="Logo Background" />
        <div class="black-overlay"></div>
    </div>

    <div class="qr-area">
        <h1>Scan</h1>
        <div id="reader"></div>
        <div id="result" style="text-align: center; margin-top: 20px; color: black;"></div>
        <button>EDIT</button>
    </div>

    <script src="/inventory-system/public/scripts/scanner.js"></script>
</body>

</html>