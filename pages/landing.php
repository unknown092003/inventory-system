<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/landing.css">
    <title>Inventory</title>
</head>
<body>
<div class="header">
    <h1>OFFICE OF CIVIL DEFENSE</h1>
    <img src="/inventory-system/public/img/ocd.png" alt="">
</div>

<div class="main-container">
    <div class="left-nav">
        <h1>INVENTORY SYSTEM<hr></h1>
        <ul>
            <li><a href="?page=home">Home</a></li>
            <li><a href="?page=list">List</a></li>
            <li><a href="?page=create">Create</a></li>
            <li><a href="?page=data">Data</a></li>
            <li><a href="?page=edit">Edit</a></li>
            <li><a href="landing/scan.php">Scan</a></li>
        </ul>
    </div>

    <div class="content-area">
        <?php
        $page = $_GET['page'] ?? 'home';
        
        switch($page) {
            case 'home':
                include 'landing/home.php';
                break;
            case 'list' :
                include 'list.php';
                break;
            case 'create':
                include 'landing/create.php';
                break;
            case 'data':
                include 'landing/data.php';
                break;
            case 'edit':
                include 'landing/edit.php';
                break;
            default:
                include 'landing/home.php';
        }
        ?>
    </div>
</div>
</body>
</html>
