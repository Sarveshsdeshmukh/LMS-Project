<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
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
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   

<section class="comments">

   <h1 class="heading">user comments</h1>

   
   <div class="show-comments">
      <?php
         $select_comments_query = "SELECT * FROM comments WHERE tutor_id = $1";
         $select_comments_result = pg_query_params($conn, $select_comments_query, array($tutor_id));
         if(pg_num_rows($select_comments_result) > 0){
            while($fetch_comment = pg_fetch_assoc($select_comments_result)){
               $select_content_query = "SELECT * FROM content WHERE id = $1";
               $select_content_result = pg_query_params($conn, $select_content_query, array($fetch_comment['content_id']));
               $fetch_content = pg_fetch_assoc($select_content_result);
      ?>
      <div class="box" style="<?php if($fetch_comment['tutor_id'] == $tutor_id){echo 'order:-1;';} ?>">
         <div class="content"><span><?= $fetch_comment['date']; ?></span><p> - <?= $fetch_content['title']; ?> - </p><a href="view_content.php?get_id=<?= $fetch_content['id']; ?>">view content</a></div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <form action="" method="post">
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
