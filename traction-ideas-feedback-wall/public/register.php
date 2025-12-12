<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if (strlen($name) < 2) $errors[] = 'Name too short';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
    if (strlen($pass) < 6) $errors[] = 'Password at least 6 chars';
    if ($pass !== $pass2) $errors[] = 'Passwords do not match';

    if (empty($errors)) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password_hash,role) VALUES (:n,:e,:ph,'user')");
            $stmt->execute(['n'=>$name,'e'=>$email,'ph'=>$hash]);
            $_SESSION['flash'] = ['type'=>'success','msg'=>'Registered. You can login.'];
            header('Location: /login.php');
            exit;
        } catch (PDOException $ex) {
            if ($ex->errorInfo[1] == 1062) $errors[] = 'Email already used';
            else $errors[] = 'DB error';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center">
  <div class="col-md-6">
    <h3>Register</h3>
    <?php if ($errors): foreach($errors as $er) echo '<div class="alert alert-danger">'.e($er).'</div>'; endif; ?>
    <form method="post">
      <div class="mb-2"><label>Name</label><input type="text" name="name" class="form-control" required></div>
      <div class="mb-2"><label>Email</label><input type="email" name="email" class="form-control" required></div>
      <div class="mb-2"><label>Password</label><input type="password" name="password" class="form-control" required></div>
      <div class="mb-2"><label>Confirm</label><input type="password" name="password2" class="form-control" required></div>
      <button class="btn btn-primary">Register</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
