<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/header.php';

$stmt = $pdo->prepare("SELECT s.*, u.name as author_name FROM suggestions s JOIN users u ON s.user_id = u.id WHERE status = 'Resolved' ORDER BY updated_at DESC LIMIT 200");
$stmt->execute();
$resolved = $stmt->fetchAll();
?>
<h3>Resolved Suggestions</h3>
<div class="row">
  <?php if (empty($resolved)): ?>
    <div class="col-12"><p class="text-muted">No resolved suggestions yet.</p></div>
  <?php endif; ?>
  <?php foreach($resolved as $s): ?>
    <div class="col-md-6 mb-3">
      <div class="card">
        <div class="card-body">
          <h5><?php echo e($s['title']); ?></h5>
          <p class="small"><?php echo nl2br(e($s['description'])); ?></p>
          <p class="text-muted small">By <?php echo e($s['author_name']); ?> • Resolved at <?php echo e($s['updated_at'] ?? $s['created_at']); ?></p>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
