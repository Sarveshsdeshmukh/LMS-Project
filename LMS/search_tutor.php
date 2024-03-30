<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>courses</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="teachers">

   <h1 class="heading">expert tutors</h1>

   <form action="" method="post" class="search-tutor">
      <input type="text" name="search_tutor" maxlength="100" placeholder="search tutor..." required>
      <button type="submit" name="search_tutor_btn" class="fas fa-search"></button>
   </form>

   <div class="box-container">

      <?php
         if(isset($_POST['search_tutor']) or isset($_POST['search_tutor_btn'])){
            $search_tutor = $_POST['search_tutor'];
            $select_tutors = pg_prepare($conn, "select_tutors", "SELECT * FROM tutors WHERE name ILIKE $1");
            $result = pg_execute($conn, "select_tutors", array("%$search_tutor%"));
            if($result && pg_num_rows($result) > 0){
               while($fetch_tutor = pg_fetch_assoc($result)){

                  $tutor_id = $fetch_tutor['id'];

                  $count_playlists = pg_prepare($conn, "count_playlists", "SELECT * FROM playlist WHERE tutor_id = $1");
                  $result_playlists = pg_execute($conn, "count_playlists", array($tutor_id));
                  $total_playlists = pg_num_rows($result_playlists);

                  $count_contents = pg_prepare($conn, "count_contents", "SELECT * FROM content WHERE tutor_id = $1");
                  $result_contents = pg_execute($conn, "count_contents", array($tutor_id));
                  $total_contents = pg_num_rows($result_contents);

                  $count_likes = pg_prepare($conn, "count_likes", "SELECT * FROM likes WHERE tutor_id = $1");
                  $result_likes = pg_execute($conn, "count_likes", array($tutor_id));
                  $total_likes = pg_num_rows($result_likes);

                  $count_comments = pg_prepare($conn, "count_comments", "SELECT * FROM comments WHERE tutor_id = $1");
                  $result_comments = pg_execute($conn, "count_comments", array($tutor_id));
                  $total_comments = pg_num_rows($result_comments);
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_tutor['profession']; ?></span>
            </div>
         </div>
         <p>playlists : <span><?= $total_playlists; ?></span></p>
         <p>total videos : <span><?= $total_contents ?></span></p>
         <p>total likes : <span><?= $total_likes ?></span></p>
         <p>total comments : <span><?= $total_comments ?></span></p>
         <form action="tutor_profile.php" method="post">
            <input type="hidden" name="tutor_email" value="<?= $fetch_tutor['email']; ?>">
            <input type="submit" value="view profile" name="tutor_fetch" class="inline-btn">
         </form>
      </div>
      <?php
               }
            }else{
               echo '<p class="empty">no results found!</p>';
            }
         }else{
            echo '<p class="empty">please search something!</p>';
         }
      ?>

   </div>

</section>

<!-- teachers section ends -->










<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
