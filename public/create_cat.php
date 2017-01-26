<?php
include 'connect.php';
include 'header.php';

//echo '<tr>';
//    echo '<td class="leftpart">';
//        echo '<h3><a href="category.php?id=">Category name</a></h3> Category description goes here';
//        echo '</td>';
//    echo '<td class="rightpart">';
//        echo '<a href="topic.php?id=">Topic subject</a> at 10-10';
//        echo '</td>';
//    echo '</tr>';

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    //the form hasn't been posted yet, display it
    echo "<form method='post' action=''>
        Category name: <input type='text' name='cat_name' />
        Category description: <textarea name='cat_description' /></textarea>
        <input type='submit' value='Add category' />
     </form>";
}
else
{
    //the form has been posted, so save it
    $sql = "INSERT INTO categories
              (cat_name, cat_description)
            VALUES
              (:name, :description);";

    $stmt = Database::getInstance()->getPDO()->prepare($sql);
    $success = $stmt->execute([
        'name' => $_POST['cat_name'],
        'description' => $_POST['cat_description']
    ]);

    if(!$success)
    {
        //something went wrong, display the error
        echo 'Something went wrong while signing in. Please try again later.';
        //Debug
        echo $stmt->errorInfo()[2];
    }
    else
    {
        echo 'New category successfully added.';
    }
}

include 'footer.php';

?>