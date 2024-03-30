<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:contents.php');
}

if(isset($_POST['delete_video'])){

   $delete_id = $_POST['video_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $delete_video_thumb_query = "SELECT thumb FROM content WHERE id = $1 LIMIT 1";
   $delete_video_thumb_result = pg_query_params($conn, $delete_video_thumb_query, array($delete_id));
   $fetch_thumb = pg_fetch_assoc($delete_video_thumb_result);
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);

   $delete_video_query = "SELECT video FROM content WHERE id = $1 LIMIT 1";
   $delete_video_result = pg_query_params($conn, $delete_video_query, array($delete_id));
   $fetch_video = pg_fetch_assoc($delete_video_result);
   unlink('../uploaded_files/'.$fetch_video['video']);

   $delete_likes_query = "DELETE FROM likes WHERE content_id = $1";
   $delete_likes_result = pg_query_params($conn, $delete_likes_query, array($delete_id));
   $delete_comments_query = "DELETE FROM comments WHERE content_id = $1";
   $delete_comments_result = pg_query_params($conn, $delete_comments_query, array($delete_id));

   $delete_content_query = "DELETE FROM content WHERE id = $1";
   $delete_content_result = pg_query_params($conn, $delete_content_query, array($delete_id));
   header('location:contents.php');
    
}

if(isset($_POST['delete_comment'])){

   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_comment_query = "SELECT * FROM comments WHERE id = $1";
   $verify_comment_result = pg_query_params($conn, $verify_comment_query, array($delete_id));

   if(pg_num_rows($verify_comment_result) > 0){
      $delete_comment_query = "DELETE FROM comments WHERE id = $1";
      $delete_comment_result = pg_query_params($conn, $delete_comment_query, array($delete_id));
      $message[] = 'comment deleted successfully!';
   }else{
      $message[] = 'comment already deleted!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>view content</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>


<section class="view-content">

   <?php
      $select_content_query = "SELECT * FROM content WHERE id = $1 AND tutor_id = $2";
      $select_content_result = pg_query_params($conn, $select_content_query, array($get_id, $tutor_id));
      if(pg_num_rows($select_content_result) > 0){
         while($fetch_content = pg_fetch_assoc($select_content_result)){
            $video_id = $fetch_content['id'];

            $count_likes_query = "SELECT * FROM likes WHERE tutor_id = $1 AND content_id = $2";
            $count_likes_result = pg_query_params($conn, $count_likes_query, array($tutor_id, $video_id));
            $total_likes = pg_num_rows($count_likes_result);

            $count_comments_query = "SELECT * FROM comments WHERE tutor_id = $1 AND content_id = $2";
            $count_comments_result = pg_query_params($conn, $count_comments_query, array($tutor_id, $video_id));
            $total_comments = pg_num_rows($count_comments_result);
   ?>
   <div class="container">
      <video src="../uploaded_files/<?= $fetch_content['video']; ?>" autoplay controls poster="../uploaded_files/<?= $fetch_content['thumb']; ?>" class="video"></video>
      <div class="date"><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></div>
      <h3 class="title"><?= $fetch_content['title']; ?></h3>
      <div class="flex">
         <div><i class="fas fa-heart"></i><span><?= $total_likes; ?></span></div>
         <div><i class="fas fa-comment"></i><span><?= $total_comments; ?></span></div>
      </div>
      <div class="description"><?= $fetch_content['description']; ?></div>
      <form action="" method="post">
         <div class="flex-btn">
            <input type="hidden" name="video_id" value="<?= $video_id; ?>">
            <a href="update_content.php?get_id=<?= $video_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this video?');" name="delete_video">
         </div>
      </form>
   </div>
   <?php
    }
   }else{
      echo '<p class="empty">no contents added yet! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add videos</a></p>';
   }
      
   ?>

</section>

<section class="comments">

   <h1 class="heading">user comments</h1>

   
   <div class="show-comments">
      <?php
         $select_comments_query = "SELECT * FROM comments WHERE content_id = $1";
         $select_comments_result = pg_query_params($conn, $select_comments_query, array($get_id));
         if(pg_num_rows($select_comments_result) > 0){
            while($fetch_comment = pg_fetch_assoc($select_comments_result)){   
               $select_commentor_query = "SELECT * FROM users WHERE id = $1";
               $select_commentor_result = pg_query_params($conn, $select_commentor_query, array($fetch_comment['user_id']));
               $fetch_commentor = pg_fetch_assoc($select_commentor_result);
      ?>
      <div class="box">
         <div class="user">
            <img src="../uploaded_files/<?= $fetch_commentor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_commentor['name']; ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
      </div>
      <?php
       }
      }else{
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
      </div>
   
</section>












<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>
