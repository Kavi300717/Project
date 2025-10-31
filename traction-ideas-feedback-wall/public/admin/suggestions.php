<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_admin();
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("SELECT s.*, u.name as author FROM suggestions s JOIN users u ON s.user_id = u.id ORDER BY s.created_at DESC");
$all = $stmt->fetchAll();
?>
<h3>All Suggestions</h3>
<table class="table table-striped">
  <thead><tr><th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Votes</th><th>Status</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach($all as $a): ?>
    <tr>
      <td><?php echo $a['id']; ?></td>
      <td><?php echo e($a['title']); ?></td>
      <td><?php echo e($a['author']); ?></td>
      <td><?php echo e($a['category']); ?></td>
      <td><?php echo (int)$a['votes']; ?></td>
      <td><?php echo e($a['status']); ?></td>
      <td>
        <button class="btn btn-sm btn-outline-secondary toggle-status" data-id="<?php echo $a['id']; ?>"><?php echo $a['status']==='Open' ? 'Resolve' : 'Unresolve'; ?></button>
        <button class="btn btn-sm btn-outline-danger admin-delete" data-id="<?php echo $a['id']; ?>">Delete</button>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
