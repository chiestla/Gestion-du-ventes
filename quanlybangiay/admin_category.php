<?php
include 'config.php';

class GestionnaireCatégorie
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function vérifierSessionAdmin()
    {
        session_start();
        $admin_id = $_SESSION['admin_id'];

        if (!isset($admin_id)) {
            header('location:login.php');
        }
    }

    public function ajouterCatégorie($nom, $description)
    {
        $nom = mysqli_real_escape_string($this->conn, $nom);
        $description = mysqli_real_escape_string($this->conn, $description);

        $sélectionner_nom_catégorie = mysqli_query($this->conn, "SELECT name FROM `categorys` WHERE name = '$nom'") or die('query failed');

        if (mysqli_num_rows($sélectionner_nom_catégorie) > 0) {
            // $message[] = 'La catégorie existe déjà.';
        } else {
            $requete_ajouter_catégorie = mysqli_query($this->conn, "INSERT INTO `categorys`(name, describes) VALUES('$nom', '$description')") or die('query failed');

            // if ($requete_ajouter_catégorie) {
            //     $message[] = 'Catégorie ajoutée avec succès !';
            // } else {
            //     $message[] = 'Impossible d\'ajouter cette catégorie !';
            // }
        }
    }

    public function supprimerCatégorie($delete_id)
    {
        try {
            mysqli_query($this->conn, "DELETE FROM `categorys` WHERE id = '$delete_id'") or die('query failed');
            // $message[] = "Suppression de la catégorie réussie";
        } catch (Exception $e) {
         echo "<script>
         alert('La suppression de la catégorie a échoué');
      </script>";
        }
    }

    public function mettreÀJourCatégorie($update_p_id, $update_nom, $update_description)
    {
        $update_nom = mysqli_real_escape_string($this->conn, $update_nom);
        $update_description = mysqli_real_escape_string($this->conn, $update_description);

        mysqli_query($this->conn, "UPDATE `categorys` SET name = '$update_nom', describes = '$update_description' WHERE id = '$update_p_id'") or die('query failed');

        header('location:admin_category.php');
    }

    public function obtenirListeCatégorie($search = '')
    {
        $search = mysqli_real_escape_string($this->conn, $search);
        $sql = mysqli_query($this->conn, "SELECT * FROM categorys WHERE name LIKE '%$search%'");

        $catégories = [];

        if (mysqli_num_rows($sql) > 0) {
            while ($row = mysqli_fetch_assoc($sql)) {
                $catégories[] = $row;
            }
        }

        return $catégories;
    }
}

$gestionnaireCatégorie = new GestionnaireCatégorie($conn);

if (isset($_POST['add_category'])) {
    $gestionnaireCatégorie->ajouterCatégorie($_POST['name'], $_POST['describes']);
}

if (isset($_GET['delete'])) {
    $gestionnaireCatégorie->supprimerCatégorie($_GET['delete']);
}

if (isset($_POST['update_category'])) {
    $gestionnaireCatégorie->mettreÀJourCatégorie($_POST['update_p_id'], $_POST['update_name'], $_POST['update_description']);
}

$listeCatégorie = $gestionnaireCatégorie->obtenirListeCatégorie(isset($_GET['search']) ? $_GET['search'] : '');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Catégories</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
   <link rel="stylesheet" href="css/admin_style.css">
   <link rel="stylesheet" href="css/add.css">
   <link rel="icon" href="uploaded_img/logo2.png">
   <style>
      table {
         font-size: 15px;
      }
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
    .fixx {
      background-color: #f39c12;
      padding: 5px;
      border-radius: 6px;
      color: white;
      text-decoration: none;
    }
    .fixxx {
      background-color: #c0392b;
      padding: 5px;
      border-radius: 6px;
      color: white;
      text-decoration: none;
    }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">
    <span style="color: #005490; font-weight: bold; display: flex; justify-content: center; font-size: 40px;">CATÉGORIES DE PRODUITS</span>
   <!-- <h1 class="title">Danh mục sản phẩm</h1> -->

   <form class="add_sup" method="post" enctype="multipart/form-data">
      <h3 style="font-weight: bolder;" >Ajouter une catégorie</h3>
      <input type="text" name="name" class="box" placeholder="Nom de la catégorie" required>
      <input type="text" name="describes" class="box" placeholder="Description" required>
      <input style="background-color: #005490;" type="submit" value="Ajouter la catégorie" name="add_category" class="btn">
   </form>

</section>

<form class="search" method="GET">
        <input type="text" name="search" placeholder="Entrez le nom de la catégorie à rechercher..." value="<?php if(isset($_GET['search'])) echo $_GET['search'] ?>">
        <button style="background-color: #005490;" type="submit" class="btn">Rechercher</button>
</form>
<button onclick="active_sup()" id="btn-sup" style="margin-bottom: 10px; margin-left: 110px; padding: 5px; font-size: 16px; background-color: #005490;" class="btn btn-info" >Ajouter nouveau</button>
<section class="show-products">

   <div class="container" style="padding: 1rem 0rem 3rem">
   <?php if(isset($_GET['search'])) {  ?>
      <table class="table table-striped">
         <thead>
            <tr>
               <th scope="col">ID</th>
               <th scope="col">Nom de la catégorie</th>
               <th scope="col">Description</th>
               <th scope="col">Actions</th>
            </tr>
         </thead>
         <tbody>
         <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $sql = mysqli_query($conn, "SELECT * FROM categorys WHERE name LIKE '%$search%'");
               if(mysqli_num_rows($sql) > 0){
                  while ($row = mysqli_fetch_array($sql)) {
             ?>
            <tr>
               <th scope="row"><?php echo $row['id']; ?></th>
               <td><?php echo $row['name']; ?></td>
               <td><?php echo $row['describes']; ?></td>
               <td>
                  <a style="text-decoration: none;" href="admin_category.php?update=<?php echo $row['id']; ?>" class="fixx">Modifier</a> | 
                  <a style="text-decoration: none;" class="fixxx" style="text-decoration: none;" href="admin_category.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Supprimer cette catégorie ?');" >Supprimer</a>
               </td>
            </tr>
         <?php
                  }
            } else {
               echo "<tr>"; echo "<td colspan=6 align=center>"; echo '<p style="font-size: 25px;">Aucune catégorie correspondant à votre recherche</p>'; echo "</td>"; echo "</tr>";
            }
         ?>
         </tbody>
      </table>
   <?php  } else { ?>
      <table class="table table-striped">
         <thead>
            <tr>
               <th scope="col">ID</th>
               <th scope="col">Nom de la catégorie</th>
               <th scope="col">Description</th>
               <th scope="col">Actions</th>
            </tr>
         </thead>
         <tbody>
         <?php
            $select_categories = mysqli_query($conn, "SELECT * FROM `categorys`") or die('query failed');
            while($fetch_cate = mysqli_fetch_assoc($select_categories)){
         ?>
            <tr>
               <th scope="row"><?php echo $fetch_cate['id']; ?></th>
               <td><?php echo $fetch_cate['name']; ?></td>
               <td><?php echo $fetch_cate['describes']; ?></td>
               <td>
                  <a href="admin_category.php?update=<?php echo $fetch_cate['id']; ?>" style="text-decoration: none;" class="fixx">Modifier</a> | 
                  <a style="text-decoration: none;" href="admin_category.php?delete=<?php echo $fetch_cate['id']; ?>" class="fixxx" onclick="return confirm('Supprimer cette catégorie ?');">Supprimer</a>
               </td>
            </tr>
         <?php
            }
         ?>
         </tbody>
      </table>
   <?php } ?>
   </div>

</section>

<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){//Afficher le formulaire de mise à jour des informations de la catégorie depuis <a></a> avec href='update'
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `categorys` WHERE id = '$update_id'") or die('query failed');//Obtenir les informations de la catégorie à mettre à jour
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                  <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Nom">
                  <input type="text" name="update_description" value="<?php echo $fetch_update['describes']; ?>" class="box" required placeholder="Description">
                  <input type="submit" style="background-color: #005490;" value="Mettre à jour" name="update_category" class="btn"> <!-- soumettre le formulaire de mise à jour -->
                  <input type="reset" style="background-color: #005490;" value="Annuler"  onclick="window.location.href = 'admin_category.php'" class="btn">
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
