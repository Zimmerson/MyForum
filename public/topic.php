<?php
//create_cat.php
include 'connect.php';
include 'header.php';

//first select the category based on $_GET['cat_id']
$sql = "SELECT
            topic_id, topic_subject 
        FROM
            topics
        WHERE
            topic_id = :id";

$stmt = Database::pdo()->prepare($sql);
$success = $stmt->execute([
    'id' => $_GET['id']
]);

if(!$success)
{
    echo 'The topic could not be displayed, please try again later.' . $stmt->errorInfo()[2];
}
else
{
    if($stmt->rowCount() == 0)
    {
        echo 'This topic does not exist.';
    }
    else
    {
        //display topic data
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        echo '<h2>' . $row['topic_subject'] . '</h2>';


        //do a query for the posts
        $sql = "SELECT  
                    p.post_id, p.post_content, p.post_date, u.user_id, u.user_name
                FROM
                    posts p
                    LEFT JOIN users u on p.post_by = u.user_id
                WHERE
                    p.post_topic = :id";

        $stmt = Database::pdo()->prepare($sql);
        $success = $stmt->execute([
            'id' => $_GET['id']
        ]);

        if(!$success)
        {
            echo 'The posts could not be displayed, please try again later.';
        }
        else
        {
            if($stmt->rowCount() == 0)
            {
                echo 'There are no posts in this topic yet.';
            }
            else
            {
                //prepare the table
                echo '<table border="1">
                      <tr>
                        <th>Post</th>
                        <th>Posted at</th>
                        <th>Posted by</th>
                      </tr>';

                foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row)
                {
                    echo '<tr>';
                    echo '<td class="leftpart">';
                    echo $row['post_content'];
                    echo '</td>';
                    echo '<td class="rightpart">';
                    echo date('d-m-Y', strtotime($row['post_date']));
                    echo '</td>';
                    echo '<td class="rightpart">';
                    echo $row['user_name'];
                    echo '</td>';
                    echo '</tr>';
                }

                echo '</table>';

            }
        }
    }
}

?>

<form method="post" action="reply.php?id=<?=$_GET['id']?>">
    <textarea name="reply_content"></textarea>
    <input type="submit" value="Submit reply" />
</form>

<?php

include 'footer.php';

