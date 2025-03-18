<?php

include 'config.php';

class Client
{
    private $id;
    private $nom;
    private $email;
    private $adresse;
    private $téléphone;

    public function __construct($id, $nom, $email, $adresse, $téléphone)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->email = $email;
        $this->adresse = $adresse;
        $this->téléphone = $téléphone;
    }
}

class ControleurClient
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        session_start();
        $this->vérifierSessionAdmin();
    }

    private function vérifierSessionAdmin()
    {
        $admin_id = $_SESSION['admin_id'];
        if (!isset($admin_id)) {
            header('location:login.php');
            exit;
        }
    }

    public function ajouterClient($nom, $email, $adresse, $téléphone)
    {
        $nom = mysqli_real_escape_string($this->conn, $nom);
        $email = mysqli_real_escape_string($this->conn, $email);
        $adresse = mysqli_real_escape_string($this->conn, $adresse);

        $query = mysqli_prepare($this->conn, "INSERT INTO `customers` (name, email, address, phone) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($query, "ssss", $nom, $email, $adresse, $téléphone);
        $ajouter_client_query = mysqli_stmt_execute($query);
    }

    public function supprimerClient($delete_id)
    {
        $delete_id = mysqli_real_escape_string($this->conn, $delete_id);

        try {
            $query = mysqli_prepare($this->conn, "DELETE FROM `customers` WHERE id = ?");
            mysqli_stmt_bind_param($query, "i", $delete_id);
            mysqli_stmt_execute($query);
        } catch (Exception $e) {
            echo "<script>
                        alert('Échec de la suppression du client');
                     </script>";
        }
    }

    public function mettreÀJourClient($update_c_id, $update_nom, $update_email, $update_téléphone, $update_adresse)
    {
        $update_nom = mysqli_real_escape_string($this->conn, $update_nom);
        $update_email = mysqli_real_escape_string($this->conn, $update_email);
        $update_adresse = mysqli_real_escape_string($this->conn, $update_adresse);

        $query = mysqli_prepare($this->conn, "UPDATE `customers` SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        mysqli_stmt_bind_param($query, "ssssi", $update_nom, $update_email, $update_téléphone, $update_adresse, $update_c_id);
        mysqli_stmt_execute($query);

        header('location:admin_customers.php');
    }
}

$controleurClient = new ControleurClient($conn);

if (isset($_POST['add_customer'])) {
    $message[] = $controleurClient->ajouterClient($_POST['nom'], $_POST['email'], $_POST['adresse'], $_POST['téléphone']);
}

if (isset($_GET['delete'])) {
    $message[] = $controleurClient->supprimerClient($_GET['delete']);
}

if (isset($_POST['update_customer'])) {
    $controleurClient->mettreÀJourClient($_POST['update_c_id'], $_POST['update_nom'], $_POST['update_email'], $_POST['update_téléphone'], $_POST['update_adresse']);
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Clients</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
   <link rel="stylesheet" href="./css/style_admin.css">
   <link rel="stylesheet" href="css/add.css">
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
<span style="color: #005490; padding-top: 24px; font-weight: bold; display: flex; justify-content: center; font-size: 40px;">LISTE DES CLIENTS</span>

<section class="add-products" style="padding: 1rem 2rem;">
   <form class="add_sup" action="" method="post" enctype="multipart/form-data">
      <h3>Ajouter un client</h3>
      <input type="text" name="nom" class="box-item" placeholder="Nom du client" required>
      <input type="text" name="email" class="box-item" placeholder="Email" required>
      <input type="number" name="téléphone" class="box-item" placeholder="Numéro de téléphone" required>
      <input type="text" name="adresse" class="box-item" placeholder="Adresse" required>
      <input style="background-color: #005490;" onclick="added_pr()" type="submit" value="Ajouter" name="add_customer" class="btn added_pr">
   </form>
</section>
<form class="search" method="GET">
        <input type="text" name="search" placeholder="Entrez le nom du client à rechercher..." value="<?php if(isset($_GET['search'])) echo $_GET['search'] ?>">
        <button style="background-color: #005490;" type="submit" class="btn">Rechercher</button>
</form>
<button onclick="active_sup()" id="btn-sup" style="margin-bottom: 10px; margin-left: 120px;
    padding: 5px;
    font-size: 16px;
    background-color: #005490;" class="btn btn-info" >Ajouter nouveau</button>
<section class="users" style="padding: 1rem 0rem 3rem ">

   <div class="container">
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
            $sql = mysqli_query($conn, "SELECT * FROM customers WHERE name LIKE '%$search%'");
               if(mysqli_num_rows($sql) > 0){
                  while ($row = mysqli_fetch_array($sql)) {
             ?>
            <tr>
               <th scope="row"><?php echo $row['id']; ?></th>
               <td><?php echo $row['name']; ?></td>
               <td><?php echo $row['email']; ?></td>
               <td><?php echo $row['address']; ?></td>
               <td><?php echo $row['phone']; ?></td>
               <td>
                  <a style="text-decoration: none;" href="admin_customers.php?update=<?php echo $row['id']; ?>" class="fixx">Modifier</a> | 
                  <a style="text-decoration: none;" href="admin_customers.php?delete=<?php echo $row['id']; ?>" class="fixxx" onclick="return confirm('Supprimer ce client ?');">Supprimer</a>
               </td>
            </tr>
         <?php
                  }
            } else {
               echo "<tr>"; echo "<td colspan=6 align=center>"; echo '<p style="font-size: 25px;">Aucun client correspondant à votre recherche</p>'; echo "</td>"; echo "</tr>";
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
            $select_customers = mysqli_query($conn, "SELECT * FROM `customers`") or die('query failed');
            while($fetch_customers = mysqli_fetch_assoc($select_customers)){
         ?>
            <tr>
               <th scope="row"><?php echo $fetch_customers['id']; ?></th>
               <td><?php echo $fetch_customers['name']; ?></td>
               <td><?php echo $fetch_customers['email']; ?></td>
               <td><?php echo $fetch_customers['address']; ?></td>
               <td><?php echo $fetch_customers['phone']; ?></td>
               <td>
                  <a style="text-decoration: none;" href="admin_customers.php?update=<?php echo $fetch_customers['id']; ?>" class="fixx">Modifier</a> | 
                  <a style="text-decoration: none;" href="admin_customers.php?delete=<?php echo $fetch_customers['id']; ?>" class="fixxx" onclick="return confirm('Supprimer ce client ?');">Supprimer</a>
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
<section class="edit-customer-form">

   <?php
      if(isset($_GET['update'])){
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `customers` WHERE id = '$update_id'") or die('query failed');
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_c_id" value="<?php echo $fetch_update['id']; ?>">
                  <input type="text" name="update_nom" class="box-item" value="<?php echo $fetch_update['name'] ?>" placeholder="Nom du client" required>
                  <input type="text" name="update_email" class="box-item" value="<?php echo $fetch_update['email']?>" placeholder="Email" required>
                  <input type="number" name="update_téléphone" class="box-item" value="<?php echo $fetch_update['phone']?>" placeholder="Numéro de téléphone" required>
                  <input type="text" name="update_adresse" class="box-item" value="<?php echo $fetch_update['address']?>" placeholder="Adresse" required>
                  <input style="background-color: #005490;" type="submit" value="Mettre à jour" name="update_customer" class="btn btn-primary">
                  <input style="background-color: #005490;" type="reset" value="Annuler" id="close-update-customer" class="btn btn-warning">
               </form>
   <?php
            }
         }
      }else{
         echo '<script>document.querySelector(".edit-customer-form").style.display = "none";</script>';
      }
   ?>

</section>

<?php include 'footer.php'; ?>

<script>
   document.querySelector('#close-update-customer').onclick = () =>{
      document.querySelector('.edit-customer-form').style.display = 'none';
      window.location.href = 'admin_customers.php';
}
</script>
<script src="js/admin_script.js"></script>
<script src="js/add.js" ></script>
</body>
</html>
