<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

$select_likes = pg_prepare($conn, "select_likes", "SELECT * FROM likes WHERE user_id = $1");
$result_likes = pg_execute($conn, "select_likes", array($user_id));
$total_likes = pg_num_rows($result_likes);

$select_comments = pg_prepare($conn, "select_comments", "SELECT * FROM comments WHERE user_id = $1");
$result_comments = pg_execute($conn, "select_comments", array($user_id));
$total_comments = pg_num_rows($result_comments);

$select_bookmark = pg_prepare($conn, "select_bookmark", "SELECT * FROM bookmark WHERE user_id = $1");
$result_bookmark = pg_execute($conn, "select_bookmark", array($user_id));
$total_bookmarked = pg_num_rows($result_bookmark);

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- quick select section starts  -->

<section class="quick-select">

   <h1 class="heading">quick options</h1>

   <div class="box-container">

      <?php
         if($user_id != ''){
      ?>
      <div class="box">
         <h3 class="title">likes and comments</h3>
         <p>total likes : <span><?= $total_likes; ?></span></p>
         <a href="likes.php" class="inline-btn">view likes</a>
         <p>total comments : <span><?= $total_comments; ?></span></p>
         <a href="comments.php" class="inline-btn">view comments</a>
         <p>saved playlist : <span><?= $total_bookmarked; ?></span></p>
         <a href="bookmark.php" class="inline-btn">view bookmark</a>
      </div>
      <?php
         }else{ 
      ?>
      <div class="box" style="text-align: center;">
         <h3 class="title">please login or register</h3>
          <div class="flex-btn" style="padding-top: .5rem;">
            <a href="login.php" class="option-btn">login</a>
            <a href="register.php" class="option-btn">register</a>
         </div>
      </div>
      <?php
      }
      ?>

      <div class="box">
         <h3 class="title">top categories</h3>
         <div class="flex">
            <a href="search_course.php?"><i class="fas fa-code"></i><span>development</span></a>
            <a href="#"><i class="fas fa-chart-simple"></i><span>business</span></a>
            <a href="#"><i class="fas fa-pen"></i><span>design</span></a>
            <a href="#"><i class="fas fa-chart-line"></i><span>marketing</span></a>
            <a href="#"><i class="fas fa-music"></i><span>music</span></a>
            <a href="#"><i class="fas fa-camera"></i><span>photography</span></a>
            <a href="#"><i class="fas fa-cog"></i><span>software</span></a>
            <a href="#"><i class="fas fa-vial"></i><span>science</span></a>
         </div>
      </div>

      <div class="box tutor">
         <h3 class="title">become a tutor</h3>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsa, laudantium.</p>
         <a href="admin/register.php" class="inline-btn">get started</a>
      </div>

   </div>

</section>

<!-- quick select section ends -->

<!-- courses section starts  -->

<section class="courses">

   <h1 class="heading">latest courses</h1>

   <div class="box-container">

      <?php
         $select_courses = pg_prepare($conn, "select_courses", "SELECT * FROM playlist WHERE status = $1 ORDER BY date DESC LIMIT 6");
         $result_courses = pg_execute($conn, "select_courses", array('active'));
         if(pg_num_rows($result_courses) > 0){
            while($fetch_course = pg_fetch_assoc($result_courses)){
               $course_id = $fetch_course['id'];

               // Check if the prepared statement exists
               $result_tutor_exists = pg_query_params($conn, "SELECT 1 FROM pg_prepared_statements WHERE name = $1", array('select_tutor'));
               $tutor_exists_row = pg_fetch_assoc($result_tutor_exists);
               if (!$tutor_exists_row) {
                   // Prepare the statement only if it doesn't exist
                   $select_tutor = pg_prepare($conn, "select_tutor", "SELECT * FROM tutors WHERE id = $1");
               }

               // Only execute the statement if it was prepared successfully
               if(isset($select_tutor)) {
                   $result_tutor = pg_execute($conn, "select_tutor", array($fetch_course['tutor_id']));
                   if($result_tutor && pg_num_rows($result_tutor) > 0) {
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
               }
            }
         }else{
            echo '<p class="empty">no courses added yet!</p>';
         }
      ?>

   </div>

   <div class="more-btn">
      <a href="courses.php" class="inline-option-btn">view more</a>
   </div>

</section>

<!-- courses section ends -->

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->

<script>
let body = document.body;

let profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   searchForm.classList.remove('active');
}

let searchForm = document.querySelector('.header .flex .search-form');

document.querySelector('#search-btn').onclick = () =>{
   searchForm.classList.toggle('active');
   profile.classList.remove('active');
}

let sideBar = document.querySelector('.side-bar');

document.querySelector('#menu-btn').onclick = () =>{
   sideBar.classList.toggle('active');
   body.classList.toggle('active');
}

document.querySelector('.side-bar .close-side-bar').onclick = () =>{
   sideBar.classList.remove('active');
   body.classList.remove('active');
}

document.querySelectorAll('input[type="number"]').forEach(InputNumber => {
   InputNumber.oninput = () =>{
      if(InputNumber.value.length > InputNumber.maxLength) InputNumber.value = InputNumber.value.slice(0, InputNumber.maxLength);
   }
});

window.onscroll = () =>{
   profile.classList.remove('active');
   searchForm.classList.remove('active');

   if(window.innerWidth < 1200){
      sideBar.classList.remove('active');
      body.classList.remove('active');
   }

}

let toggleBtn = document.querySelector('#toggle-btn');
let darkMode = localStorage.getItem('dark-mode');

const enabelDarkMode = () =>{
   toggleBtn.classList.replace('fa-sun', 'fa-moon');
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
}

const disableDarkMode = () =>{
   toggleBtn.classList.replace('fa-moon', 'fa-sun');
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
}

if(darkMode === 'enabled'){
   enabelDarkMode();
}

toggleBtn.onclick = (e) =>{
   let darkMode = localStorage.getItem('dark-mode');
   if(darkMode === 'disabled'){
      enabelDarkMode();
   }else{
      disableDarkMode();
   }
}
</script>


<script src="js/script.js"></script>
   
</body>
</html>
