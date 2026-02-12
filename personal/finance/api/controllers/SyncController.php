<?php
namespace Controllers;
use Lib\DB;
use Lib\Response;

class SyncController { 
    public function bootstrap() { 
        $pdo = DB::pdo();
        $accounts = $pdo->query("SELECT id,name,type FROM accounts")->fetchAll(); 
        $parties = $pdo->query("SELECT id,name,phone,whatsapp,address FROM parties")->fetchAll(); 
        return array('accounts'=>$accounts,'parties'=>$parties);
    } 

    public function changes() { 
        $since = $_GET['since'] ?? '1970-01-01 00:00:00'; 
        $pdo = DB::pdo(); 
        $sql = "SELECT t.*, tl.account_id, tl.debit, tl.credit, tl.qty, tl.price 
                FROM transactions t 
                LEFT JOIN transaction_lines tl ON t.id = tl.transaction_id 
                WHERE t.created_at > ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($since)); 
        return array('transactions'=>$stmt->fetchAll()); 
    } 

    public function push() { 
        $pdo = DB::pdo();
        $input = json_decode(file_get_contents('php://input'), true);
        $items = $input['items'] ?? array();
        
        $pdo->beginTransaction();
        try {
            foreach ($items as $item) {
                $payload = json_decode($item['payloadJson'], true);
                if ($item['entity'] === 'transactions') {
                    $stmt = $pdo->prepare("INSERT INTO transactions (date, doc_type, party_id, notes, created_by) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $payload['date'],
                        $payload['doc_type'],
                        $payload['partyId'] ?? null,
                        $payload['notes'] ?? '',
                        $_SERVER['user_id'] ?? 1
                    ]);
                }
            }
            $pdo->commit();
            return array('status'=>'success', 'count'=>count($items));
        } catch (\Exception $e) {
            $pdo->rollBack();
            Response::json(array('error'=>$e->getMessage()), 500);
            exit;
        }
    } 
}