<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/inventory-system/public/styles/landing.css" />
  <title>Inventory System</title>
  <style>
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
    }
    .modal-content {
      background-color: #fefefe;
      margin: auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 300px;
      border-radius: 10px;
      text-align: center;
      position: relative;
    }
    .close-btn {
      color: #aaa;
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .modal-content a {
      display: block;
      padding: 12px;
      margin: 10px 0;
      background-color: #4a90e2;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s;
    }
    .modal-content a:hover {
      background-color: #357abd;
    }
    .header img {
        cursor: pointer;
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
