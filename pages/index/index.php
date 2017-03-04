<?php if (!count($data['categories'])): ?>
    There are no categories to display.
<?php else: ?>

    <table>
        <thead>
        <tr>
            <th>Category</th>
            <th>Last topic</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data['categories'] as $category): ?>
            <tr>
                <td>
                    <a href="/category?id=<?php echo $category['cat_id']; ?>"><?php echo $category['cat_name']; ?></a>
                    <?php echo $category['cat_description']; ?>
                </td>
                <td>
                    <a href="/topic?id=">Topic subject</a> as 10:10
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>
