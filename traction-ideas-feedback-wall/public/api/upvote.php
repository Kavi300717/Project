<?php
// api/upvote.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}
$input = json_decode(file_get_contents('php://input'), true);
$id = (int)($input['suggestion_id'] ?? 0);
if ($id <= 0) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'Invalid id']); exit; }

// fingerprint for guest
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$fp = hash('sha256', $ip . '|' . $ua . '|' . session_id());

$voter_user_id = is_logged_in() ? current_user_id() : null;

try {
    $pdo->beginTransaction();
    if ($voter_user_id) {
        $q = $pdo->prepare("SELECT id FROM votes_log WHERE suggestion_id = :sid AND voter_user_id = :vid LIMIT 1");
        $q->execute(['sid'=>$id,'vid'=>$voter_user_id]);
        if ($q->fetch()) { $pdo->rollBack(); echo json_encode(['success'=>false,'message'=>'Already voted']); exit; }
        $ins = $pdo->prepare("INSERT INTO votes_log (suggestion_id, voter_user_id) VALUES (:sid, :vid)");
        $ins->execute(['sid'=>$id,'vid'=>$voter_user_id]);
    } else {
        $q = $pdo->prepare("SELECT id FROM votes_log WHERE suggestion_id = :sid AND voter_fingerprint = :fp LIMIT 1");
        $q->execute(['sid'=>$id,'fp'=>$fp]);
        if ($q->fetch()) { $pdo->rollBack(); echo json_encode(['success'=>false,'message'=>'Already voted (guest)']); exit; }
        $ins = $pdo->prepare("INSERT INTO votes_log (suggestion_id, voter_fingerprint) VALUES (:sid, :fp)");
        $ins->execute(['sid'=>$id,'fp'=>$fp]);
    }
    $u = $pdo->prepare("UPDATE suggestions SET votes = votes + 1 WHERE id = :id");
    $u->execute(['id'=>$id]);
    $pdo->commit();
    $count = $pdo->prepare("SELECT votes FROM suggestions WHERE id = :id");
    $count->execute(['id'=>$id]);
    $c = $count->fetchColumn();
    echo json_encode(['success'=>true,'votes'=>(int)$c]);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500); echo json_encode(['success'=>false,'message'=>'Server error']); exit;
}
