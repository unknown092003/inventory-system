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
    <main>
        <div id="container">
            <div class="qr-area">
                <h1>Scan</h1>
                <div id="reader"></div>
                <p id="message"></p>
            </div>
        </div>
        <dialog>
            <h2 id="prod-name"></h2>
            <p><small id="prod-id"></small></p>
            <p id="prod-desc"></p>
            <button id="scan-again">Scan Again</button>
            <a href="/inventory-system/pages/login.php">Edit</a>
        </dialog>
    </main>
    <!-- <div class="bg-wrapper">
        <img class="logo-bg" src="/inventory-system/public/img/ocd.png" alt="Logo Background" />
        <div class="black-overlay"></div>
    </div>

    <div class="qr-area">
        <h1>Scan</h1>
        <div id="reader"></div>
        <div id="result" style="text-align: center; margin-top: 20px; color: black;"></div>
    </div>

    <dialog>
        <h2 id="prod-name"></h2>
        <p><small id="prod-id"></small></p>
        <p id="prod-desc"></p>
        <button id="scan-again">Scan Again</button>
    </dialog> -->

    <script src="/inventory-system/public/scripts/scanner.js"></script>
</body>

</html>