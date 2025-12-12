<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM suggestions WHERE id = :id LIMIT 1");
$stmt->execute(['id'=>$id]);
$s = $stmt->fetch();
if (!$s) { http_response_code(404); exit('Not found'); }

// ownership rule: owner can edit own; admin can edit only admin-owned suggestions.
$owner_ok = (current_user_id() == $s['user_id']);
$admin_ok = (current_user_role() === 'admin' && current_user_id() == $s['user_id']);

if (!($owner_ok || $admin_ok)) {
    http_response_code(403); exit('Forbidden');
}

$errors=[];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) $errors[] = 'Invalid CSRF';
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $cat = $_POST['category'] ?? '';
    if (mb_strlen($title) < 3 || mb_strlen($title) > 255) $errors[] = 'Title invalid';
    if (mb_strlen($desc) < 10) $errors[] = 'Description too short';
    if (!in_array($cat, ['Feature','Design','Bug','Idea'])) $errors[] = 'Invalid category';
    if (empty($errors)) {
        $u = $pdo->prepare("UPDATE suggestions SET title = :t, description = :d, category = :c, updated_at = NOW() WHERE id = :id");
        $u->execute(['t'=>$title,'d'=>$desc,'c'=>$cat,'id'=>$id]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Updated'];
        header('Location: /index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <h3>Edit Suggestion</h3>
    <?php foreach($errors as $er) echo '<div class="alert alert-danger">'.e($er).'</div>'; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <div class="mb-2"><label>Title</label><input name="title" class="form-control" required value="<?php echo e($s['title']); ?>"></div>
      <div class="mb-2"><label>Category</label>
        <select name="category" class="form-select" required>
          <?php foreach(['Feature','Design','Bug','Idea'] as $c): ?>
            <option <?php if($s['category']===$c) echo 'selected'; ?>><?php echo $c; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-2"><label>Description</label><textarea name="description" rows="6" class="form-control" required><?php echo e($s['description']); ?></textarea></div>
      <button class="btn btn-primary">Save</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
