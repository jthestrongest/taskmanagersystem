<?php

@include 'config.php';

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = strtolower(mysqli_real_escape_string($conn, $_POST['email']));
   $pass = $_POST['password'];
   $cpass = $_POST['cpassword'];
   $user_type = $_POST['user_type'];

   $select = "SELECT * FROM user_form WHERE email = '$email'";
   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){
      $error[] = 'user already exists!';
   } else {
      if($pass != $cpass){
         $error[] = 'passwords do not match!';
      } else {
      
         $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
         $insert = "INSERT INTO user_form(name, email, password, user_type) VALUES('$name','$email','$hashed_pass','$user_type')";
         mysqli_query($conn, $insert);
         header('location:index.php');
      }
   }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register form</title>
   <link rel="stylesheet" href="designforregister.css" />
</head>
<body>
   <h1>Task Management System</h1>
   
   <link rel="stylesheet" href="designforregister.css?v=1.0" />

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Register Now</h3>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <input type="text" name="name" required placeholder="Enter your name">
      <input type="email" name="email" required placeholder="enter your email">
      <input type="password" name="password" required placeholder="enter your password">
      <input type="password" name="cpassword" required placeholder="confirm your password">
      <select name="user_type" required>
         <option value="" disabled selected>Select user type</option>
         <option value="user">User</option>
         <option value="admin">Admin</option>
      </select>
      <input type="submit" name="submit" value="Register Now" class="form-btn">
      <p>already have an account? <a href="index.php">login now</a></p>
   </form>

</div>

</body>
</html>