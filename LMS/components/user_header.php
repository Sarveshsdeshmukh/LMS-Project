<?php
// Include the file containing the database connection
include 'connect.php';

if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . $msg . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<header class="header">
    <section class="flex">
        <a href="home.php" class="logo">Educa.</a>
        <form action="search_course.php" method="post" class="search-form">
            <input type="text" name="search_course" placeholder="search courses..." required maxlength="100">
            <button type="submit" class="fas fa-search" name="search_course_btn"></button>
        </form>
        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="search-btn" class="fas fa-search"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <div id="toggle-btn" class="fas fa-sun"></div>
        </div>
        <div class="profile">
            <?php
            // Check if the user_id is set
            if (!empty($user_id)) {
                // Prepare and execute the statement
                $select_profile = pg_prepare($conn, "select_profile", "SELECT * FROM users WHERE id = $1");
                $select_profile_result = pg_execute($conn, "select_profile", array($user_id));
                // Check if the execution was successful
                if ($select_profile_result !== false && pg_num_rows($select_profile_result) > 0) {
                    $fetch_profile = pg_fetch_assoc($select_profile_result);
            ?>
                    <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
                    <h3><?= $fetch_profile['name']; ?></h3>
                    <span>student</span>
                    <a href="profile.php" class="btn">view profile</a>
                    <div class="flex-btn">
                        <a href="components/user_logout.php" onclick="return confirm('logout from this website?');" class="delete-btn">logout</a>
                    </div>
                <?php } else { ?>
                    <h3>Invalid user</h3>
                <?php }
            } else { ?>
                <h3>please login or register</h3>
                <div class="flex-btn" style="padding-top: .5rem;">
                    <a href="login.php" class="option-btn">login</a>
                    <a href="register.php" class="option-btn">register</a>
                </div>
            <?php } ?>
        </div>
    </section>
</header>
<div class="side-bar">
    <div class="close-side-bar">
        <i class="fas fa-times"></i>
    </div>
    <div class="profile">
        <?php
        // Check if the user_id is set
        if (!empty($user_id)) {
            // Prepare and execute the statement for sidebar
            $select_profile_sidebar = pg_prepare($conn, "select_profile_sidebar", "SELECT * FROM users WHERE id = $1");
            $select_profile_result_sidebar = pg_execute($conn, "select_profile_sidebar", array($user_id));
            // Check if the execution was successful
            if ($select_profile_result_sidebar !== false && pg_num_rows($select_profile_result_sidebar) > 0) {
                $fetch_profile_sidebar = pg_fetch_assoc($select_profile_result_sidebar);
        ?>
                <img src="uploaded_files/<?= $fetch_profile_sidebar['image']; ?>" alt="">
                <h3><?= $fetch_profile_sidebar['name']; ?></h3>
                <span>student</span>
                <a href="profile.php" class="btn">view profile</a>
            <?php } else { ?>
                <h3>Invalid user</h3>
            <?php }
        } else { ?>
            <h3>please login or register</h3>
            <div class="flex-btn" style="padding-top: .5rem;">
                <a href="login.php" class="option-btn">login</a>
                <a href="register.php" class="option-btn">register</a>
            </div>
        <?php } ?>
    </div>
    <nav class="navbar">
        <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
        <a href="about.php"><i class="fas fa-question"></i><span>about us</span></a>
        <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
        <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
        <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
    </nav>
</div>
