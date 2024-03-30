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

   <h1 class="heading">all courses</h1>

   <div class="box-container">

      <?php
         // Prepare the query using pg_prepare() for PostgreSQL
         $select_courses = pg_prepare($conn, "select_courses", "SELECT * FROM playlist WHERE status = $1 ORDER BY date DESC");
         if($select_courses) {
            $result = pg_execute($conn, "select_courses", array('active'));
            if($result){
               $row_count = pg_num_rows($result);
               if($row_count > 0){
                  while($fetch_course = pg_fetch_assoc($result)){
                     $course_id = $fetch_course['id'];

                     // Fetch tutor details
                     $select_tutor = pg_prepare($conn, "select_tutor", "SELECT * FROM tutors WHERE id = $1");
                     $result_tutor = pg_execute($conn, "select_tutor", array($fetch_course['tutor_id']));
                     if ($result_tutor && pg_num_rows($result_tutor) > 0) {
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
                     } else {
                        echo '<p class="empty">Tutor details not found!</p>';
                     }
                  }
               } else {
                  echo '<p class="empty">no courses added yet!</p>';
               }
            } else {
               echo "Error executing query: " . pg_last_error($conn);
            }
         } else {
            echo "Error preparing query: " . pg_last_error($conn);
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
