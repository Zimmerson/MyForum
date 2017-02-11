<?php
//create_cat.php
include 'connect.php';
include 'header.php';

//first select the category based on $_GET['cat_id']
$sql = "SELECT
            cat_id, cat_name, cat_description
        FROM
            categories
        WHERE
            cat_id = :id";

$stmt = Database::pdo()->prepare($sql);
$success = $stmt->execute([
    'id' => $_GET['id']
]);

if(!$success)
{
    echo 'The category could not be displayed, please try again later.' . $stmt->errorInfo()[2];
}
else
{
    if($stmt->rowCount() == 0)
    {
        echo 'This category does not exist.';
    }
    else
    {
        //display category data
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        echo '<h2>Topics in ′' . $row['cat_name'] . '′ category</h2>';


        //do a query for the topics
        $sql = "SELECT  
                     topic_id, topic_subject, topic_date, topic_cat
                FROM
                    topics
                WHERE
                    topic_cat = :id";

        $stmt = Database::pdo()->prepare($sql);
        $success = $stmt->execute([
           'id' => $_GET['id']
        ]);

        if(!$success)
        {
            echo 'The topics could not be displayed, please try again later.';
        }
        else
        {
            if($stmt->rowCount() == 0)
            {
                echo 'There are no topics in this category yet.';
            }
            else
            {
                //prepare the table
                echo '<table border="1">
                      <tr>
                        <th>Topic</th>
                        <th>Created at</th>
                      </tr>';

                foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row)
                {
                    echo '<tr>';
                    echo '<td class="leftpart">';
                    echo '<h3><a href="topic.php?id=' . $row['topic_id'] . '">' . $row['topic_subject'] . '</a><h3>';
                    echo '</td>';
                    echo '<td class="rightpart">';
                    echo date('d-m-Y', strtotime($row['topic_date']));
                    echo '</td>';
                    echo '</tr>';
                }
            }
        }
    }
}

include 'footer.php';
?>
