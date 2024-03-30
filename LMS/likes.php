<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
}

if(isset($_POST['remove'])){

   if($user_id != ''){
      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      $verify_likes = pg_prepare($conn, "verify_likes", "SELECT * FROM likes WHERE user_id = $1 AND content_id = $2");
      $result_verify_likes = pg_execute($conn, "verify_likes", array($user_id, $content_id));

      if($result_verify_likes && pg_num_rows($result_verify_likes) > 0){
         $remove_likes = pg_prepare($conn, "remove_likes", "DELETE FROM likes WHERE user_id = $1 AND content_id = $2");
         pg_execute($conn, "remove_likes", array($user_id, $content_id));
         $message[] = 'removed from likes!';
      }
   }else{
      $message[] = 'please login first!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>liked videos</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- courses section starts  -->

<section class="liked-videos">

   <h1 class="heading">liked videos</h1>

   <div class="box-container">

   <?php
      $select_likes = pg_prepare($conn, "select_likes", "SELECT * FROM likes WHERE user_id = $1");
      $result_select_likes = pg_execute($conn, "select_likes", array($user_id));
      if($result_select_likes && pg_num_rows($result_select_likes) > 0){
         while($fetch_likes = pg_fetch_assoc($result_select_likes)){

            $select_contents = pg_prepare($conn, "select_contents", "SELECT * FROM content WHERE id = $1 ORDER BY date DESC");
            $result_select_contents = pg_execute($conn, "select_contents", array($fetch_likes['content_id']));

            if($result_select_contents && pg_num_rows($result_select_contents) > 0){
               while($fetch_contents = pg_fetch_assoc($result_select_contents)){

               $select_tutors = pg_prepare($conn, "select_tutors", "SELECT * FROM tutors WHERE id = $1");
               $result_select_tutors = pg_execute($conn, "select_tutors", array($fetch_contents['tutor_id']));
               $fetch_tutor = pg_fetch_assoc($result_select_tutors);
   ?>
   <div class="box">
      <div class="tutor">
         <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
         <div>
            <h3><?= $fetch_tutor['name']; ?></h3>
            <span><?= $fetch_contents['date']; ?></span>
         </div>
      </div>
      <img src="uploaded_files/<?= $fetch_contents['thumb']; ?>" alt="" class="thumb">
      <h3 class="title"><?= $fetch_contents['title']; ?></h3>
      <form action="" method="post" class="flex-btn">
         <input type="hidden" name="content_id" value="<?= $fetch_contents['id']; ?>">
         <a href="watch_video.php?get_id=<?= $fetch_contents['id']; ?>" class="inline-btn">watch video</a>
         <input type="submit" value="remove" class="inline-delete-btn" name="remove">
      </form>
   </div>
   <?php
            }
         }else{
            echo '<p class="emtpy">content was not found!</p>';         
         }
      }
   }else{
      echo '<p class="empty">nothing added to likes yet!</p>';
   }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
