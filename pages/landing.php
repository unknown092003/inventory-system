<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/inventory-system/public/styles/landing.css" />
  <title>Inventory System</title>
</head>
<body>

  <!-- HEADER -->
  <header class="header">
    <button class="menu-toggle" onclick="toggleMenu()">☰</button>
    <h1>OFFICE OF CIVIL DEFENSE</h1>
    <img src="/inventory-system/public/img/ocd.png" alt="OCD Logo" />
  </header>

  <!-- MAIN CONTAINER -->
  <div class="main-container">
    
    <!-- SIDEBAR -->
    <nav class="left-nav" id="mobileMenu">
      <h2>INVENTORY SYSTEM</h2>
      <hr />
      <ul>
        <li><a href="?page=home">Home</a></li>
        <li><a href="?page=list">List</a></li>
        <li><a href="?page=create">Create</a></li>
        <li><a href="?page=data">Data</a></li>
        <li><a href="?page=edit">Edit</a></li>
        <li><a href="landing/scan.php">Scan</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
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
          case 'edit': include 'landing/edit.php'; break;
          default: include 'landing/home.php';
        }
      ?>
    </main>
  </div>

  <!-- MOBILE MENU SCRIPT -->
  <script>
    function toggleMenu() {
      const nav = document.getElementById("mobileMenu");
      nav.classList.toggle("open");
    }
  </script>
</body>
</html>
