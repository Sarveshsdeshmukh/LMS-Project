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

<!-- courses section starts  -->

<section class="courses">

   <h1 class="heading">search results</h1>

   <div class="box-container">

      <?php
         if(isset($_POST['search_course']) or isset($_POST['search_course_btn'])){
            $search_course = $_POST['search_course'];
            $select_courses = pg_prepare($conn, "select_courses", "SELECT * FROM playlist WHERE title ILIKE $1 AND status = $2");
            $result = pg_execute($conn, "select_courses", array("%$search_course%", 'active'));
            if($result && pg_num_rows($result) > 0){
               while($fetch_course = pg_fetch_assoc($result)){
                  $course_id = $fetch_course['id'];

                  $select_tutor = pg_prepare($conn, "select_tutor", "SELECT * FROM tutors WHERE id = $1");
                  $result_tutor = pg_execute($conn, "select_tutor", array($fetch_course['tutor_id']));
                  $fetch_tutor = pg_fetch_assoc($result_tutor);
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn">view playlist</a>
      </div>
      <?php
               }
            }else{
               echo '<p class="empty">no courses found!</p>';
            }
         }else{
            echo '<p class="empty">please search something!</p>';
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
