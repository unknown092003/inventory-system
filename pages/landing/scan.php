<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="/inventory-system/public/scripts/html5-qrcode.min.js"></script>
    <title>Lumos Inventory Scanner</title>
    <style>
        :root {
            --primary: #001938;
            --secondary: #ffa200;
        }
        
        @font-face {
            font-family: 'Poppins';
            src: url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--primary);
            min-height: 100vh;
            color: #333;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 10px;
                max-width: 100%;
            }
            
            .header h1 {
                font-size: 1.6rem;
            }
            
            .header-actions {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .scanner-container {
                padding: 15px;
            }
            
            #reader {
                min-height: 280px;
            }
            
            dialog {
                width: 95%;
                max-height: 90vh;
            }
            
            .dialog-content {
                max-height: 50vh;
            }
            
            .dialog-actions {
                flex-direction: column;
            }
            
            .button {
                width: 100%;
            }
        }

        /* Header Styles */
        .header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
            position: relative;
            z-index: 2;
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: white;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: relative;
            display: inline-block;
        }
        
        .header h1::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: var(--secondary);
            border-radius: 3px;
        }

        .header p {
            font-size: 1rem;
            color: rgba(255,255,255,0.85);
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 15px;
        }

        @media screen and (max-width: 768px) {
    .scanner-wrapper {
        padding: 0 10px;
    }

    .scanner-container {
        padding: 15px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .scanner-frame {
        width: 100%;
        max-width: 260px;
        height: 260px;
        border-radius: 16px;
    }

    .scanner-frame::before,
    .scanner-frame::after,
    .scanner-frame-corner-bottom::before,
    .scanner-frame-corner-bottom::after {
        width: 30px;
        height: 30px;
        border-width: 0;
    }

    .scanner-frame::before {
        border-top-width: 2px;
        border-left-width: 2px;
        border-top-left-radius: 12px;
    }

    .scanner-frame::after {
        border-top-width: 2px;
        border-right-width: 2px;
        border-top-right-radius: 12px;
    }

    .scanner-frame-corner-bottom::before {
        border-bottom-width: 2px;
        border-left-width: 2px;
        border-bottom-left-radius: 12px;
    }

    .scanner-frame-corner-bottom::after {
        border-bottom-width: 2px;
        border-right-width: 2px;
        border-bottom-right-radius: 12px;
    }

    #reader {
        min-height: 250px;
        border-radius: 16px;
    }

    #message {
        font-size: 0.9rem;
        padding: 0 10px;
        margin-top: 15px;
    }
}


        /* Button Styles */
        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: var(--primary);
            color: var(--secondary);
            border: 1px solid var(--secondary);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            background-color: var(--secondary);
            color: var(--primary);
        }

        .btn:active {
            transform: translateY(0);
        }
        
        .btn i {
            font-size: 1.2rem;
        }
        
        .btn-primary {
            background: var(--secondary);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(255, 154, 86, 0.3);
        }
        
        .btn-primary:hover {
            background: #ff8a3d;
            box-shadow: 0 6px 20px rgba(255, 154, 86, 0.4);
        }

        /* Dialog Styles */
        dialog {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 650px;
            border: none;
            border-radius: 24px;
            padding: 0;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            background: white;
            animation: fadeInUp 0.4s cubic-bezier(0.22, 1, 0.36, 1);
            overflow: hidden;
            margin: 0;
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dialog-header {
            background-color: var(--primary);
            color: var(--secondary);
            padding: 20px;
            text-align: center;
            border-bottom: 2px solid var(--secondary);
        }
        
        .dialog-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
        }

        .dialog-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }
        
        .dialog-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .dialog-close:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }

        .dialog-content {
            padding: 25px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .item-detail {
            margin-bottom: 18px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(42, 45, 87, 0.1);
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 12px;
            align-items: center;
        }
        
        @media (max-width: 480px) {
            .item-detail {
                grid-template-columns: 1fr;
                gap: 6px;
            }
        }

        .item-label {
            font-size: 0.85rem;
            color: var(--medium-text);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            opacity: 0.8;
        }

        .item-value {
            font-size: 1.05rem;
            color: var(--dark-text);
            font-weight: 500;
            word-break: break-word;
        }

        .item-value.empty {
            color: var(--light-text);
            font-style: italic;
            font-weight: 400;
        }

        .dialog-actions {
            padding: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            border-top: 1px solid rgba(42, 45, 87, 0.1);
            background: var(--light-bg);
        }

        .button {
            flex: 1;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            font-size: 1rem;
            min-width: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .button.secondary {
            background: white;
            color: var(--medium-text);
            border: 1px solid rgba(42, 45, 87, 0.1);
        }
        
        .button.secondary:hover {
            background: #f0f1f8;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .button.primary {
            background-color: var(--primary);
            color: var(--secondary);
            border: 1px solid var(--secondary);
        }
        
        .button.primary:hover {
            background-color: var(--secondary);
            color: var(--primary);
        }

        .button:active {
            transform: translateY(0);
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .status-badge i {
            font-size: 0.9rem;
        }

        .status-active {
            background-color: rgba(39, 174, 96, 0.15);
            color: var(--success);
        }
        
        .status-active i {
            color: var(--success);
        }

        .status-inactive {
            background-color: rgba(243, 156, 18, 0.15);
            color: var(--warning);
        }
        
        .status-inactive i {
            color: var(--warning);
        }

        .status-disposed {
            background-color: rgba(231, 76, 60, 0.15);
            color: var(--danger);
        }
        
        .status-disposed i {
            color: var(--danger);
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 22px;
            height: 22px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Utility Classes */
        .text-center {
            text-align: center;
        }
        
        .mt-2 {
            margin-top: 10px;
        }
        
        .hidden {
            display: none !important;
        }
        
        /* Floating Particles Background */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }
        
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            animation: float linear infinite;
        }
        
        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-100vh) rotate(360deg); }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Floating particles background -->
    <div class="particles" id="particles"></div>
    
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-qrcode"></i> Inventory System Scanner</h1>
            <p>Office Of Civil Defense Cordillera Administrative Region</p>
        </div>
        
        <div class="header-actions">
            <button id="home-btn" class="btn"><i class="fas fa-arrow-left"></i> Dashboard</button>
            <button id="switch-cam-btn" class="btn hidden"><i class="fas fa-camera-retro"></i> Switch Camera</button>
        </div>
        
        <div class="scanner-wrapper">
            <div class="scanner-container">
                <div id="reader"></div>
                <div class="scanner-overlay">
                    <div class="scanner-frame">
                        <div class="scanner-frame-corner-bottom"></div>
                    </div>
                </div>
                <p id="message">Initializing scanner...</p>
            </div>
        </div>
    </div>
        
    <dialog id="item-dialog">
        <div class="dialog-header">
            <h2 id="property-number">Property Details</h2>
            <button class="dialog-close" aria-label="Close"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="dialog-content">
            <div class="item-detail">
                <span class="item-label">Description</span>
                <span id="description" class="item-value empty">N/A</span>
            </div>
            
            <div class="item-detail">
                <span class="item-label">Model Number</span>
                <span id="model-number" class="item-value empty">N/A</span>
            </div>
            
            <div class="item-detail">
                <span class="item-label">Serial Number</span>
                <span id="serial-number" class="item-value empty">N/A</span>
            </div>
            
            <div class="item-detail">
                <span class="item-label">Acquisition Date</span>
                <span id="acquisition-date" class="item-value empty">N/A</span>
            </div>
            
            <div class="item-detail">
                <span class="item-label">Accountable Person</span>
                <span id="person-accountable" class="item-value empty">N/A</span>
            </div>
            
            <div class="item-detail">
                <span class="item-label">Status</span>
                <span id="status" class="item-value empty">N/A</span>
            </div>
            
            <div class="item-detail">
                <span class="item-label">Equipment Type</span>
                <span id="equipment-type" class="item-value empty">N/A</span>
            </div>
            
            <div class="item-detail">
                <span class="item-label">Last Updated</span>
                <span id="signature-date" class="item-value empty">N/A</span>
            </div>
        </div>
        
        <div class="dialog-actions">
            <button id="scan-again" class="button secondary"><i class="fas fa-redo"></i> Scan Again</button>
            <button id="edit-item" class="button primary"><i class="fas fa-edit"></i> Edit</button>
            <button id="view-details" class="button primary"><i class="fas fa-info-circle"></i> Full Details</button>
        </div>
    </dialog>

    <script>
        // ===== GLOBAL VARIABLES =====
        const html5QrCode = new Html5Qrcode("reader");
        const dialog = document.querySelector('dialog');
        const scanAgainBtn = document.getElementById('scan-again');
        const editItemBtn = document.getElementById('edit-item');
        const viewDetailsBtn = document.getElementById('view-details');
        const switchCamBtn = document.getElementById('switch-cam-btn');
        const homeBtn = document.getElementById('home-btn');
        const dialogCloseBtn = document.querySelector('.dialog-close');
        const messageEl = document.getElementById('message');
        const particlesEl = document.getElementById('particles');
        
        let currentCameraIndex = 0;
        let cameras = [];
        let isScanning = false;
        let currentPropertyNumber = null;

        // ===== INITIALIZATION =====
        document.addEventListener('DOMContentLoaded', () => {
            // Create floating particles
            createParticles();
            
            // Prevent zooming on mobile
            document.addEventListener('gesturestart', function(e) {
                e.preventDefault();
            });
            
            // Set up event listeners
            homeBtn.addEventListener('click', () => {
                window.location.href = '/inventory-system/pages/landing.php';
            });
            
            dialogCloseBtn.addEventListener('click', () => {
                dialog.close();
                startScanner();
            });
            
            getCameras().then(() => {
                if (cameras.length > 0) {
                    startScanner();
                }
            }).catch(err => {
                showError("Camera access error: " + err.message);
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
                        showError("No cameras found. Please ensure you have a camera connected.");
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
            updateMessage("Point your camera at a QR code", "scanning");
            
            // Adjust QR box size for mobile
            const qrboxSize = Math.min(320, window.innerWidth - 60);
            
            html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: qrboxSize, height: qrboxSize },
                    aspectRatio: 1.0,
                    disableFlip: true // Slightly better performance
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
                    // Don't show verbose errors to user
                    console.log("QR Code scan error:", errorMessage);
                }
            ).then(() => {
                isScanning = true;
                switchCamBtn.classList.remove('hidden');
            }).catch((err) => {
                console.error("Error starting scanner:", err);
                showError("Error accessing camera. Please ensure camera permissions are granted.");
                if (cameras.length > 1) {
                    toggleCamera();
                }
            });
        }

        function getCameras() {
            return Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length > 0) {
                    cameras = devices;
                    selectDefaultCamera();
                    updateSwitchButtonText();
                    return devices;
                } else {
                    throw new Error("No cameras found");
                }
            }).catch(err => {
                console.log("Camera access error:", err);
                showError("Could not access camera. Please ensure camera permissions are granted.");
                switchCamBtn.classList.add('hidden');
                return [];
            });
        }

        function toggleCamera() {
            if (cameras.length < 2) return;
            
            currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
            updateSwitchButtonText();
            startScanner();
        }
        
        function updateSwitchButtonText() {
            const currentCam = cameras[currentCameraIndex];
            const camName = currentCam.label 
                ? currentCam.label.split('(')[0].trim() 
                : `Camera ${currentCameraIndex + 1}`;
            switchCamBtn.innerHTML = `<i class="fas fa-camera-retro"></i> ${camName}`;
        }

        function selectDefaultCamera() {
            // Prefer back camera
            let backCamIndex = cameras.findIndex(cam =>
                /back|rear|environment|1|primary|main|0/i.test(cam.label)
            );
            
            // Fallback to first camera with "2" in label
            if (backCamIndex === -1) {
                backCamIndex = cameras.findIndex(cam => /2|secondary/i.test(cam.label));
            }
            
            // Default to first camera if no preference found
            if (backCamIndex === -1) backCamIndex = 0;
            
            currentCameraIndex = backCamIndex;
        }

        // ===== ITEM PROCESSING FUNCTIONS =====
        function processScannedCode(scannedText) {
            currentPropertyNumber = scannedText.trim();
            updateMessage("Processing scanned item...", "loading");
            fetchItemData(currentPropertyNumber);
        }

        function fetchItemData(propertyNumber) {
            fetch(`/inventory-system/api/getItem.php?property_number=${encodeURIComponent(propertyNumber)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok");
                    }
                    return response.json();
                })
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
                    showError(error.message || "Error fetching item data.");
                    console.error("Error:", error);
                    startScanner();
                });
        }

        function displayItemData(data) {
            // Update dialog display with proper formatting
            document.getElementById('property-number').textContent = data.property_number || "Property Details";
            
            updateField('description', data.description);
            updateField('model-number', data.model_number);
            updateField('serial-number', data.serial_number);
            updateField('acquisition-date', formatDate(data.acquisition_date));
            updateField('person-accountable', data.person_accountable);
            updateStatusField(data.remarks);
            updateField('equipment-type', data.equipment_type);
            updateField('signature-date', formatDate(data.signature_of_inventory_team_date));
            
            updateMessage("Item scanned successfully!", "success");
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
                element.textContent = '';
                
                const badge = document.createElement('span');
                badge.textContent = status;
                
                // Remove all status classes
                badge.className = '';
                
                // Add appropriate status class and icon
                if (status.toLowerCase().includes('service')) {
                    badge.classList.add('status-badge', 'status-active');
                    badge.innerHTML = `<i class="fas fa-check-circle"></i> ${status}`;
                } else if (status.toLowerCase().includes('unservice')) {
                    badge.classList.add('status-badge', 'status-inactive');
                    badge.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${status}`;
                } else if (status.toLowerCase().includes('disposed')) {
                    badge.classList.add('status-badge', 'status-disposed');
                    badge.innerHTML = `<i class="fas fa-times-circle"></i> ${status}`;
                } else {
                    badge.classList.add('item-value');
                    badge.textContent = status;
                }
                
                element.appendChild(badge);
                element.classList.remove('empty');
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
        
        // ===== UI HELPER FUNCTIONS =====
        function updateMessage(text, state = '') {
            messageEl.textContent = text;
            messageEl.className = '';
            
            if (state === 'loading') {
                messageEl.innerHTML = `<span class="loading-spinner"></span> ${text}`;
            } else if (state === 'error') {
                messageEl.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${text}`;
            } else if (state === 'success') {
                messageEl.innerHTML = `<i class="fas fa-check-circle"></i> ${text}`;
            } else if (state === 'scanning') {
                messageEl.innerHTML = `<i class="fas fa-qrcode"></i> ${text}`;
            }
        }
        
        function showError(message) {
            updateMessage(message, 'error');
        }
        
        // Create floating particles
        function createParticles() {
            const particleCount = Math.floor(window.innerWidth / 20);
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                // Random properties
                const size = Math.random() * 5 + 2;
                const posX = Math.random() * 100;
                const posY = Math.random() * 100 + 100; // Start below viewport
                const opacity = Math.random() * 0.3 + 0.1;
                const duration = Math.random() * 30 + 20;
                const delay = Math.random() * 10;
                
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                particle.style.opacity = opacity;
                particle.style.animationDuration = `${duration}s`;
                particle.style.animationDelay = `${delay}s`;
                
                particlesEl.appendChild(particle);
            }
        }
    </script>
</body>
</html>