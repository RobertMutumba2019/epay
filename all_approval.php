<?php
$result = $conn->query("SELECT * FROM approval_matrix ORDER BY ap_date_added DESC");
?>

<table class="table table-bordered">
<thead>
<tr>
    <th>#</th>
    <th>Code</th>
    <th>Unit Code</th>
    <th>Date Added</th>
    <th>Added By</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php $i = 1; while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($row['ap_code']) ?></td>
    <td><?= htmlspecialchars($row['ap_unit_code']) ?></td>
    <td><?= date('Y-m-d H:i', $row['ap_date_added']) ?></td>
    <td>
        <?php
        $user_result = $conn->query("SELECT username FROM login WHERE id = {$row['ap_added_by']}");
        $user = $user_result->fetch_assoc();
        echo $user ? $user['username'] : 'Unknown';
        ?>
    </td>
    <td>
        <a href="?action=edit&id=<?= $row['ap_id'] ?>" class="btn btn-xs btn-primary">Edit</a>
        <a href="?action=delete&id=<?= $row['ap_id'] ?>" class="btn btn-xs btn-danger"
           onclick="return confirm('Delete?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>