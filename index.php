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
                <div class="header-container">
                    <div class="header-container">
                        <h1>Scan</h1>
                    </div>
                    <button id="switch-cam-btn" class="home-btn" style="display:none">Switch Camera</button>
                    <button class="home-btn">
  <a href="/inventory-system/pages/login.php" style="display:block; width:100%; height:100%">Login</a>
</button>
                </div>
                <div id="reader"></div>
                <p id="message"></p>
            </div>
        </div>
        
        <dialog id="item-dialog">
            <h2 id="prod-num"></h2>
            <p id="art"></p>
            <p id="prod-desc"></p>
            <p id="model"></p>
            <p id="accquisition-date"></p>
            <p id="person-acc"></p>
            <p id="status"></p>
            <p id="et"></p>
            <p id="sign"></p>
            
            <div class="dialog-actions">
                <button id="scan-again">
                    Scan Again
                </button>
                <button id="view-data" class="button primary">
                    View Data
                </button>
            </div>
        </dialog>
    </main>

    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const dialog = document.querySelector('dialog');
        const scanAgainBtn = document.getElementById('scan-again');
        const viewDataBtn = document.getElementById('view-data');
        const switchCamBtn = document.getElementById('switch-cam-btn');
        let currentCameraIndex = 0;
        let cameras = [];
        let isScanning = false;

        // Function to start scanning
        function startScanner() {
            // If no cameras detected, try to get them first
            if (cameras.length === 0) {
                getCameras().then(() => {
                    if (cameras.length > 0) {
                        startCamera();
                    } else {
                        document.getElementById('message').textContent = "No cameras found. Please ensure you have a camera connected.";
                    }
                });
                return;
            }
            
            startCamera();
        }

        function startCamera() {
            // Stop scanner if already running
            if (isScanning) {
                html5QrCode.stop().then(() => {
                    isScanning = false;
                    startCamera();
                }).catch(() => {
                    isScanning = false;
                    startCamera();
                });
                return;
            }
            
            const cameraId = cameras[currentCameraIndex].id;
            console.log("Starting camera:", cameraId);
            
            html5QrCode.start(
                cameraId,
                {
                    fps: 20,
                    qrbox: { width: 250, height: 250 }
                },
                (decodedText) => {
                    // Stop scanner when QR is detected
                    isScanning = false;
                    html5QrCode.stop().then(() => {
                        // Fetch item data from server
                        fetchItemData(decodedText);
                    }).catch(err => {
                        console.error("Error stopping scanner:", err);
                        // Restart scanner on error
                        startScanner();
                    });
                },
                (errorMessage) => {
                    // Parse error, ignore it.
                    console.log("QR Code scan error:", errorMessage);
                }
            ).then(() => {
                isScanning = true;
            }).catch((err) => {
                console.error("Error starting scanner:", err);
                document.getElementById('message').textContent = "Error accessing camera. Please ensure camera permissions are granted.";
                // Try next camera if available
                if (cameras.length > 1) {
                    toggleCamera();
                }
            });
        }

        // Function to get available cameras
        function getCameras() {
            return Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length > 0) {
                    cameras = devices;
                    if (devices.length > 1) {
                        switchCamBtn.style.display = 'block';
                        switchCamBtn.textContent = `Switch Camera (${devices[0].label || 'Camera 1'})`;
                    }
                    return devices;
                } else {
                    throw new Error("No cameras found");
                }
            }).catch(err => {
                console.log("Camera access error:", err);
                document.getElementById('message').textContent = "Could not access camera. Please ensure camera permissions are granted.";
                switchCamBtn.style.display = 'none';
                return [];
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
                    document.getElementById('art').textContent = `Article: ${data.article}`;
                    document.getElementById('prod-desc').textContent = `Description: ${data.description}`;
                    document.getElementById('model').textContent = `Model: ${data.model_number}`;
                    document.getElementById('accquisition-date').textContent = `Acquired: ${data.acquisition_date_cost}`;
                    document.getElementById('person-acc').textContent = `Accountable: ${data.person_accountable}`;
                    document.getElementById('status').textContent = `Status: ${data.remarks}`;
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
            if (cameras.length < 2) return;
            
            currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
            const currentCam = cameras[currentCameraIndex];
            console.log('Switching to camera:', currentCam.label || currentCam.id);
            switchCamBtn.textContent = `Switch Camera (${currentCam.label || `Camera ${currentCameraIndex + 1}`})`;
            startScanner();
        }

        // Initialize camera switching
        switchCamBtn.addEventListener('click', toggleCamera);

        // Start scanner when page loads
        document.addEventListener('DOMContentLoaded', () => {
            getCameras().then(() => {
                if (cameras.length > 0) {
                    startScanner();
                }
            });
        });
    </script>
</body>
</html>