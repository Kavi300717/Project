<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT user_id FROM suggestions WHERE id = :id LIMIT 1");
$stmt->execute(['id'=>$id]);
$s = $stmt->fetch();
if (!$s) { http_response_code(404); exit('Not found'); }

$owner_ok = (current_user_id() == $s['user_id']);
$admin_ok = (current_user_role() === 'admin');

if (!($owner_ok || $admin_ok)) {
    http_response_code(403); exit('Forbidden');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash'] = ['type'=>'danger','msg'=>'Invalid CSRF'];
        header('Location: /index.php');
        exit;
    }
    $d = $pdo->prepare("DELETE FROM suggestions WHERE id = :id");
    $d->execute(['id'=>$id]);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Deleted'];
    header('Location: /index.php');
    exit;
}

// show confirm form if GET
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <h3>Confirm Delete</h3>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <p>Are you sure you want to delete this suggestion?</p>
      <button class="btn btn-danger">Delete</button>
      <a class="btn btn-secondary" href="/index.php">Cancel</a>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
