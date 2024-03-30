<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:home.php');
}

if(isset($_POST['save_list'])){

   if($user_id != ''){
      
      $list_id = $_POST['list_id'];
      $list_id = filter_var($list_id, FILTER_SANITIZE_STRING);

      $select_list = pg_prepare($conn, "select_list", "SELECT * FROM bookmark WHERE user_id = $1 AND playlist_id = $2");
      $select_list_result = pg_execute($conn, "select_list", array($user_id, $list_id));

      if(pg_num_rows($select_list_result) > 0){
         $remove_bookmark = pg_prepare($conn, "remove_bookmark", "DELETE FROM bookmark WHERE user_id = $1 AND playlist_id = $2");
         pg_execute($conn, "remove_bookmark", array($user_id, $list_id));
         $message[] = 'playlist removed!';
      }else{
         $insert_bookmark = pg_prepare($conn, "insert_bookmark", "INSERT INTO bookmark(user_id, playlist_id) VALUES($1, $2)");
         pg_execute($conn, "insert_bookmark", array($user_id, $list_id));
         $message[] = 'playlist saved!';
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
   <title>playlist</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- playlist section starts  -->

<section class="playlist">

   <h1 class="heading">playlist details</h1>

   <div class="row">

      <?php
         $select_playlist = pg_prepare($conn, "select_playlist", "SELECT * FROM playlist WHERE id = $1 and status = $2 LIMIT 1");
         $select_playlist_result = pg_execute($conn, "select_playlist", array($get_id, 'active'));
         if(pg_num_rows($select_playlist_result) > 0){
            $fetch_playlist = pg_fetch_assoc($select_playlist_result);

            $playlist_id = $fetch_playlist['id'];

            $count_videos = pg_prepare($conn, "count_videos", "SELECT * FROM content WHERE playlist_id = $1");
            $count_videos_result = pg_execute($conn, "count_videos", array($playlist_id));
            $total_videos = pg_num_rows($count_videos_result);

            $select_tutor = pg_prepare($conn, "select_tutor", "SELECT * FROM tutors WHERE id = $1 LIMIT 1");
            $select_tutor_result = pg_execute($conn, "select_tutor", array($fetch_playlist['tutor_id']));
            $fetch_tutor = pg_fetch_assoc($select_tutor_result);

            $select_bookmark = pg_prepare($conn, "select_bookmark", "SELECT * FROM bookmark WHERE user_id = $1 AND playlist_id = $2");
            $select_bookmark_result = pg_execute($conn, "select_bookmark", array($user_id, $playlist_id));

      ?>

      <div class="col">
         <form action="" method="post" class="save-list">
            <input type="hidden" name="list_id" value="<?= $playlist_id; ?>">
            <?php
               if(pg_num_rows($select_bookmark_result) > 0){
            ?>
            <button type="submit" name="save_list"><i class="fas fa-bookmark"></i><span>saved</span></button>
            <?php
               }else{
            ?>
               <button type="submit" name="save_list"><i class="far fa-bookmark"></i><span>save playlist</span></button>
            <?php
               }
            ?>
         </form>
         <div class="thumb">
            <span><?= $total_videos; ?> videos</span>
            <img src="uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
      </div>

      <div class="col">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_tutor['profession']; ?></span>
            </div>
         </div>
         <div class="details">
            <h3><?= $fetch_playlist['title']; ?></h3>
            <p><?= $fetch_playlist['description']; ?></p>
            <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
      </div>

      <?php
         }else{
            echo '<p class="empty">this playlist was not found!</p>';
         }  
      ?>

   </div>

</section>

<!-- playlist section ends -->

<!-- videos container section starts  -->

<section class="videos-container">

   <h1 class="heading">playlist videos</h1>

   <div class="box-container">

      <?php
         $select_content = pg_prepare($conn, "select_content", "SELECT * FROM content WHERE playlist_id = $1 AND status = $2 ORDER BY date DESC");
         $select_content_result = pg_execute($conn, "select_content", array($get_id, 'active'));
         if(pg_num_rows($select_content_result) > 0){
            while($fetch_content = pg_fetch_assoc($select_content_result)){  
      ?>
      <a href="watch_video.php?get_id=<?= $fetch_content['id']; ?>" class="box">
         <i class="fas fa-play"></i>
         <img src="uploaded_files/<?= $fetch_content['thumb']; ?>" alt="">
         <h3><?= $fetch_content['title']; ?></h3>
      </a>
      <?php
            }
         }else{
            echo '<p class="empty">no videos added yet!</p>';
         }
      ?>

   </div>

</section>

<!-- videos container section ends -->











<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
