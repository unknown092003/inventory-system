const html5QrCode = new Html5Qrcode("reader");

html5QrCode
  .start(
    {
      facingMode: "environment",
    },
    {
      fps: 10,
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
    document.getElementById("message").innerText =
      "Unable to start scanning. Please check your camera permissions.";
  });

function onScanSuccess(decodedText, decodedResult) {
  const form = new FormData();
  form.append("productNumber", decodedText);

  fetch("/inventory-system/item.php", {
    method: "POST",
    body: form,
  })
    .then((res) => {
      html5QrCode.pause();
      return res.json();
    })
    .then((data) => {
      // alert(JSON.stringify(data));
      setContent(data);
      document.querySelector("dialog").showModal();
      document.getElementById("scan-again").addEventListener("click", () => {
        document.querySelector("dialog").close();
        html5QrCode.resume();
      });
    });
}

function onScanError(errorMessage) {
  console.warn(`QR Code scan error: ${errorMessage}`);
}

function setContent(data) {
  const prodName = document.getElementById("prod-name");
  const prodId = document.getElementById("prod-id");
  const prodDesc = document.getElementById("prod-desc");

  prodName.textContent = data.description ?? "No Record Found";
  prodId.textContent = data.property_number;
  // prodDesc.textContent = data.product_description;
}
