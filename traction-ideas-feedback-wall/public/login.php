<?php

require_once __DIR__ . '/../includes/config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($email) || empty($password)){
        $error = "Missing email or password.";
    }
    else{
        $stmt = $pdo->prepare("SELECT id, password_hash, role, name FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        if($user && password_verify($password, $user['password_hash'])){
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            session_regenerate_id(true);
            header('Location: /index.php');
            exit;
        }else{
            $error = "Invalid credentials.";
        }
    }
}
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h3>Login</h3>
        <?php if($error): ?><div class="alert alert-danger"><?php echo e($error); ?></div><?php endif; ?>
            <form method="post">
                <div class="mb-2"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                <div class="mb-2"><label for="">Password</label><input type="password" name="password" class="form-control" required></div>
                <button class="btn btn-primary">Login</button>
            </form>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>