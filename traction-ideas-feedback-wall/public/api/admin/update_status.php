<?php
// api/admin/update_status.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
header('Content-Type: application/json');
if (current_user_role() !== 'admin') { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }
$in = json_decode(file_get_contents('php://input'), true);
$id = (int)($in['id'] ?? 0);
if (!$id) { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
$stmt = $pdo->prepare("SELECT status FROM suggestions WHERE id = :id");
$stmt->execute(['id'=>$id]);
$old = $stmt->fetchColumn();
if ($old === false) { http_response_code(404); echo json_encode(['error'=>'Not found']); exit; }
$new = $old === 'Open' ? 'Resolved' : 'Open';
$pdo->prepare("UPDATE suggestions SET status = :st, updated_at = NOW() WHERE id = :id")->execute(['st'=>$new,'id'=>$id]);
echo json_encode(['success'=>true,'status'=>$new]);
