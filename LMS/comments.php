<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
}

if(isset($_POST['delete_comment'])){

   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_comment = pg_prepare($conn, "verify_comment_delete", "SELECT * FROM comments WHERE id = $1");
   $verify_comment_result = pg_execute($conn, "verify_comment_delete", array($delete_id));

   if(pg_num_rows($verify_comment_result) > 0){
      $delete_comment = pg_prepare($conn, "delete_comment", "DELETE FROM comments WHERE id = $1");
      $delete_comment_result = pg_execute($conn, "delete_comment", array($delete_id));
      $message[] = 'comment deleted successfully!';
   }else{
      $message[] = 'comment already deleted!';
   }

}

if(isset($_POST['update_now'])){

   $update_id = $_POST['update_id'];
   $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
   $update_box = $_POST['update_box'];
   $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

   $verify_comment = pg_prepare($conn, "verify_comment_update", "SELECT * FROM comments WHERE id = $1 AND comment = $2");
   $verify_comment_result = pg_execute($conn, "verify_comment_update", array($update_id, $update_box));

   if(pg_num_rows($verify_comment_result) > 0){
      $message[] = 'comment already added!';
   }else{
      $update_comment = pg_prepare($conn, "update_comment", "UPDATE comments SET comment = $1 WHERE id = $2");
      $update_comment_result = pg_execute($conn, "update_comment", array($update_box, $update_id));
      $message[] = 'comment edited successfully!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>user comments</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php
   if(isset($_POST['edit_comment'])){
      $edit_id = $_POST['comment_id'];
      $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
      $verify_comment = pg_prepare($conn, "verify_comment_edit", "SELECT * FROM comments WHERE id = $1 LIMIT 1");
      $verify_comment_result = pg_execute($conn, "verify_comment_edit", array($edit_id));
      if(pg_num_rows($verify_comment_result) > 0){
         $fetch_edit_comment = pg_fetch_assoc($verify_comment_result);
?>
<section class="edit-comment">
   <h1 class="heading">edit comment</h1>
   <form action="" method="post">
      <input type="hidden" name="update_id" value="<?= $fetch_edit_comment['id']; ?>">
      <textarea name="update_box" class="box" maxlength="1000" required placeholder="please enter your comment" cols="30" rows="10"><?= $fetch_edit_comment['comment']; ?></textarea>
      <div class="flex">
         <a href="comments.php" class="inline-option-btn">cancel edit</a>
         <input type="submit" value="update now" name="update_now" class="inline-btn">
      </div>
   </form>
</section>
<?php
   }else{
      $message[] = 'comment was not found!';
   }
}
?>

<section class="comments">

   <h1 class="heading">your comments</h1>

   
   <div class="show-comments">
      <?php
         $select_comments = pg_prepare($conn, "select_user_comments", "SELECT * FROM comments WHERE user_id = $1");
         $select_comments_result = pg_execute($conn, "select_user_comments", array($user_id));
         if(pg_num_rows($select_comments_result) > 0){
            while($fetch_comment = pg_fetch_assoc($select_comments_result)){
               $select_content = pg_prepare($conn, "select_content", "SELECT * FROM content WHERE id = $1");
               $select_content_result = pg_execute($conn, "select_content", array($fetch_comment['content_id']));
               $fetch_content = pg_fetch_assoc($select_content_result);
      ?>
      <div class="box" style="<?php if($fetch_comment['user_id'] == $user_id){echo 'order:-1;';} ?>">
         <div class="content"><span><?= $fetch_comment['date']; ?></span><p> - <?= $fetch_content['title']; ?> - </p><a href="watch_video.php?get_id=<?= $fetch_content['id']; ?>">view content</a></div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <?php
            if($fetch_comment['user_id'] == $user_id){ 
         ?>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" name="edit_comment" class="inline-option-btn">edit comment</button>
            <button type="submit" name="delete_comment" class="inline-delete-btn" onclick="return confirm('delete this comment?');">delete comment</button>
         </form>
         <?php
         }
         ?>
      </div>
      <?php
       }
      }else{
         echo '<p class="empty">no comments added yet!</p>';
      }
      ?>
      </div>
   
</section>

<!-- comments section ends -->

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
