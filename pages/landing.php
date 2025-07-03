<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/inventory-system/public/styles/landing.css" />
  <title>Inventory System</title>
  <style>
   /* Modal Overlay */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  inset: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex; /* for centering */
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.3s ease-in-out;
}

/* Modal Container */
.modal-content {
  background-color: #ffffff;
  padding: 30px 20px;
  border-radius: 16px;
  width: 90%;
  max-width: 360px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
  text-align: center;
  position: relative;
  animation: slideUp 0.4s ease;
}

/* Close Button */
.close-btn {
  color: #999;
  position: absolute;
  top: 16px;
  right: 20px;
  font-size: 24px;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s, color 0.2s;
}

.close-btn:hover {
  color: #ff5e5e;
  transform: rotate(90deg);
}

/* Modal Links (Buttons) */
.modal-content a {
  display: block;
  padding: 12px 18px;
  margin: 12px 0;
  background-color: #4a90e2;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 600;
  transition: background-color 0.3s ease, transform 0.2s;
}

.modal-content a:hover {
  background-color: #357abd;
  transform: translateY(-2px);
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0 }
  to { opacity: 1 }
}

@keyframes slideUp {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

/* Responsive Tweaks */
@media (max-width: 420px) {
  .modal-content {
    padding: 24px 15px;
  }

  .modal-content a {
    font-size: 0.95rem;
  }

  .close-btn {
    top: 12px;
    right: 15px;
    font-size: 22px;
  }
}

  </style>
</head>
<body>

  <!-- HEADER -->
  <header class="header">
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <div>
      <h1>OFFICE OF CIVIL DEFENSE </h1>
      <p>Cordillera Administrative Region</p>
    </div>
    <img src="/inventory-system/public/img/ocd.png" alt="OCD Logo" id="user-menu-trigger" />
  </header>

  <!-- MAIN CONTAINER -->
  <div class="main-container">
    
    <!-- SIDEBAR -->
<nav class="left-nav" id="mobileMenu">
  <div class="nav-div">
    <h2>INVENTORY SYSTEM</h2>
    <hr />
    <ul>
      <li><a class="a" href="?page=home">Home</a></li>
      <li><a class="a" href="?page=list">List</a></li>
      <li><a class="a" href="?page=create">Create</a></li>
      <li><a class="a" href="?page=data">Data</a></li>
      <li><a class="a" href="viewdata/viewdata2.php">VIEW Data</a></li>
      <li><a class="a" href="landing/scan.php">Scan</a></li>
    </ul>
  </div>

  <!-- Move logout outside ul -->
  <a class="logout" href="logout.php">Logout</a>
</nav>



    <!-- MAIN CONTENT -->
    <main class="content-area">
      <?php
        $page = $_GET['page'] ?? 'home';
        switch ($page) {
          case 'home': include 'landing/home.php'; break;
          case 'list': include 'list.php'; break;
          case 'create': include 'landing/equipment-type.php'; break;
          case 'data': include 'landing/data.php'; break;
          // case 'edit': include 'landing/edit.php'; break;
          default: include 'landing/home.php';
        }
      ?>
    </main>
  </div>

  <!-- USER MODAL -->
  <div id="user-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn">&times;</span>
      <h2>User Options</h2>
      <a href="register.php">Create User</a>
      <a href="change_password.php">Change Password</a>
      <a href="view_accounts.php">View All Accounts</a>
    </div>
  </div>

  <!-- MOBILE MENU SCRIPT -->
  <script>
    function toggleMenu() {
      const nav = document.getElementById("mobileMenu");
      nav.classList.toggle("open");
    }

    // Modal script
    const modal = document.getElementById('user-modal');
    const trigger = document.getElementById('user-menu-trigger');
    const closeBtn = document.querySelector('.close-btn');

    trigger.onclick = function() {
      modal.style.display = 'flex';
    }
    closeBtn.onclick = function() {
      modal.style.display = 'none';
    }
    window.onclick = function(event) {
      if (event.target == modal) {
        modal.style.display = 'none';
      }
    }
  </script>
</body>
</html>
