<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="/inventory-system/public/scripts/html5-qrcode.min.js"></script>
    <link rel="stylesheet" href="/inventory-system/public/styles/qr2.css">
    <title>Scan</title>
</head>

<body>
    <main>
        <div id="container">
            <div class="qr-area">
                <div class="header-container">
                    <div class="header-container">
                        <h1>Scan</h1>
                    </div>
                        <button class="home-btn" onclick="window.location.href='../landing.php'">HOME</button>
                    </div>
                <div id="reader"></div>
                <p id="message"></p>
            </div>
        </div>
        .
<dialog id="item-dialog">
    <h2 id="prod-num"></h2>
    <p id="prod-desc"></p>
    <p id="model"></p>
    <p id="serial"></p>
    <p id="accquisition-date"></p>
    <p id="person-acc"></p>
    <p id="status"></p>
    <p id="et"></p>
    <p id="sign"></p>
    
    <div class="dialog-actions">

        
        <!-- <a href="#" id="edit-item" class="button">Edit</a> -->
        
        <button id="view-data" class="button primary">
            
        </button>
        </div> 
       <button id="scan-again">
        Scan Again
            <!-- <img src="/inventory-system/public/img/scan-icon.png" alt="Scan Again"> -->
        </button>
</dialog>

    </main>
    <script src="/inventory-system/public/scripts/scanner.js"></script>
</body>

</html>