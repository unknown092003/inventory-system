<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/qr.css"> <!-- Adjust the path as necessary -->
    <script src="/inventory-system/public/scripts/html5-qrcode.min.js"></script> <!-- Ensure this path is correct -->
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
        <div id="result" style="text-align: center; margin-top: 20px; color: black;"></div> <!-- Changed color to black for better visibility -->
        <button>EDIT</button>
    </div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Handle the scanned QR code text
            document.getElementById('result').innerText = `QR Code Scanned: ${decodedText}`;
            console.log(`Scan result: ${decodedText}`, decodedResult);
        }

        function onScanError(errorMessage) {
            // Handle scan error
            console.warn(`QR Code scan error: ${errorMessage}`);
        }

        const html5QrCode = new Html5Qrcode("reader");

        // Start the QR code scanner
        html5QrCode.start(
            { facingMode: "environment" }, // Use the rear camera
            {
                fps: 30, // Frames per second
                qrbox: { width: 300, height: 200 } // Size of the scanning box
            },
            onScanSuccess,
            onScanError
        ).catch(err => {
            console.error(`Unable to start scanning: ${err}`);
            document.getElementById('result').innerText = "Unable to start scanning. Please check your camera permissions.";
        });
    </script>
</body>
</html>