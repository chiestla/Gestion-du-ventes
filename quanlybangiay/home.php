<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //Créer une session admin

   if(!isset($admin_id)){// si la session n'existe pas => redirection vers la page de connexion
      header('location:login.php');
   }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Tableau de bord</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="icon" href="uploaded_img/logo2.png">


</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="dashboard">

   <!-- <h1 class="title">Tableau de bord</h1> -->
   <span style="color: #005490;padding: 6px 0px 24px 0; font-weight: bold; display: flex; justify-content: center; font-size: 40px;">Tableau de bord</span>
   <div class="box-container">

      <div class="box">
         <?php 
            $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('requête échouée');
            $number_of_orders = mysqli_num_rows($select_orders);
         ?>
         <h3><?php echo $number_of_orders; ?></h3>
         <p style="color: #005490;">Commandes</p>
      </div>

      <div class="box">
         <?php 
            $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('requête échouée');
            $number_of_products = mysqli_num_rows($select_products);
         ?>
         <h3><?php echo $number_of_products; ?></h3>
         <p style="color: #005490;">Produits</p>
      </div>
      
      <div class="box">
         <?php 
            $select_cate = mysqli_query($conn, "SELECT * FROM `categorys`") or die('requête échouée');
            $number_of_cate = mysqli_num_rows($select_cate);
         ?>
         <h3><?php echo $number_of_cate; ?></h3>
         <p style="color: #005490;">Catégories</p>
      </div>
      
      <div class="box">
         <?php 
            $select_suppliers = mysqli_query($conn, "SELECT * FROM `suppliers`") or die('requête échouée');
            $number_of_sup = mysqli_num_rows($select_suppliers);
         ?>
         <h3><?php echo $number_of_sup; ?></h3>
         <p style="color: #005490;">Fournisseurs</p>
      </div>

      <div class="box">
         <?php 
            $select_users = mysqli_query($conn, "SELECT * FROM `customers`") or die('requête échouée');
            $number_of_users = mysqli_num_rows($select_users);
         ?>
         <h3><?php echo $number_of_users; ?></h3>
         <p style="color: #005490;">Clients</p>
      </div>
      <div class="box">
         <?php 
            $select_users = mysqli_query($conn, "SELECT * FROM `orders`") or die('requête échouée');
            $number_of_users = mysqli_num_rows($select_users);
         ?>
         <h3><?php echo $number_of_users; ?></h3>
         <p style="color: #005490;">Commandes</p>
      </div>

   </div>

</section>
<?php include 'footer.php'; ?>
<script src="js/admin_script.js"></script>

</body>
</html>
