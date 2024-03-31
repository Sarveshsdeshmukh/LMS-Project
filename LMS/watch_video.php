<?php

include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location:home.php');
}

if (isset($_POST['like_content'])) {

    if ($user_id != '') {

        $content_id = $_POST['content_id'];
        $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

        $select_content = pg_prepare($conn, "select_content", "SELECT * FROM content WHERE id = $1 AND status = $2");
        if (!$select_content) {
            $message[] = 'Error preparing select_content statement';
        } else {
            $select_content_result = pg_execute($conn, "select_content", array($get_id, 'active'));
            if (!$select_content_result) {
                $message[] = 'Error executing select_content statement';
            } else {
                $fetch_content = pg_fetch_assoc($select_content_result);

                $tutor_id = $fetch_content['tutor_id'];

                $select_likes = pg_prepare($conn, "select_likes_$content_id", "SELECT * FROM likes WHERE user_id = $1 AND content_id = $2");
                $select_likes_result = pg_execute($conn, "select_likes_$content_id", array($user_id, $content_id));

                if (pg_num_rows($select_likes_result) > 0) {
                    $remove_likes = pg_prepare($conn, "remove_likes", "DELETE FROM likes WHERE user_id = $1 AND content_id = $2");
                    pg_execute($conn, "remove_likes", array($user_id, $content_id));
                    $message[] = 'removed from likes!';
                } else {
                    $insert_likes = pg_prepare($conn, "insert_likes", "INSERT INTO likes(user_id, tutor_id, content_id) VALUES($1,$2,$3)");
                    pg_execute($conn, "insert_likes", array($user_id, $tutor_id, $content_id));
                    $message[] = 'added to likes!';
                }
            }
        }
    } else {
        $message[] = 'please login first!';
    }
}

if (isset($_POST['add_comment'])) {

    if ($user_id != '') {

        $id = unique_id();
        $comment_box = $_POST['comment_box'];
        $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);
        $content_id = $_POST['content_id'];
        $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

        $select_content = pg_prepare($conn, "select_content", "SELECT * FROM content WHERE id = $1 AND status = $2");
        if (!$select_content) {
            $message[] = 'Error preparing select_content statement';
        } else {
            $select_content_result = pg_execute($conn, "select_content", array($get_id, 'active'));
            if (!$select_content_result) {
                $message[] = 'Error executing select_content statement';
            } else {
                $fetch_content = pg_fetch_assoc($select_content_result);

                $tutor_id = $fetch_content['tutor_id'];

                if (pg_num_rows($select_content_result) > 0) {

                    $select_comment = pg_prepare($conn, "select_comment", "SELECT * FROM comments WHERE content_id = $1 AND user_id = $2 AND tutor_id = $3 AND comment = $4");
                    $select_comment_result = pg_execute($conn, "select_comment", array($content_id, $user_id, $tutor_id, $comment_box));

                    if (pg_num_rows($select_comment_result) > 0) {
                        $message[] = 'comment already added!';
                    } else {
                        $insert_comment = pg_prepare($conn, "insert_comment", "INSERT INTO comments(id, content_id, user_id, tutor_id, comment) VALUES($1,$2,$3,$4,$5)");
                        pg_execute($conn, "insert_comment", array($id, $content_id, $user_id, $tutor_id, $comment_box));
                        $message[] = 'new comment added!';
                    }
                } else {
                    $message[] = 'something went wrong!';
                }
            }
        }
    } else {
        $message[] = 'please login first!';
    }
}

if (isset($_POST['delete_comment'])) {

    $delete_id = $_POST['comment_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_comment = pg_prepare($conn, "verify_comment", "SELECT * FROM comments WHERE id = $1");
    $verify_comment_result = pg_execute($conn, "verify_comment", array($delete_id));

    if (pg_num_rows($verify_comment_result) > 0) {
        $delete_comment = pg_prepare($conn, "delete_comment", "DELETE FROM comments WHERE id = $1");
        pg_execute($conn, "delete_comment", array($delete_id));
        $message[] = 'comment deleted successfully!';
    } else {
        $message[] = 'comment already deleted!';
    }
}

if (isset($_POST['update_now'])) {

    $update_id = $_POST['update_id'];
    $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
    $update_box = isset($_POST['update_box']) ? $_POST['update_box'] : '';
    $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

    $verify_comment = pg_prepare($conn, "verify_comment", "SELECT * FROM comments WHERE id = $1 AND comment = $2");
    $verify_comment_result = pg_execute($conn, "verify_comment", array($update_id, $update_box));

    if ($verify_comment_result !== false && pg_num_rows($verify_comment_result) > 0) {
        $message[] = 'Comment already added!';
    } else {
        $update_comment = pg_prepare($conn, "update_comment", "UPDATE comments SET comment = $1 WHERE id = $2");
        pg_execute($conn, "update_comment", array($update_box, $update_id));
        $message[] = 'Comment edited successfully!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>watch video</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php include 'components/user_header.php'; ?>

    <?php
    if (isset($_POST['edit_comment'])) {
        $edit_id = $_POST['comment_id'];
        $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
        $verify_comment = pg_prepare($conn, "verify_comment", "SELECT * FROM comments WHERE id = $1 LIMIT 1");
        $verify_comment_result = pg_execute($conn, "verify_comment", array($edit_id));
        if (pg_num_rows($verify_comment_result) > 0) {
            $fetch_edit_comment = pg_fetch_assoc($verify_comment_result);
    ?>
            <section class="edit-comment">
                <h1 class="heading">edit comment</h1>
                <form action="" method="post">
                    <input type="hidden" name="update_id" value="<?= $fetch_edit_comment['id']; ?>">
                    <textarea name="update_box" class="box" maxlength="1000" required placeholder="please enter your comment" cols="30" rows="10"><?= $fetch_edit_comment['comment']; ?></textarea>
                    <div class="flex">
                        <a href="watch_video.php?get_id=<?= $get_id; ?>" class="inline-option-btn">cancel edit</a>
                        <input type="submit" value="update now" name="update_now" class="inline-btn">
                    </div>
                </form>
            </section>
    <?php
        } else {
            $message[] = 'comment was not found!';
        }
    }
    ?>

    <!-- watch video section starts  -->

    <section class="watch-video">

        <?php
        $select_content = pg_prepare($conn, "select_content", "SELECT * FROM content WHERE id = $1 AND status = $2");
        $select_content_result = pg_execute($conn, "select_content", array($get_id, 'active'));
        if ($select_content_result !== false && pg_num_rows($select_content_result) > 0) {
            while ($fetch_content = pg_fetch_assoc($select_content_result)) {
                $content_id = $fetch_content['id'];

                $select_likes = pg_prepare($conn, "select_likes_$content_id", "SELECT * FROM likes WHERE content_id = $1");
                $select_likes_result = pg_execute($conn, "select_likes_$content_id", array($content_id));
                $total_likes = pg_num_rows($select_likes_result);

                $verify_likes = pg_prepare($conn, "verify_likes", "SELECT * FROM likes WHERE user_id = $1 AND content_id = $2");
                $verify_likes_result = pg_execute($conn, "verify_likes", array($user_id, $content_id));

                $select_tutor = pg_prepare($conn, "select_tutor", "SELECT * FROM tutors WHERE id = $1 LIMIT 1");
                $select_tutor_result = pg_execute($conn, "select_tutor", array($fetch_content['tutor_id']));
                $fetch_tutor = pg_fetch_assoc($select_tutor_result);
        ?>
                <div class="video-details">
                    <video src="uploaded_files/<?= $fetch_content['video']; ?>" class="video" poster="uploaded_files/<?= $fetch_content['thumb']; ?>" controls autoplay></video>
                    <h3 class="title"><?= $fetch_content['title']; ?></h3>
                    <div class="info">
                        <p><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></p>
                        <p><i class="fas fa-heart"></i><span><?= $total_likes; ?> likes</span></p>
                    </div>
                    <div class="tutor">
                        <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
                        <div>
                            <h3><?= $fetch_tutor['name']; ?></h3>
                            <span><?= $fetch_tutor['profession']; ?></span>
                        </div>
                    </div>
                    <form action="" method="post" class="flex">
                        <input type="hidden" name="content_id" value="<?= $content_id; ?>">
                        <a href="playlist.php?get_id=<?= $fetch_content['playlist_id']; ?>" class="inline-btn">view playlist</a>
                        <?php
                        if (pg_num_rows($verify_likes_result) > 0) {
                        ?>
                            <button type="submit" name="like_content"><i class="fas fa-heart"></i><span>liked</span></button>
                        <?php
                        } else {
                        ?>
                            <button type="submit" name="like_content"><i class="far fa-heart"></i><span>like</span></button>
                        <?php
                        }
                        ?>
                    </form>
                    <div class="description"><p><?= $fetch_content['description']; ?></p></div>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">no videos added yet!</p>';
        }
        ?>

    </section>

    <!-- watch video section ends -->

    <!-- comments section starts  -->

    <section class="comments">

        <h1 class="heading">add a comment</h1>

        <form action="" method="post" class="add-comment">
            <input type="hidden" name="content_id" value="<?= $get_id; ?>">
            <textarea name="comment_box" required placeholder="write your comment..." maxlength="1000" cols="30" rows="10"></textarea>
            <input type="submit" value="add comment" name="add_comment" class="inline-btn">
        </form>

        <h1 class="heading">user comments</h1>


        <div class="show-comments">
            <?php
            $select_comments = pg_prepare($conn, "select_comments", "SELECT * FROM comments WHERE content_id = $1");
            $select_comments_result = pg_execute($conn, "select_comments", array($get_id));
            if ($select_comments_result !== false && pg_num_rows($select_comments_result) > 0) {
                while ($fetch_comment = pg_fetch_assoc($select_comments_result)) {
                    if (!isset($select_commentor)) {
                        $select_commentor = pg_prepare($conn, "select_commentor", "SELECT * FROM users WHERE id = $1");
                        if (!$select_commentor) {
                            $message[] = 'Error preparing select_commentor statement';
                        }
                    }
                    $select_commentor_result = pg_execute($conn, "select_commentor", array($fetch_comment['user_id']));
                    $fetch_commentor = pg_fetch_assoc($select_commentor_result);
            ?>
                    <div class="box" style="<?php if ($fetch_comment['user_id'] == $user_id) {
                                                echo 'order:-1;';
                                            } ?>">
                        <div class="user">
                            <img src="uploaded_files/<?= $fetch_commentor['image']; ?>" alt="">
                            <div>
                                <h3><?= $fetch_commentor['name']; ?></h3>
                                <span><?= $fetch_comment['date']; ?></span>
                            </div>
                        </div>
                        <p class="text"><?= $fetch_comment['comment']; ?></p>
                        <?php
                        if ($fetch_comment['user_id'] == $user_id) {
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
            } else {
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
