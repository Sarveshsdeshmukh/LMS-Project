<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['tutor_fetch'])){

   $tutor_email = $_POST['tutor_email'];
   $tutor_email = filter_var($tutor_email, FILTER_SANITIZE_STRING);
   
   // Prepare the select_tutor statement if it doesn't exist
   if (!pg_prepare($conn, "select_tutor", "SELECT * FROM tutors WHERE email = $1")) {
      echo '<p class="empty">Error preparing query: ' . pg_last_error($conn) . '</p>';
      exit; // Stop execution if there's an error
   }
   $select_tutor_result = pg_execute($conn, "select_tutor", array($tutor_email));

   $fetch_tutor = pg_fetch_assoc($select_tutor_result);
   $tutor_id = $fetch_tutor['id'];

   // Prepare the count_playlists statement if it doesn't exist
   if (!pg_prepare($conn, "count_playlists", "SELECT * FROM playlist WHERE tutor_id = $1")) {
      echo '<p class="empty">Error preparing query: ' . pg_last_error($conn) . '</p>';
      exit; // Stop execution if there's an error
   }
   $count_playlists_result = pg_execute($conn, "count_playlists", array($tutor_id));
   $total_playlists = pg_num_rows($count_playlists_result);

   // Prepare the count_contents statement if it doesn't exist
   if (!pg_prepare($conn, "count_contents", "SELECT * FROM content WHERE tutor_id = $1")) {
      echo '<p class="empty">Error preparing query: ' . pg_last_error($conn) . '</p>';
      exit; // Stop execution if there's an error
   }
   $count_contents_result = pg_execute($conn, "count_contents", array($tutor_id));
   $total_contents = pg_num_rows($count_contents_result);

   // Prepare the count_likes statement if it doesn't exist
   if (!pg_prepare($conn, "count_likes", "SELECT * FROM likes WHERE tutor_id = $1")) {
      echo '<p class="empty">Error preparing query: ' . pg_last_error($conn) . '</p>';
      exit; // Stop execution if there's an error
   }
   $count_likes_result = pg_execute($conn, "count_likes", array($tutor_id));
   $total_likes = pg_num_rows($count_likes_result);

   // Prepare the count_comments statement if it doesn't exist
   if (!pg_prepare($conn, "count_comments", "SELECT * FROM comments WHERE tutor_id = $1")) {
      echo '<p class="empty">Error preparing query: ' . pg_last_error($conn) . '</p>';
      exit; // Stop execution if there's an error
   }
   $count_comments_result = pg_execute($conn, "count_comments", array($tutor_id));
   $total_comments = pg_num_rows($count_comments_result);

}else{
   header('location:teachers.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>tutor's profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- teachers profile section starts  -->

<section class="tutor-profile">

   <h1 class="heading">profile details</h1>

   <div class="details">
      <div class="tutor">
         <img src="uploaded_files/<?= isset($fetch_tutor['image']) ? $fetch_tutor['image'] : ''; ?>" alt="">
         <h3><?= isset($fetch_tutor['name']) ? $fetch_tutor['name'] : ''; ?></h3>
         <span><?= isset($fetch_tutor['profession']) ? $fetch_tutor['profession'] : ''; ?></span>
      </div>
      <div class="flex">
         <p>total playlists : <span><?= isset($total_playlists) ? $total_playlists : ''; ?></span></p>
         <p>total videos : <span><?= isset($total_contents) ? $total_contents : ''; ?></span></p>
         <p>total likes : <span><?= isset($total_likes) ? $total_likes : ''; ?></span></p>
         <p>total comments : <span><?= isset($total_comments) ? $total_comments : ''; ?></span></p>
      </div>
   </div>

</section>

<!-- teachers profile section ends -->

<section class="courses">

   <h1 class="heading">latest courses</h1>

   <div class="box-container">

      <?php
         $select_courses = pg_prepare($conn, "select_courses", "SELECT * FROM playlist WHERE tutor_id = $1 AND status = 'active'");
         $select_courses_result = pg_execute($conn, "select_courses", array($tutor_id));
         if($select_courses_result && pg_num_rows($select_courses_result) > 0){
            while($fetch_course = pg_fetch_assoc($select_courses_result)){
               $course_id = $fetch_course['id'];

               // Reusing the select_tutor prepared statement
               $select_tutor_result = pg_execute($conn, "select_tutor", array($fetch_course['tutor_id']));
               $fetch_tutor = pg_fetch_assoc($select_tutor_result);
               // Check if $fetch_course['date'] exists before accessing it
               $course_date = isset($fetch_course['date']) ? $fetch_course['date'] : '';
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= isset($fetch_tutor['image']) ? $fetch_tutor['image'] : ''; ?>" alt="">
            <div>
               <h3><?= isset($fetch_tutor['name']) ? $fetch_tutor['name'] : ''; ?></h3>
               <span><?= $course_date; ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= isset($fetch_course['thumb']) ? $fetch_course['thumb'] : ''; ?>" class="thumb" alt="">
         <h3 class="title"><?= isset($fetch_course['title']) ? $fetch_course['title'] : ''; ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">view playlist</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no courses added yet!</p>';
      }
      ?>

   </div>

</section>

<!-- courses section ends -->

<?php include 'components/footer.php'; ?>    

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>
