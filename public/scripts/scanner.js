const html5QrCode = new Html5Qrcode("reader");
// document.querySelector("dialog").showModal();
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
  const prodNum = document.getElementById("prod-num");
  const prodDesc = document.getElementById("prod-desc");
  const modal = document.getElementById("model");
  const serial = document.getElementById("serial");
  const accquisitionDate = document.getElementById("accquisition-date");
  const personAcc = document.getElementById("person-acc");
  const status = document.getElementById("status");
  const sign = document.getElementById("sign");

  prodNum.textContent = "Product Number:" + data.property_number ?? "No Record Found";
  prodDesc.textContent = data.description;
  modal.textContent = data.model_number;
  serial.textContent = data.serial_number;
  accquisitionDate.textContent = data.acquisition_date_cost;
  personAcc.textContent = data.person_accountable;
  status.textContent = data.status;
  sign.textContent = data.signature_of_inventory_team_date;
}
