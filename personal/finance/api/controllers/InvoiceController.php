<?php
namespace Controllers;
use Lib\DB;

class InvoiceController { 
    public function list() { 
        $pdo = DB::pdo(); 
        $sql = "SELECT i.id, t.date, i.due_date, i.status, t.party_id FROM invoices i JOIN transactions t ON i.transaction_id=t.id ORDER BY i.id DESC"; 
        return $pdo->query($sql)->fetchAll(); 
    } 

    public function create() { 
        $pdo = DB::pdo(); 
        $b = json_decode(file_get_contents('php://input'), true); 
        $pdo->beginTransaction(); 
        $pdo->prepare("INSERT INTO transactions(date,doc_type,party_id,notes,created_by) VALUES (?,?,?,?,?)")->execute(array($b['date'],'SALE',$b['party_id'] ?? null,$b['notes']??'', $_SERVER['user_id']??1)); 
        $txnId = $pdo->lastInsertId(); 
        foreach(($b['lines'] ?? array()) as $l){ 
            $pdo->prepare("INSERT INTO transaction_lines(transaction_id,account_id,debit,credit,qty,price,tax) VALUES (?,?,?,?,?,?,?)")->execute(array($txnId,$l['account_id'],$l['debit'] ?? 0,$l['credit'] ?? 0,$l['qty']??null,$l['price']??null,$l['tax']??null)); 
        } 
        $pdo->prepare("INSERT INTO invoices(transaction_id,due_date,status) VALUES (?,?,?)")->execute(array($txnId,$b['due_date'],'SENT')); 
        $pdo->commit(); 
        return array('id'=>$txnId); 
    } 

    public function receive($id) { 
        $pdo = DB::pdo(); 
        $b = json_decode(file_get_contents('php://input'), true); 
        $amount = $b['amount'] ?? 0; 
        $pdo->prepare("INSERT INTO payments(date,party_id,amount,type) VALUES (?,?,?,?)")->execute(array(date('Y-m-d'), $b['party_id'] ?? null, $amount, 'RECEIPT')); 
        $paymentId = $pdo->lastInsertId(); 
        $pdo->prepare("INSERT INTO invoice_payments(invoice_id,payment_id,amount) VALUES (?,?,?)")->execute(array((int)$id, $paymentId, $amount)); 
        return array('status'=>'received','invoice_id'=>(int)$id,'payment_id'=>$paymentId); 
    } 
}