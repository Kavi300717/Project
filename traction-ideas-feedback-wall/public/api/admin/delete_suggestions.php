<?php
// api/admin/delete_suggestion.php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
header('Content-Type: application/json');
if (current_user_role() !== 'admin') { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }
$in = json_decode(file_get_contents('php://input'), true);
$id = (int)($in['id'] ?? 0);
if (!$id) { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
try {
    $pdo->beginTransaction();
    $pdo->prepare("DELETE FROM suggestions WHERE id = :id")->execute(['id'=>$id]);
    $pdo->commit();
    echo json_encode(['success'=>true]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500); echo json_encode(['error'=>'Server error']); exit;
}
