<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_login();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) $errors[] = 'Invalid CSRF';
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $cat = $_POST['category'] ?? '';
    if (mb_strlen($title) < 3 || mb_strlen($title) > 255) $errors[] = 'Title length invalid';
    if (mb_strlen($desc) < 10) $errors[] = 'Description too short';
    if (!in_array($cat, ['Feature','Design','Bug','Idea'])) $errors[] = 'Invalid category';
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO suggestions (user_id,title,description,category) VALUES (:uid,:t,:d,:c)");
        $stmt->execute(['uid'=>current_user_id(),'t'=>$title,'d'=>$desc,'c'=>$cat]);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Suggestion added'];
        header('Location: /index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-8">
    <h3>Add Suggestion</h3>
    <?php foreach($errors as $er) echo '<div class="alert alert-danger">'.e($er).'</div>'; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <div class="mb-2"><label>Title</label><input name="title" class="form-control" required></div>
      <div class="mb-2"><label>Category</label>
        <select name="category" class="form-select" required>
          <option>Feature</option><option>Design</option><option>Bug</option><option>Idea</option>
        </select>
      </div>
      <div class="mb-2"><label>Description</label><textarea name="description" rows="6" class="form-control" required></textarea></div>
      <button class="btn btn-success">Create</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
