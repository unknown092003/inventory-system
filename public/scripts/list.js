const listContainer = document.querySelectorAll("main > div");

listContainer.forEach((container) => {
  const qrCode = new QRCode(container.dataset.productNumber, {
    text: container.dataset.productNumber,
    width: 150,
    height: 150,
    correctLevel: QRCode.CorrectLevel.L,
  });
  //   qrCode.makeCode(container.dataset.productNumber);
});
