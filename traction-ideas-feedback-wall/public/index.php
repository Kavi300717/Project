<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/header.php';

// Filters
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'votes'; // votes or date
$search = trim($_GET['q'] ?? '');

// Build query
$where = "status = 'Open'";
$params = [];

if ($category && in_array($category, ['Feature','Design','Bug','Idea'])) {
    $where .= " AND category = :category";
    $params['category'] = $category;
}
if ($search !== '') {
    $where .= " AND (title LIKE :s OR description LIKE :s)";
    $params['s'] = "%$search%";
}
$order = ($sort === 'date') ? 'created_at DESC' : 'votes DESC';

$stmt = $pdo->prepare("SELECT s.*, u.name as author_name FROM suggestions s JOIN users u ON s.user_id = u.id WHERE $where ORDER BY $order LIMIT 200");
$stmt->execute($params);
$suggestions = $stmt->fetchAll();
?>
<div class="row mb-3">
  <div class="col-md-8">
    <form class="row g-2" method="get">
      <div class="col-auto">
        <input type="text" name="q" class="form-control" placeholder="Search title/description" value="<?php echo e($search); ?>">
      </div>
      <div class="col-auto">
        <select name="category" class="form-select">
          <option value="">All categories</option>
          <?php foreach(['Feature','Design','Bug','Idea'] as $c): ?>
            <option value="<?php echo $c; ?>" <?php if($category===$c) echo 'selected'; ?>><?php echo $c; ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <select name="sort" class="form-select">
          <option value="votes" <?php if($sort==='votes') echo 'selected'; ?>>Top votes</option>
          <option value="date" <?php if($sort==='date') echo 'selected'; ?>>Newest</option>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-primary">Filter</button>
        <a class="btn btn-link" href="/resolved.php">View Resolved</a>
      </div>
    </form>
  </div>
</div>

<div class="row">
  <?php if (empty($suggestions)): ?>
    <div class="col-12"><p class="text-muted">No suggestions found.</p></div>
  <?php endif; ?>

  <?php foreach($suggestions as $s): ?>
    <div class="col-md-6 col-lg-4 mb-3">
      <div class="card h-100">
        <div class="card-body d-flex flex-column">
          <div class="d-flex justify-content-between align-items-start">
            <h5 class="card-title"><?php echo e($s['title']); ?></h5>
            <div>
              <button class="btn btn-sm btn-outline-primary upvote-btn" data-id="<?php echo $s['id']; ?>">↑ <span class="badge bg-light text-dark vote-count"><?php echo (int)$s['votes']; ?></span></button>
            </div>
          </div>
          <p class="card-text small mt-2" style="flex:1"><?php echo nl2br(e(mb_strimwidth($s['description'],0,300,'...'))); ?></p>
          <p class="mb-1"><span class="badge bg-secondary"><?php echo e($s['category']); ?></span></p>
          <p class="text-muted small mb-2">By <?php echo e($s['author_name']); ?> • <?php echo e($s['created_at']); ?></p>

          <div>
            <?php
            $can_edit = (is_logged_in() && current_user_id() == $s['user_id']);
            $admin_edit = (is_logged_in() && current_user_role()==='admin' && current_user_id()==$s['user_id']);
            ?>
            <?php if ($can_edit || $admin_edit): ?>
              <a href="/edit.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
            <?php endif; ?>

            <?php if (is_logged_in() && (current_user_id() == $s['user_id'] || current_user_role() === 'admin')): ?>
              <form action="/delete.php?id=<?php echo $s['id']; ?>" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this suggestion?');">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <button class="btn btn-sm btn-outline-danger">Delete</button>
              </form>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
