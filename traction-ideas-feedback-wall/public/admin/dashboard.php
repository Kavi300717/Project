<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_admin();

require_once __DIR__ . '/../includes/header.php';

// Stats
$total = $pdo->query("SELECT COUNT(*) FROM suggestions")->fetchColumn();
$open = $pdo->query("SELECT COUNT(*) FROM suggestions WHERE status='Open'")->fetchColumn();
$resolved = $pdo->query("SELECT COUNT(*) FROM suggestions WHERE status='Resolved'")->fetchColumn();
$total_votes = $pdo->query("SELECT SUM(votes) FROM suggestions")->fetchColumn();

?>
<h3>Admin Dashboard</h3>
<div class="row">
  <div class="col-md-3">
    <div class="card p-3"><h5>Total</h5><p><?php echo $total; ?></p></div>
  </div>
  <div class="col-md-3">
    <div class="card p-3"><h5>Open</h5><p><?php echo $open; ?></p></div>
  </div>
  <div class="col-md-3">
    <div class="card p-3"><h5>Resolved</h5><p><?php echo $resolved; ?></p></div>
  </div>
  <div class="col-md-3">
    <div class="card p-3"><h5>Total Votes</h5><p><?php echo $total_votes ?: 0; ?></p></div>
  </div>
</div>

<div class="mt-4">
  <a href="/admin/suggestions.php" class="btn btn-primary">Manage Suggestions</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
