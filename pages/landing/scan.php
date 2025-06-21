<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="/inventory-system/public/scripts/html5-qrcode.min.js"></script>
    <link rel="stylesheet" href="/inventory-system/public/styles/qr.css">
    <title>Inventory Scanner</title>
    <style>
        /* ===== MOBILE-FIRST STYLES ===== */
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
            height: 100vh;
            overflow-x: hidden;
            touch-action: manipulation;
        }

        #container {
            width: 100%;
            max-width: 100%;
            padding: 15px;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 0 5px;
            gap: 10px;
        }

        .header-buttons {
            display: flex;
            gap: 8px;
        }

        h1 {
            color: #2c3e50;
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
            flex-grow: 1;
            text-align: center;
        }

        #reader {
            width: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex-grow: 1;
            min-height: 200px;
            max-height: 60vh;
        }

        #message {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            margin: 10px 0;
            padding: 0 5px;
        }

        /* Button Styles */
        .btn {
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background-color 0.2s;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .btn-home {
            background-color: #34495e;
            color: white;
        }

        .btn-home:active {
            background-color: #2c3e50;
        }

        .btn-switch {
            background-color: #34495e;
            color: white;
        }

        .btn-switch:active {
            background-color: #2c3e50;
        }

        /* ===== MOBILE-OPTIMIZED DIALOG ===== */
        dialog {
            width: 95%;
            max-width: 100%;
            border: none;
            border-radius: 16px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            animation: fadeIn 0.3s ease-out;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -40%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
        }

        .dialog-header {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 16px 20px;
            text-align: center;
            position: relative;
        }

        .dialog-header h2 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 0 20px;
        }

        .dialog-content {
            padding: 15px;
            background-color: white;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            flex-grow: 1;
        }

        .item-detail {
            display: flex;
            flex-direction: column;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eee;
        }

        .item-label {
            font-weight: 600;
            color: #555;
            font-size: 0.85rem;
            margin-bottom: 4px;
        }

        .item-value {
            color: #2c3e50;
            font-size: 0.95rem;
            word-break: break-word;
        }

        .item-value.empty {
            color: #999;
            font-style: italic;
        }

        .dialog-actions {
            display: flex;
            flex-direction: column;
            padding: 15px;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            gap: 10px;
        }

        .button {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            font-size: 0.95rem;
            text-align: center;
            width: 100%;
        }

        .button.secondary {
            background-color: #e0e0e0;
            color: #333;
        }

        .button.secondary:active {
            background-color: #d0d0d0;
        }

        .button.primary {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
        }

        .button.primary:active {
            background: linear-gradient(135deg, #27ae60, #219653);
        }

        .button.edit {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .button.edit:active {
            background: linear-gradient(135deg, #2980b9, #2472a4);
        }

        .button-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-disposed {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Mobile-specific optimizations */
        @media (max-height: 700px) {
            .dialog-content {
                max-height: 50vh;
            }
        }

        @media (min-width: 500px) {
            .dialog-actions {
                flex-direction: row;
                justify-content: space-between;
            }
            
            .button-group {
                flex-direction: row;
                justify-content: flex-end;
            }
            
            .button {
                width: auto;
                min-width: 120px;
            }
            
            #scan-again {
                margin-right: auto;
            }
        }
    </style>
</head>
<body>
    <main>
        <div id="container">
            <div class="qr-area">
                <div class="header-container">
                    <button id="home-btn" class="btn btn-home" title="Back to Home">
                        Home
                    </button>
                    <h1>Inventory Scanner</h1>
                    <div class="header-buttons">
                        <button id="switch-cam-btn" class="btn btn-switch" style="display:none" title="Switch Camera">
                            Switch
                        </button>
                    </div>
                </div>
                
                <div id="reader"></div>
                <p id="message">Initializing scanner...</p>
            </div>
        </div>
        
        <dialog id="item-dialog">
            <div class="dialog-header">
                <h2 id="property-number">Property Number</h2>
            </div>
            
            <div class="dialog-content">
                <div class="item-detail">
                    <span class="item-label">Description</span>
                    <span id="description" class="item-value empty">N/A</span>
                </div>
                
                <div class="item-detail">
                    <span class="item-label">Model</span>
                    <span id="model-number" class="item-value empty">N/A</span>
                </div>
                
                <div class="item-detail">
                    <span class="item-label">Serial</span>
                    <span id="serial-number" class="item-value empty">N/A</span>
                </div>
                
                <div class="item-detail">
                    <span class="item-label">Acquired</span>
                    <span id="acquisition-date" class="item-value empty">N/A</span>
                </div>
                
                <div class="item-detail">
                    <span class="item-label">Accountable</span>
                    <span id="person-accountable" class="item-value empty">N/A</span>
                </div>
                
                <div class="item-detail">
                    <span class="item-label">Status</span>
                    <span id="status" class="item-value empty">N/A</span>
                </div>
                
                <div class="item-detail">
                    <span class="item-label">Type</span>
                    <span id="equipment-type" class="item-value empty">N/A</span>
                </div>
                
                <div class="item-detail">
                    <span class="item-label">Last Updated</span>
                    <span id="signature-date" class="item-value empty">N/A</span>
                </div>
            </div>
            
            <div class="dialog-actions">
                <button id="scan-again" class="button secondary">
                    Scan Again
                </button>
                <div class="button-group">
                    <button id="edit-item" class="button edit">
                        Edit
                    </button>
                    <button id="view-details" class="button primary">
                        Details
                    </button>
                </div>
            </div>
        </dialog>
    </main>

    <script>
        // ===== GLOBAL VARIABLES =====
        const html5QrCode = new Html5Qrcode("reader");
        const dialog = document.querySelector('dialog');
        const scanAgainBtn = document.getElementById('scan-again');
        const editItemBtn = document.getElementById('edit-item');
        const viewDetailsBtn = document.getElementById('view-details');
        const switchCamBtn = document.getElementById('switch-cam-btn');
        const homeBtn = document.getElementById('home-btn');
        
        let currentCameraIndex = 0;
        let cameras = [];
        let isScanning = false;
        let currentPropertyNumber = null;

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', () => {
            // Prevent zooming on mobile
            document.addEventListener('gesturestart', function(e) {
                e.preventDefault();
            });
            
            // Set up event listeners
            homeBtn.addEventListener('click', () => {
                window.location.href = '/inventory-system/pages/landing.php';
            });
            
            getCameras().then(() => {
                if (cameras.length > 0) {
                    startScanner();
                }
            });
            
            switchCamBtn.addEventListener('click', toggleCamera);
            scanAgainBtn.addEventListener('click', () => {
                dialog.close();
                startScanner();
            });
            
            editItemBtn.addEventListener('click', () => {
                if (currentPropertyNumber) {
                    window.location.href = `/inventory-system/pages/landing/edit.php?property_number=${encodeURIComponent(currentPropertyNumber)}`;
                }
            });
            
            viewDetailsBtn.addEventListener('click', () => {
                if (currentPropertyNumber) {
                    window.location.href = `/inventory-system/pages/landing/data.php?page=data&search=${encodeURIComponent(currentPropertyNumber)}`;
                }
            });
            
            // Handle mobile back button
            if (window.history && window.history.pushState) {
                window.addEventListener('popstate', function() {
                    if (dialog.open) {
                        dialog.close();
                        startScanner();
                    }
                });
            }
        });

        // ===== SCANNER FUNCTIONS =====
        function startScanner() {
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
            document.getElementById('message').textContent = "Scanning...";
            
            // Adjust QR box size for mobile
            const qrboxSize = Math.min(300, window.innerWidth - 40);
            
            html5QrCode.start(
                cameraId,
                {
                    fps: 10, // Slightly lower FPS for mobile performance
                    qrbox: { width: qrboxSize, height: qrboxSize },
                    aspectRatio: 1.0 // Square aspect ratio
                },
                (decodedText) => {
                    isScanning = false;
                    html5QrCode.stop().then(() => {
                        processScannedCode(decodedText);
                    }).catch(err => {
                        console.error("Error stopping scanner:", err);
                        startScanner();
                    });
                },
                (errorMessage) => {
                    console.log("QR Code scan error:", errorMessage);
                }
            ).then(() => {
                isScanning = true;
            }).catch((err) => {
                console.error("Error starting scanner:", err);
                document.getElementById('message').textContent = "Error accessing camera. Please ensure camera permissions are granted.";
                if (cameras.length > 1) {
                    toggleCamera();
                }
            });
        }

        function getCameras() {
            return Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length > 0) {
                    cameras = devices;
                    if (devices.length > 1) {
                        switchCamBtn.style.display = 'block';
                        switchCamBtn.textContent = `Switch (${devices[0].label || 'Camera 1'})`;
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

        function toggleCamera() {
            if (cameras.length < 2) return;
            
            currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
            const currentCam = cameras[currentCameraIndex];
            switchCamBtn.textContent = `Switch (${currentCam.label || `Camera ${currentCameraIndex + 1}`})`;
            startScanner();
        }

        // Always use back camera first if available
        function selectDefaultCamera() {
            let backCamIndex = cameras.findIndex(cam =>
                /back|rear|environment/i.test(cam.label)
            );
            if (backCamIndex === -1) backCamIndex = 0;
            currentCameraIndex = backCamIndex;
        }

        // Patch getCameras to select back camera by default
        const originalGetCameras = getCameras;
        getCameras = function() {
            return originalGetCameras().then(devices => {
                if (devices.length > 0) {
                    selectDefaultCamera();
                    const currentCam = cameras[currentCameraIndex];
                    if (switchCamBtn && currentCam) {
                        switchCamBtn.textContent = `Switch (${currentCam.label || `Camera ${currentCameraIndex + 1}`})`;
                    }
                }
                return devices;
            });
        };

        // ===== ITEM PROCESSING FUNCTIONS =====
        function processScannedCode(scannedText) {
            currentPropertyNumber = scannedText.trim();
            fetchItemData(currentPropertyNumber);
        }

        function fetchItemData(propertyNumber) {
            document.getElementById('message').textContent = "Fetching item data...";
            
            fetch(`/inventory-system/api/getItem.php?property_number=${encodeURIComponent(propertyNumber)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error || !data.property_number) {
                        throw new Error(data.error || "Item not found");
                    }
                    
                    displayItemData(data);
                    dialog.showModal();
                    // Add state to history for back button
                    if (window.history && window.history.pushState) {
                        history.pushState({ dialogOpen: true }, '');
                    }
                })
                .catch(error => {
                    document.getElementById('message').textContent = error.message || "Error fetching item data.";
                    console.error("Error:", error);
                    startScanner();
                });
        }

        function displayItemData(data) {
            // Update dialog display with proper formatting
            document.getElementById('property-number').textContent = data.property_number;
            
            updateField('description', data.description);
            updateField('model-number', data.model_number);
            updateField('serial-number', data.serial_number);
            updateField('acquisition-date', formatDate(data.acquisition_date));
            updateField('person-accountable', data.person_accountable);
            updateStatusField(data.remarks);
            updateField('equipment-type', data.equipment_type);
            updateField('signature-date', formatDate(data.signature_of_inventory_team_date));
            
            document.getElementById('message').textContent = "Item found!";
        }

        function updateField(fieldId, value) {
            const element = document.getElementById(fieldId);
            if (value) {
                element.textContent = value;
                element.classList.remove('empty');
            } else {
                element.textContent = 'N/A';
                element.classList.add('empty');
            }
        }

        function updateStatusField(status) {
            const element = document.getElementById('status');
            if (status) {
                element.textContent = status;
                element.classList.remove('empty');
                
                // Remove all status classes
                element.classList.remove('status-active', 'status-inactive', 'status-disposed');
                
                // Add appropriate status class
                if (status.toLowerCase().includes('service')) {
                    element.classList.add('status-active');
                } else if (status.toLowerCase().includes('unservice')) {
                    element.classList.add('status-inactive');
                } else if (status.toLowerCase().includes('disposed')) {
                    element.classList.add('status-disposed');
                }
            } else {
                element.textContent = 'N/A';
                element.classList.add('empty');
            }
        }

        function formatDate(dateString) {
            if (!dateString) return null;
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } catch (e) {
                return dateString; // Return original if formatting fails
            }
        }
    </script>
</body>
</html>