// scanner.js
const html5QrCode = new Html5Qrcode("reader");
const dialog = document.querySelector('dialog');
const scanAgainBtn = document.getElementById('scan-again');
const viewDataBtn = document.getElementById('view-data');

// Function to start scanning
function startScanner() {
    html5QrCode.start(
        { facingMode: "environment" },
        {
            fps: 20,
            qrbox: { width: 250, height: 250 }
        },
        (decodedText) => {
            // Stop scanner when QR is detected
            html5QrCode.stop().then(() => {
                // Fetch item data from server
                fetchItemData(decodedText);
            }).catch(err => {
                console.error("Error stopping scanner:", err);
                // Restart scanner on error
                startScanner();
            });
        },
        () => {
            // Parse error, ignore it.
        })
    .catch((err) => {
        console.error("Error starting scanner:", err);
        // Restart scanner on error
        startScanner();
    });
}

// Function to fetch item data
function fetchItemData(propertyNumber) {
    fetch(`/inventory-system/api/getItem.php?property_number=${encodeURIComponent(propertyNumber)}`)
        .then(response => response.json())
        .then(data => {
            if (data.error || !data.property_number) {
                document.getElementById('message').textContent = "No item detected.";
                console.warn("No item detected or error:", data.error);
                // Restart scanner
                startScanner();
                return;
            }
            
            // Populate dialog with data
            document.getElementById('prod-num').textContent = `Property Number: ${data.property_number}`;
            document.getElementById('prod-desc').textContent = `Description: ${data.description}`;
            document.getElementById('model').textContent = `Model: ${data.model_number}`;
            document.getElementById('serial').textContent = `Serial: ${data.serial_number}`;
            document.getElementById('accquisition-date').textContent = `Acquired: ${data.acquisition_date_cost}`;
            document.getElementById('person-acc').textContent = `Accountable: ${data.person_accountable}`;
            document.getElementById('status').textContent = `Status: ${data.remarks }`;
            document.getElementById('sign').textContent = `Last Updated: ${data.signature_of_inventory_team_date}`;
            
            // Set property number as data attribute for view button
            viewDataBtn.setAttribute('data-property-number', data.property_number);
            
            // Show dialog
            dialog.showModal();
        })
        .catch(error => {
            document.getElementById('message').textContent = "Error fetching item data.";
            console.error("Error:", error);
            // Restart scanner
            startScanner();
        });
}

// Scan again button
scanAgainBtn.addEventListener('click', () => {
    dialog.close();
    startScanner();
});

// View Data button - redirect to data.php with search parameter
viewDataBtn.addEventListener('click', () => {
    const propertyNumber = viewDataBtn.getAttribute('data-property-number');
    window.location.href = `/inventory-system/pages/landing/data.php?page=data&search=${encodeURIComponent(propertyNumber)}`;
});

// Start scanner when page loads
startScanner();
// Add this to your scanner.js
const editItemBtn = document.getElementById('edit-item');
if (editItemBtn) {
    editItemBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const propertyNumber = document.getElementById('prod-num').textContent.replace('Property Number: ', '');
        window.location.href = `/inventory-system/pages/landing/edit-item.php?property_number=${encodeURIComponent(propertyNumber)}`;
    });
}