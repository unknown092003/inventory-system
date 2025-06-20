// scanner.js
const html5QrCode = new Html5Qrcode("reader");
const dialog = document.querySelector('dialog');
const scanAgainBtn = document.getElementById('scan-again');
const viewDataBtn = document.getElementById('view-data');
const switchCamBtn = document.querySelector('button.home-btn:first-child'); // More specific selector
let currentCameraIndex = 0;
let cameras = [];

// Function to start scanning
function startScanner() {
    // Stop scanner if already running
    if (html5QrCode.isScanning) {
        html5QrCode.stop().catch(() => {});
    }
    
    html5QrCode.start(
        cameras[currentCameraIndex].id,
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

// Camera switch functionality
function toggleCamera() {
    currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
    const currentCam = cameras[currentCameraIndex];
89|     console.log('Switching to camera:', currentCam.label || currentCam.id);
    switchCamBtn.textContent = `Switch Camera (${currentCam.label || `Camera ${currentCameraIndex + 1}`})`;
    startScanner();
}

// Get available cameras and initialize
Html5Qrcode.getCameras().then(devices => {
    if (devices && devices.length > 1) {
        cameras = devices;
        switchCamBtn.style.display = 'block';
        switchCamBtn.addEventListener('click', toggleCamera);
    }
}).catch(err => {
    console.log("Camera access error:", err);
    switchCamBtn.style.display = 'none';
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