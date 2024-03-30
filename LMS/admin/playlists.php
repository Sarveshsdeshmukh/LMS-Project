<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_POST['delete'])){
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_playlist_query = "SELECT * FROM playlist WHERE id = $1 AND tutor_id = $2 LIMIT 1";
   $verify_playlist_result = pg_query_params($conn, $verify_playlist_query, array($delete_id, $tutor_id));

   if(pg_num_rows($verify_playlist_result) > 0){

   

   $delete_playlist_thumb_query = "SELECT * FROM playlist WHERE id = $1 LIMIT 1";
   $delete_playlist_thumb_result = pg_query_params($conn, $delete_playlist_thumb_query, array($delete_id));
   $fetch_thumb = pg_fetch_assoc($delete_playlist_thumb_result);
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);
   $delete_bookmark_query = "DELETE FROM bookmark WHERE playlist_id = $1";
   $delete_bookmark_result = pg_query_params($conn, $delete_bookmark_query, array($delete_id));
   $delete_playlist_query = "DELETE FROM playlist WHERE id = $1";
   $delete_playlist_result = pg_query_params($conn, $delete_playlist_query, array($delete_id));
   $message[] = 'playlist deleted!';
   }else{
      $message[] = 'playlist already deleted!';
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlists</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="playlists">

   <h1 class="heading">added playlists</h1>

   <div class="box-container">
   
      <div class="box" style="text-align: center;">
         <h3 class="title" style="margin-bottom: .5rem;">create new playlist</h3>
         <a href="add_playlist.php" class="btn">add playlist</a>
      </div>

      <?php
         $select_playlist_query = "SELECT * FROM playlist WHERE tutor_id = $1 ORDER BY date DESC";
         $select_playlist_result = pg_query_params($conn, $select_playlist_query, array($tutor_id));
         if(pg_num_rows($select_playlist_result) > 0){
         while($fetch_playlist = pg_fetch_assoc($select_playlist_result)){
            $playlist_id = $fetch_playlist['id'];
            $count_videos_query = "SELECT * FROM content WHERE playlist_id = $1";
            $count_videos_result = pg_query_params($conn, $count_videos_query, array($playlist_id));
            $total_videos = pg_num_rows($count_videos_result);
      ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-circle-dot" style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fetch_playlist['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fetch_playlist['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></div>
         </div>
         <div class="thumb">
            <span><?= $total_videos; ?></span>
            <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
         </div>
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <p class="description"><?= $fetch_playlist['description']; ?></p>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $playlist_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete" class="delete-btn" onclick="return confirm('delete this playlist?');" name="delete">
         </form>
         <a href="view_playlist.php?get_id=<?= $playlist_id; ?>" class="btn">view playlist</a>
      </div>
      <?php
         } 
      }else{
         echo '<p class="empty">no playlist added yet!</p>';
      }
      ?>

   </div>

</section>













<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

<script>
   document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
      if(content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
   });
</script>

</body>
</html>
