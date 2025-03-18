<?php

include 'config.php';

class Fournisseur
{
    private $id;
    private $nom;
    private $email;
    private $adresse;
    private $telephone;

    public function __construct($id, $nom, $email, $adresse, $telephone)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->adresse = $adresse;
        $this->telephone = $telephone;
    }
}

class ControleurFournisseur
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        session_start();
        $this->verifierSessionAdmin();
    }

    private function verifierSessionAdmin()
    {
        $admin_id = $_SESSION['admin_id'];
        if (!isset($admin_id)) {
            header('location:login.php');
            exit;
        }
    }

    public function ajouterFournisseur($nom, $email, $adresse, $telephone)
    {
        $nom = mysqli_real_escape_string($this->conn, $nom);
        $email = mysqli_real_escape_string($this->conn, $email);
        $adresse = mysqli_real_escape_string($this->conn, $adresse);

        $query = mysqli_prepare($this->conn, "INSERT INTO `fournisseurs` (name, address, email, phone) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($query, "ssss", $nom, $email, $adresse, $telephone);
        $ajout_fournisseur_query = mysqli_stmt_execute($query);

        return $ajout_fournisseur_query ? 'Fournisseur ajouté avec succès !' : 'Échec de l\'ajout du fournisseur !';
    }

    public function supprimerFournisseur($delete_id)
    {
        $delete_id = mysqli_real_escape_string($this->conn, $delete_id);

        try {
            $query = mysqli_prepare($this->conn, "DELETE FROM `fournisseurs` WHERE id = ?");
            mysqli_stmt_bind_param($query, "i", $delete_id);
            mysqli_stmt_execute($query);
        } catch (Exception $e) {
            // return 'Échec de la suppression du fournisseur';
            echo 
            "<script>
            alert('Échec de la suppression du fournisseur');
            </script>";
        }
    }

    public function mettreAJourFournisseur($update_s_id, $update_nom, $update_email, $update_telephone, $update_adresse)
    {
        $update_nom = mysqli_real_escape_string($this->conn, $update_nom);
        $update_email = mysqli_real_escape_string($this->conn, $update_email);
        $update_adresse = mysqli_real_escape_string($this->conn, $update_adresse);

        $query = mysqli_prepare($this->conn, "UPDATE `fournisseurs` SET nom = ?, email = ?, telephone = ?, adresse = ? WHERE id = ?");
        mysqli_stmt_bind_param($query, "ssssi", $update_nom, $update_email, $update_telephone, $update_adresse, $update_s_id);
        mysqli_stmt_execute($query);

        header('location:admin_fournisseur.php');
    }

}

$controleurFournisseur = new ControleurFournisseur($conn);

if (isset($_POST['ajouter_fournisseur'])) {
    $message[] = $controleurFournisseur->ajouterFournisseur($_POST['nom'], $_POST['email'], $_POST['adresse'], $_POST['telephone']);
}

if (isset($_GET['supprimer'])) {
    $message[] = $controleurFournisseur->supprimerFournisseur($_GET['supprimer']);
}

if (isset($_POST['mettre_a_jour_fournisseur'])) {
    $controleurFournisseur->mettreAJourFournisseur($_POST['update_s_id'], $_POST['update_nom'], $_POST['update_email'], $_POST['update_telephone'], $_POST['update_adresse']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Fournisseurs</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
   <link rel="stylesheet" href="./css/style_admin.css">
   <link rel="stylesheet" href="css/ajouter.css">
   <link rel="icon" href="uploaded_img/logo2.png">

   <style>
      table {
         font-size: 15px;
      }
      .title {
         margin-top: 5px;
      }
      .box-item {
         margin:1rem 0;
         padding:1.2rem 1.4rem;
         border:var(--border);
         border-radius: .5rem;
         background-color: var(--light-bg);
         font-size: 1.8rem;
         color:var(--black);
         width: 100%;
      }
    .edit-supplier-form{
        min-height: 100vh;
        background-color: rgba(0,0,0,.7);
        display: flex;
        align-items: center;
        justify-content: center;
        padding:2rem;
        overflow-y: scroll;
        position: fixed;
        top:0; left:0;
        z-index: 1200;
        width: 100%;
    }

    .edit-supplier-form form{
        width: 50rem;
        padding:2rem;
        text-align: center;
        border-radius: .5rem;
        background-color: var(--white);
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
<span style="color: #005490;padding-top: 24px; font-weight: bold; display: flex; justify-content: center; font-size: 40px;">FOURNISSEURS</span>


<section class="ajouter-produits" style="padding: 1rem 2rem;">
   <form class="add_sup" action="" method="post" enctype="multipart/form-data">
        <h3 style="font-weight: bolder;" >Ajouter un fournisseur</h3>
        <input type="text" name="nom" class="box-item" placeholder="Nom du fournisseur" required>
        <input type="text" name="email" class="box-item" placeholder="Email" required>
        <input type="number" name="telephone" class="box-item" placeholder="Téléphone" required>
        <input type="text" name="adresse" class="box-item" placeholder="Adresse" required>
        <input style="background-color: #005490;" onclick="added_pr()" type="submit" value="Ajouter" name="ajouter_fournisseur" class="btn added_pr">
   </form>
</section>
<form class="search" method="GET">
        <input type="text" name="search" placeholder="Entrez le nom du fournisseur à rechercher..." value="<?php if(isset($_GET['search'])) echo $_GET['search'] ?>">
        <button style="background-color: #005490;" type="submit" class="btn">Rechercher</button>
</form>
<button onclick="active_sup()" id="btn-sup" style="margin-bottom: 10px;
    margin-left: 120px;
    padding: 5px;
    font-size: 16px;
    background-color: #005490;" class="btn btn-info" >Ajouter nouveau</button>
<section class="utilisateurs" style="padding: 1rem 0rem 3rem">

   <div class="container" >
   <?php if(isset($_GET['search'])) {  ?>
      <table class="table table-striped">
         <thead>
            <tr>
               <th scope="col">ID</th>
               <th scope="col">Nom</th>
               <th scope="col">Email</th>
               <th scope="col">Adresse</th>
               <th scope="col">Téléphone</th>
               <th scope="col">Action</th>
            </tr>
         </thead>
         <tbody>
         <?php
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $sql = mysqli_query($conn, "SELECT * FROM fournisseurs WHERE nom LIKE '%$search%'");
               if(mysqli_num_rows($sql) > 0){
                  while ($row = mysqli_fetch_array($sql)) {
             ?>
            <tr>
               <th scope="row"><?php echo $row['id']; ?></th>
               <td><?php echo $row['nom']; ?></td>
               <td><?php echo $row['email']; ?></td>
               <td><?php echo $row['adresse']; ?></td>
               <td><?php echo $row['telephone']; ?></td>
               <td>
                  <a style="text-decoration: none;" href="admin_fournisseur.php?update=<?php echo $row['id']; ?>" class="fixx">Modifier</a> |
                  <a style="text-decoration: none;" href="admin_fournisseur.php?supprimer=<?php echo $row['id']; ?>" class="fixxx" onclick="return confirm('Supprimer ce fournisseur ?');">Supprimer</a>
               </td>
            </tr>
         <?php
                  }
            } else {
               echo "<tr>"; echo "<td colspan=6 align=center>"; echo '<p style="font-size: 25px;">Aucun fournisseur ne correspond à votre recherche</p>'; echo "</td>"; echo "</tr>";
            }
         ?>
         </tbody>
      </table>
    <?php  } else { ?>
      <table class="table table-striped">
         <thead>
            <tr>
               <th scope="col">ID</th>
               <th scope="col">Nom</th>
               <th scope="col">Email</th>
               <th scope="col">Adresse</th>
               <th scope="col">Téléphone</th>
               <th scope="col">Action</th>
            </tr>
         </thead>
         <tbody>
         <?php
            $select_fournisseurs = mysqli_query($conn, "SELECT * FROM `fournisseurs`") or die('query failed');
            while($fetch_fournisseurs = mysqli_fetch_assoc($select_fournisseurs)){
         ?>
            <tr>
               <th scope="row"><?php echo $fetch_fournisseurs['id']; ?></th>
               <td><?php echo $fetch_fournisseurs['nom']; ?></td>
               <td><?php echo $fetch_fournisseurs['email']; ?></td>
               <td><?php echo $fetch_fournisseurs['adresse']; ?></td>
               <td><?php echo $fetch_fournisseurs['telephone']; ?></td>
               <td>
                  <a href="admin_fournisseur.php?update=<?php echo $fetch_fournisseurs['id']; ?>" class="fixx">Modifier</a> |
                  <a href="admin_fournisseur.php?supprimer=<?php echo $fetch_fournisseurs['id']; ?>" class="fixxx" onclick="return confirm('Supprimer ce fournisseur ?');">Supprimer</a>
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
<section class="edit-supplier-form">

   <?php
      if(isset($_GET['update'])){//afficher le formulaire de mise à jour à partir de onclick <a></a> href='update'
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `fournisseurs` WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_s_id" value="<?php echo $fetch_update['id']; ?>">
                  <input type="text" name="update_nom" class="box-item" value="<?php echo $fetch_update['nom'] ?>" placeholder="Nom du fournisseur" required>
                  <input type="text" name="update_email" class="box-item" value="<?php echo $fetch_update['email']?>" placeholder="Email" required>
                  <input type="number" name="update_telephone" class="box-item" value="<?php echo $fetch_update['telephone']?>" placeholder="Téléphone" required>
                  <input type="text" name="update_adresse" class="box-item" value="<?php echo $fetch_update['adresse']?>" placeholder="Adresse" required>
                  <input style="background-color: #005490;" type="submit" value="Mettre à jour" name="mettre_a_jour_fournisseur" class="btn btn-primary">
                  <input style="background-color: #005490;" type="reset" value="Annuler" id="close-update-supplier" class="btn btn-warning">
               </form>
   <?php
            }
         }
      }else{
         echo '<script>document.querySelector(".edit-supplier-form").style.display = "none";</script>';
      }
   ?>

</section>

<?php include 'footer.php'; ?>

<script>
   document.querySelector('#close-update-supplier').onclick = () =>{
      document.querySelector('.edit-supplier-form').style.display = 'none';
      window.location.href = 'admin_fournisseur.php';
}
</script>


<script src="js/admin_script.js"></script>
<script src="js/ajouter.js" ></script>
</body>
</html>
