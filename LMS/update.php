<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
}

if(isset($_POST['submit'])){

   $select_user = pg_prepare($conn, "select_user", "SELECT * FROM users WHERE id = $1 LIMIT 1");
   $select_user_result = pg_execute($conn, "select_user", array($user_id));
   $fetch_user = pg_fetch_assoc($select_user_result);

   $prev_pass = $fetch_user['password'];
   $prev_image = $fetch_user['image'];

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   if(!empty($name)){
      $update_name = pg_prepare($conn, "update_name", "UPDATE users SET name = $1 WHERE id = $2");
      $update_name_result = pg_execute($conn, "update_name", array($name, $user_id));
      $message[] = 'username updated successfully!';
   }

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   if(!empty($email)){
      $select_email = pg_prepare($conn, "select_email", "SELECT email FROM users WHERE email = $1");
      $select_email_result = pg_execute($conn, "select_email", array($email));
      if(pg_num_rows($select_email_result) > 0){
         $message[] = 'email already taken!';
      }else{
         $update_email = pg_prepare($conn, "update_email", "UPDATE users SET email = $1 WHERE id = $2");
         $update_email_result = pg_execute($conn, "update_email", array($email, $user_id));
         $message[] = 'email updated successfully!';
      }
   }

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = uniqid().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_files/'.$rename;

   if(!empty($image)){
      if($image_size > 2000000){
         $message[] = 'image size too large!';
      }else{
         $update_image = pg_prepare($conn, "update_image", "UPDATE users SET image = $1 WHERE id = $2");
         $update_image_result = pg_execute($conn, "update_image", array($rename, $user_id));
         move_uploaded_file($image_tmp_name, $image_folder);
         if($prev_image != '' AND $prev_image != $rename){
            unlink('uploaded_files/'.$prev_image);
         }
         $message[] = 'image updated successfully!';
      }
   }

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   if($old_pass != $empty_pass){
      if($old_pass != $prev_pass){
         $message[] = 'old password not matched!';
      }elseif($new_pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         if($new_pass != $empty_pass){
            $update_pass = pg_prepare($conn, "update_pass", "UPDATE users SET password = $1 WHERE id = $2");
            $update_pass_result = pg_execute($conn, "update_pass", array($cpass, $user_id));
            $message[] = 'password updated successfully!';
         }else{
            $message[] = 'please enter a new password!';
         }
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
   <title>update profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container" style="min-height: calc(100vh - 19rem);">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>update profile</h3>
      <div class="flex">
         <div class="col">
            <p>your name</p>
            <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" maxlength="100" class="box">
            <p>your email</p>
            <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" maxlength="100" class="box">
            <p>update pic</p>
            <input type="file" name="image" accept="image/*" class="box">
         </div>
         <div class="col">
               <p>old password</p>
               <input type="password" name="old_pass" placeholder="enter your old password" maxlength="50" class="box">
               <p>new password</p>
               <input type="password" name="new_pass" placeholder="enter your new password" maxlength="50" class="box">
               <p>confirm password</p>
               <input type="password" name="cpass" placeholder="confirm your new password" maxlength="50" class="box">
         </div>
      </div>
      <input type="submit" name="submit" value="update profile" class="btn">
   </form>

</section>

<!-- update profile section ends -->

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
