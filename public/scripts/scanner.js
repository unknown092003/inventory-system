function onScanSuccess(decodedText, decodedResult) {
  document.getElementById(
    "result"
  ).innerText = `QR Code Scanned: ${decodedText}`;

  fetch("/inventory-system/item.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      productNumber: decodedText,
    }),
  })
    .then((res) => res.json())
    .then((data) => console.log(data));
}

function onScanError(errorMessage) {
  console.warn(`QR Code scan error: ${errorMessage}`);
}

const html5QrCode = new Html5Qrcode("reader");

html5QrCode
  .start(
    {
      facingMode: "environment",
    },
    {
      fps: 30,
      qrbox: {
        width: 200,
        height: 200,
      },
    },
    onScanSuccess,
    onScanError
  )
  .catch((err) => {
    console.error(`Unable to start scanning: ${err}`);
    document.getElementById("result").innerText =
      "Unable to start scanning. Please check your camera permissions.";
  });
