<?php

include 'config.php';

class GestionnaireProduits {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        session_start();
    }

    public function verifierSessionAdmin() {
        $admin_id = $_SESSION['admin_id'];
        if (!isset($admin_id)) {
            header('location:login.php');
            exit();
        }
    }

    public function ajouterProduit() {
        if (isset($_POST['add_product'])) {
            $nom = mysqli_real_escape_string($this->conn, $_POST['name']);
            $marque = mysqli_real_escape_string($this->conn, $_POST['trademark']);
            $id_fournisseur = $_POST['supplier'];
            $id_categorie = $_POST['category'];
            $prix = $_POST['price'];
            $remise = $_POST['discount'];
            $nouveau_prix = $prix * (100 - $remise) / 100;
            $quantite = $_POST['quantity'];
            $quantite_initiale = $_POST['quantity'];
            $description = $_POST['describe'];
            $image = $_FILES['image']['name'];
            $image_taille = $_FILES['image']['size'];
            $image_nom_tmp = $_FILES['image']['tmp_name'];
            $dossier_image = 'uploaded_img/' . $image;

            $requete_nom_produit = mysqli_query($this->conn, "SELECT name FROM `products` WHERE name = '$nom'") or die('query failed');

            if (mysqli_num_rows($requete_nom_produit) > 0) {
                $message[] = 'Le produit existe déjà.';
            } else {
                $requete_ajout_produit = mysqli_query($this->conn, "INSERT INTO `products`(name, trademark, cate_id, supplier_id, price, discount, newprice,quantity,initial_quantity, describes, image) VALUES('$nom', '$marque', '$id_categorie', '$id_fournisseur', '$prix', '$remise', '$nouveau_prix', '$quantite', '$quantite_initiale', '$description', '$image')") or die('query failed');

               //  if ($requete_ajout_produit) {
               //      if ($image_taille > 2000000) {
               //          $message[] = 'La taille de l\'image est trop grande, veuillez mettre à jour l\'image!';
               //      } else {
               //          move_uploaded_file($image_nom_tmp, $dossier_image);
               //          $message[] = 'Produit ajouté avec succès!';
               //      }
               //  } else {
               //      $message[] = 'L\'ajout du produit a échoué!';
               //  }
            }
        }
    }

    public function supprimerProduit() {
        if (isset($_GET['delete'])) {
            $id_suppression = $_GET['delete'];
            try {
                $requete_supprimer_image = mysqli_query($this->conn, "SELECT image FROM `products` WHERE id = '$id_suppression'") or die('query failed');
               //  $fetch_delete_image = mysqli_fetch_assoc($requete_supprimer_image);
                mysqli_query($this->conn, "DELETE FROM `products` WHERE id = '$id_suppression'") or die('query failed');
               //  unlink('uploaded_img/' . $fetch_delete_image['image']);
               //  $message[] = "Produit supprimé avec succès!";
            } catch (Exception $e) {
                echo "<script>
                        alert('La suppression du produit a échoué car il a été ajouté à des commandes');
                     </script>";
            // return 'La suppression du fournisseur a échoué';

                     
            }
        }
      
    }

    public function modifierProduit() {
        if (isset($_POST['update_product'])) {
            $id_maj_p = $_POST['update_p_id'];
            $nom_maj = $_POST['update_name'];
            $marque_maj = $_POST['update_trademark'];
            $categorie_maj = $_POST['update_category'];
            $prix_maj = $_POST['update_price'];
            $remise_maj = $_POST['update_discount'];
            $nouveau_prix_maj = $prix_maj * (100 - $remise_maj) / 100;
            $quantite_maj = $_POST['update_quantity'];
            $description_maj = $_POST['update_describe'];

            mysqli_query($this->conn, "UPDATE `products` SET name = '$nom_maj', trademark = '$marque_maj', cate_id='$categorie_maj', price = '$prix_maj', newprice='$nouveau_prix_maj', discount='$remise_maj', quantity='$quantite_maj', describes='$description_maj' WHERE id = '$id_maj_p'") or die('query failed');

            $image_maj = $_FILES['update_image']['name'];
            $image_nom_tmp_maj = $_FILES['update_image']['tmp_name'];
            $image_taille_maj = $_FILES['update_image']['size'];
            $dossier_maj = 'uploaded_img/' . $image_maj;
            $ancienne_image_maj = $_POST['update_old_image'];

            if (!empty($image_maj)) {
                if ($image_taille_maj > 2000000) {
                    $message[] = 'La taille du fichier image est trop grande';
                } else {
                    mysqli_query($this->conn, "UPDATE `products` SET image = '$image_maj' WHERE id = '$id_maj_p'") or die('query failed');
                    move_uploaded_file($image_nom_tmp_maj, $dossier_maj);
                    unlink('uploaded_img/' . $ancienne_image_maj);
                }
            }

            header('location:admin_products.php');
        }
    }
}

$gestionnaireProduits = new GestionnaireProduits($conn);
$gestionnaireProduits->verifierSessionAdmin();
$gestionnaireProduits->ajouterProduit();
$gestionnaireProduits->supprimerProduit();
$gestionnaireProduits->modifierProduit();

?>


<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Produits</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="stylesheet" href="css/add.css">
   <link rel="icon" href="uploaded_img/logo2.png">

   <style>
      .search {
         display: flex;
         justify-content: center;
         align-items: center;
         margin-bottom: 12px;
      }
      .search input {
         padding: 10px 25px;
         width: 425px;
         margin-right: 10px;
         font-size: 18px;
         border-radius: 4px;
      }
      .btn {
         margin-top:  0px !important;
      }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">

<span style="color: #005490; font-weight: bold; display: flex; justify-content: center; font-size: 40px;">PRODUITS</span>


   <form class="add_pr" action="" method="post" enctype="multipart/form-data">
      <h3>Ajouter un produit</h3>
      <input type="text" name="name" class="box" placeholder="Nom du produit" required>
      <input type="text" name="trademark" class="box" placeholder="Marque" required>
      <label style="font-size: 16px;" for="">Choisir la catégorie du produit</label>
      <select name="category" class="box">
         <?php
            $selectionner_categories= mysqli_query($conn, "SELECT * FROM `categorys`") or die('Query failed');
            if(mysqli_num_rows($selectionner_categories)>0){
               while($categorie=mysqli_fetch_assoc($selectionner_categories)){
                  echo "<option value='" . $categorie['id'] . "'>".$categorie['name']."</option>";
               }
            }
            else{
               echo "<option>Aucune catégorie disponible.</option>";
            }
         ?>
      </select>
      <label style="font-size: 16px;" for="">Choisir le fournisseur</label>

      <select name="supplier" class="box">
         <?php
            $selectionner_fournisseurs= mysqli_query($conn, "SELECT * FROM `suppliers`") or die('Query failed');
            if(mysqli_num_rows($selectionner_fournisseurs)>0){
               while($fournisseur=mysqli_fetch_assoc($selectionner_fournisseurs)){
                  echo "<option value='" . $fournisseur['id'] . "'>".$fournisseur['name']."</option>";
               }
            }
            else{
               echo "<option>Aucun fournisseur disponible.</option>";
            }
         ?>
      <input type="number" min="0" name="price" class="box" placeholder="Prix du produit" required>
      <input type="number" min="0" name="discount" class="box" placeholder="% de réduction" required>
      <input type="number" min="1" name="quantity" class="box" placeholder="Quantité" required>
      <input type="text" name="describe" class="box" placeholder="Description" required>
      <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
      <input style="background-color: #005490;" onclick="added_pr()" type="submit" value="Ajouter" name="add_product" class="btn added_pr">
      <!-- <input onclick="cancel_added_pr()" type="submit" value="Annuler" name="" class="btn cancel_add"> -->
   </form>

</section>
<form class="search" method="GET">
        <input type="text" name="search" placeholder="Entrez le nom du produit à rechercher..." value="<?php if(isset($_GET['search'])) echo $_GET['search'] ?>">
        <button style="background-color: #005490;" type="submit" class="btn">Rechercher</button>
</form>
<!-- <button onclick="active_sup()" id="btn-sup" style="margin-bottom: 10px; margin-left: 120px; padding: 8px; font-size: 16px;" class="btn btn-info" >Ajouter nouveau</button> -->

<button onclick="addActive()" class="btn btn-info" style="margin-bottom: 10px; margin-left: 56px; padding: 8px; font-size: 16px; background-color: #005490;">Ajouter nouveau</button>
<section class="show-products">

   <div class="box-container" style="display: grid;
    grid-template-columns: repeat(4, 30rem);
    justify-content: center;
    gap: 1.5rem;
    max-width: 1200px;
    margin: 0 auto;
    align-items: flex-start;">
   <?php if(isset($_GET['search'])) {  ?>
      <?php
         $search = isset($_GET['search']) ? $_GET['search'] : '';
         $sql = mysqli_query($conn, "SELECT * FROM products WHERE name LIKE '%$search%'");
            if(mysqli_num_rows($sql) > 0){
               while ($row = mysqli_fetch_array($sql)) {
         ?>
            <div style="height: -webkit-fill-available;" class="box">
                  <img style="border-radius: 4px;" src="uploaded_img/<?php echo $row['image']; ?>" alt="">
                  <div class="name"><?php echo $row['name']; ?></div>
                  <div class="sub-name">Marque: <?php echo $row['trademark']; ?></div>
                  <?php
                  $id_categorie =  $row['cate_id'];
                      $resultat= mysqli_query($conn, "SELECT * FROM `categorys` WHERE id = $id_categorie") or die('Query failed');
                      $nom_categorie = mysqli_fetch_assoc($resultat)
                   ?>
                  <div class="sub-name">Catégorie: <?php echo $nom_categorie['name']; ?></div>
                  <?php
                  $id_fournisseur =  $row['supplier_id'];
                      $resultat= mysqli_query($conn, "SELECT * FROM `suppliers` WHERE id = $id_fournisseur") or die('Query failed');
                      $nom_fournisseur = mysqli_fetch_assoc($resultat)
                   ?>
                  <div class="sub-name">Fournisseur: <?php echo $nom_fournisseur['name']; ?></div>
                  <div class="sub-name">Description: <?php echo $row['describes']; ?></div>
                  <div class="price"><span style="text-decoration-line: line-through"><?php echo number_format($row['price'],0,',','.'  ); ?></span> EUR (Réduction: <?php echo $row['discount']; ?>%)</div>
                  <div class="price"><?php echo number_format($row['newprice'],0,',','.' );; ?> EUR (Quantité: <?php echo $row['quantity']; ?>)</div>
                  <a href="admin_products.php?update=<?php echo $row['id']; ?>" class="option-btn">Mettre à jour</a>
                  <a href="admin_products.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Supprimer ce produit?');">Supprimer</a>
               </div>
         <?php
               }
         } else {
            echo '<p style="font-size: 25px;">Aucun produit ne correspond à votre recherche</p>';
         }
         ?>
   <?php  } else { ?>
      <?php
         $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
               <div style="height: -webkit-fill-available;" class="box">
                  <img style="border-radius: 4px;" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                  <div class="name"><?php echo $fetch_products['name']; ?></div>
                  <div class="sub-name">Marque: <?php echo $fetch_products['trademark']; ?></div>
                  <?php
                  $id_categorie =  $fetch_products['cate_id'];
                      $resultat= mysqli_query($conn, "SELECT * FROM `categorys` WHERE id = $id_categorie") or die('Query failed');
                      $nom_categorie = mysqli_fetch_assoc($resultat)
                   ?>
                  <div class="sub-name">Catégorie: <?php echo $nom_categorie['name']; ?></div>
                  <?php
                  $id_fournisseur =  $fetch_products['supplier_id'];
                      $resultat= mysqli_query($conn, "SELECT * FROM `suppliers` WHERE id = $id_fournisseur") or die('Query failed');
                      $nom_fournisseur = mysqli_fetch_assoc($resultat)
                   ?>
                  <div class="sub-name">Fournisseur: <?php echo $nom_fournisseur['name']; ?></div>
                  <div class="sub-name">Description: <?php echo $fetch_products['describes']; ?></div>
                  <div class="price"><span style="text-decoration-line: line-through"><?php echo number_format($fetch_products['price'],0,',','.'  ); ?></span> EUR (Réduction: <?php echo $fetch_products['discount']; ?>%)</div>
                  <div class="price"><?php echo number_format($fetch_products['newprice'],0,',','.' );; ?> EUR (Quantité: <?php echo $fetch_products['quantity']; ?>)</div>
                  <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Mettre à jour</a>
                  <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Supprimer ce produit?');">Supprimer</a>
               </div>
            <?php
            }
            }else{
               echo '<p class="empty">Aucun produit n\'a été ajouté!</p>';
            }
            ?>
   <?php } ?>
   </div>

</section>

<section class="edit-product-form" style="height:650px">

   <?php
      if(isset($_GET['update'])){//show update form from onclick <a></a> href='update'
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input style="margin: 4px 0;" style="margin: 4px 0;" type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                  <input style="margin: 4px 0;" type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                  <input style="margin: 4px 0;" type="hidden" name="update_trademark" value="<?php echo $fetch_update['trademark']; ?>">
                  <input style="margin: 4px 0;" type="hidden" name="update_supplier" value="<?php echo $fetch_update['supplier_id']; ?>">
                  <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                  <input style="margin: 4px 0;" type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Nom du produit">
                  <select style="margin: 4px 0;" name="update_category" class="box">
                  <?php
                  $id_categorie =  $fetch_update['cate_id'];
                      $resultat= mysqli_query($conn, "SELECT * FROM `categorys` WHERE id = $id_categorie") or die('Query failed');
                      $nom_categorie = mysqli_fetch_assoc($resultat)
                   ?>
                     <option value="<?php echo $nom_categorie['id']?>"><?=$nom_categorie['name']?></option>
                     <?php
                        $select_category= mysqli_query($conn, "SELECT * FROM `categorys`") or die('Query failed');
                        while($fetch_category=mysqli_fetch_assoc($select_category)){
                           if($fetch_category['name']!=$fetch_update['category']){
                              echo"<option  value='" . $fetch_category['id'] . "'>".$fetch_category['name']."</option>";
                           }
                        }
                     ?>
                  </select>
                  <input style="margin: 4px 0;" type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Prix du produit">
                  <input style="margin: 4px 0;" type="number" name="update_quantity" value="<?php echo $fetch_update['quantity']; ?>" min="0" class="box" required placeholder="Quantité du produit">
                  <input style="margin: 4px 0;" type="text" name="update_describe" value="<?php echo $fetch_update['describes']; ?>" class="box" required placeholder="Description">
                  <input style="margin: 4px 0; background-color: #005490;" type="submit" value="Mettre à jour" name="update_product" class="btn">
                  <input style="margin: 4px 0; background-color: #005490" type="reset" value="Annuler" id="close-update" class="option-btn">
               </form>
   <?php
            }
         }
      }else{
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>
<?php include 'footer.php'; ?>

<script src="js/admin_script.js"></script>
<script src="js/add.js" ></script>

</body>
</html>
