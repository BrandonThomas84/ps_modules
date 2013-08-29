<?php
require('../functions.php');
sec_session_start(); // Our custom secure way of starting a php session.

if(isset($_POST['email'], $_POST['p'])) { 
   $email = $_POST['email'];
   $password = $_POST['p']; // The hashed password.

   if(login($email, $password, $mysqli) == true) {
      // Login success
       header('Location: ../../index.php');
   } else {
      // Login failed
      header('Location: ../../index.php?msg=er0002');
   }
} else { 
   // The correct POST variables were not sent to this page.
   echo 'Invalid Request';
}


?>