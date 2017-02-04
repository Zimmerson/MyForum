<?php
//create_cat.php
require 'connect.php';
include 'header.php';

echo '<h2>Create a topic</h2>';

if (!isset($_SESSION['signed_in']) || $_SESSION['signed_in'] == false) {
    //the user is not signed in
    echo 'Sorry, you have to be <a href="signin.php">signed in</a> to create a topic.';
} else {

    //the user is signed in
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        //the form hasn't been posted yet, display it
        //retrieve the categories from the database for use in the dropdown
        $sql = "SELECT
                    cat_id,
                    cat_name,
                    cat_description
                FROM
                    categories";

        $stmt = Database::pdo()->prepare($sql);
        $success = $stmt->execute();

        if (!$success) {
            //the query failed, uh-oh :-(
            echo 'Error while selecting from database. Please try again later.';
        } else {
            if ($stmt->rowCount() === 0) {
                //there are no categories, so a topic can't be posted
                if ($_SESSION['user_level'] == 1) {
                    echo 'You have not created categories yet.';
                } else {
                    echo 'Before you can post a topic, you must wait for an admin to create some categories.';
                }
            } else {

                echo '<form method="post" action="">
                    Subject: <input type="text" name="topic_subject" />
                    Category:';

                echo '<select name="topic_cat">';
                foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    echo '<option value="' . $row['cat_id'] . '">' . $row['cat_name'] . '</option>';
                }
                echo '</select>';

                echo 'Message: <textarea name="post_content" /></textarea>
                    <input type="submit" value="Create topic" />
                 </form>';
            }
        }
    } else {
        //start the transaction
        $success = Database::pdo()->beginTransaction();

        if (!$success) {
            //Damn! the query failed, quit
            echo 'An error occurred while creating your topic. Please try again later.';
        } else {

            //the form has been posted, so save it
            //insert the topic into the topics table first, then we'll save the post into the posts table
            $sql = "INSERT INTO topics
                      (topic_subject, topic_date, topic_cat, topic_by)
                    VALUES
                      (:subject, NOW(), :category, :user)";

            $stmt = Database::pdo()->prepare($sql);
            $success = $stmt->execute([
                'subject' => $_POST['topic_subject'],
                'category' => $_POST['topic_cat'],
                'user' => $_SESSION['user_id']
            ]);

            if (!$success) {
                //something went wrong, display the error
                echo 'An error occurred while inserting your data. Please try again later.' . mysql_error();
                Database::pdo()->rollBack();
            } else {
                //the first query worked, now start the second, posts query
                //retrieve the id of the freshly created topic for usage in the posts query
                $topicId = Database::pdo()->lastInsertId();

                $sql = "INSERT INTO posts
                            (post_content, post_date, post_topic, post_by)
                        VALUES
                            (:content, NOW(), :topic, :user)";

                $stmt = Database::pdo()->prepare($sql);
                $success = $stmt->execute([
                    'content' => $_POST['post_content'],
                    'topic' => $topicId,
                    'user' => $_SESSION['user_id']
                ]);

                if (!$success) {
                    //something went wrong, display the error
                    echo 'An error occurred while inserting your post. Please try again later.' . $stmt->errorInfo()[2];
                    Database::pdo()->rollBack();
                } else {
                    Database::pdo()->commit();
                    //after a lot of work, the query succeeded!
                    echo 'You have successfully created <a href="topic.php?id=' . $topicId . '">your new topic</a>.';
                }
            }
        }
    }
}

include 'footer.php';
