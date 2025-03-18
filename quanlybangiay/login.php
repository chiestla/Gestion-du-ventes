<?php

   include 'config.php';
   session_start();

   if(isset($_POST['submit'])){//récupérer les informations de connexion depuis le formulaire soumis avec name='submit'

      $email = mysqli_real_escape_string($conn, $_POST['email']);
      $mdp = mysqli_real_escape_string($conn, md5($_POST['password']));

      $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$mdp'") or die('requête échouée');

      if(mysqli_num_rows($select_users) > 0){//vérifier si le compte existe

         $row = mysqli_fetch_assoc($select_users);
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_email'] = $row['email'];
            $_SESSION['admin_id'] = $row['id'];
            header('location:home.php');

      }else{
         $message[] = 'Email ou mot de passe incorrect!';
      }

   }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Connexion</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .check_email,
      .check_password {
         color: red;
         text-align: justify;
         margin-left: 5px;
         font-size: 15px;
      }
   </style>
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>';
   }
}
?>
   
<div class="form-container">

   <form id="form" method="post">
      <h3>Connexion</h3>
      <input type="text" name="email" placeholder="Email" class="box">
      <div class="check_email"></div>
      <input type="password" name="password" placeholder="Mot de passe" class="box">
      <div class="check_password"></div>
      <input style="background-color: #005490;" type="submit" name="submit" value="Connexion" class="btn">
      <p>Vous n'avez pas de compte? <a style="color: #005490;" href="register.php">Inscrivez-vous</a></p>
   </form>

</div>
<!-- Validation du formulaire -->
<script>
  document.getElementById("form").addEventListener("submit", function(event) {
   var array = document.getElementsByTagName('input');
   var emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;
   if (array[0].value == "") {
      document.querySelector('.check_email').innerHTML = "L'email ne peut pas être vide !";
      event.preventDefault();
   } else if(!emailRegex.test(array[0].value)) {
      document.querySelector('.check_email').innerHTML = "Format d'email incorrect !";
      event.preventDefault();
   } else {
      document.querySelector('.check_email').innerHTML = "";
   }
   if (array[1].value == "") {
      document.querySelector('.check_password').innerHTML = "Le mot de passe ne peut pas être vide !";
      event.preventDefault();
   } else if(array[1].value.length < 6) {
      document.querySelector('.check_password').innerHTML = "Le mot de passe doit contenir au moins 6 caractères !";
      event.preventDefault();
   }  else {
      document.querySelector('.check_password').innerHTML = "";
   }
  });
</script>
</body>
</html>
