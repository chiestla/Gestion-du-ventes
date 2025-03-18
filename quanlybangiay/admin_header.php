<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="uploaded_img/logo2.png">
   
   <title>Document</title>
   <style>
      .header .navbar .header_btn:hover{
         background-color: #005490;
         color: white;
         border-radius: 7px;
      }
   </style>
</head>
<body>
   <header class="header">

   <div style="max-width: 1400px; height: 70px; padding: 1rem 1rem;" class="flex">
      <a href=""><img  style="width: 38px; height:38px; position: relative; top: -2px; left: 32px;" src="uploaded_img/logo..jpg" alt=""></a>
      <a style="text-decoration: none; position: relative; left: -20px;" href="home.php" class="logo">GDC</a>

      <nav style="min-height: unset;margin-bottom: 0px;width: 1050;position: relative;left: -70px;" class="navbar">
         <a class="header_btn" style="margin: 0; padding: 6px; text-decoration: none;" href="home.php">Accueil</a>
         <a class="header_btn" style="margin: 0;padding: 6px;text-decoration: none;" href="admin_products.php">Produits</a>
         <a class="header_btn" style="margin: 0;padding: 6px;text-decoration: none;" href="admin_category.php">Catégories</a>
         <a class="header_btn" style="margin: 0;padding: 6px;text-decoration: none;" href="admin_supplier.php">Fournisseurs</a>
         <a class="header_btn" style="margin: 0;padding: 6px;text-decoration: none;" href="admin_customers.php">Clients</a>
         <a class="header_btn" style="margin: 0;padding: 6px;text-decoration: none;" href="admin_orders.php">Commandes</a>
         <!-- <a class="header_btn" style="margin: 0;padding: 6px;text-decoration: none;" href="admin_shippingporders.php">Commandes de livraison</a> -->
         <a class="header_btn" style="margin: 0;padding: 6px;text-decoration: none;" href="admin_statistical.php">Statistiques</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="account-box">
         <p style="color: #005490;">Nom d'utilisateur : <span style="color: #005490;"><?php echo $_SESSION['admin_name']; ?></span></p>
         <p style="color: #005490;">Email : <span style="color: #005490;"><?php echo $_SESSION['admin_email']; ?></span></p>
         <a style="background-color: #005490;"href="logout.php" class="delete-btn">Se déconnecter</a>
         <div><a style="color: #005490;" href="login.php">Connexion</a> | <a style="color: #005490;" href="register.php">Inscription</a></div>
      </div>

   </div>

</header>
</body>
</html>
