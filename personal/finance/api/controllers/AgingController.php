<?php
namespace Controllers;
use Lib\DB;

class AgingController {
    private function aging($sqlBase) {
        $pdo = DB::pdo();
        $now = date('Y-m-d');
        $buckets = array(
            array('name'=>'0-30','min'=>0,'max'=>30),
            array('name'=>'31-60','min'=>31,'max'=>60),
            array('name'=>'61-90','min'=>61,'max'=>90),
            array('name'=>'90+','min'=>91,'max'=>3650)
        );
        $result = array(); $total = 0.0;
        foreach($buckets as $b){
            $stmt = $pdo->prepare("$sqlBase AND DATEDIFF(?, due_date) BETWEEN ? AND ?");
            $stmt->execute(array($now, $b['min'], $b['max']));
            $amt = floatval($stmt->fetch()['amt'] ?? 0);
            $result[] = array('bucket'=>$b['name'], 'amount'=>$amt);
            $total += $amt;
        }
        return array('total'=>$total,'buckets'=>$result);
    }

    public function ar(){
        $sql = "SELECT SUM((SELECT COALESCE(SUM(debit - credit),0) FROM transaction_lines tl WHERE tl.transaction_id = i.transaction_id) - (SELECT COALESCE(SUM(amount),0) FROM invoice_payments ip WHERE ip.invoice_id = i.id)) AS amt FROM invoices i WHERE i.status IN ('SENT','PARTIAL')";
        return $this->aging($sql);
    }

    public function ap(){
        $sql = "SELECT SUM((SELECT COALESCE(SUM(debit - credit),0) FROM transaction_lines tl WHERE tl.transaction_id = b.transaction_id) - (SELECT COALESCE(SUM(amount),0) FROM bill_payments bp WHERE bp.bill_id = b.id)) AS amt FROM bills b WHERE b.status IN ('OPEN','PARTIAL')";
        return $this->aging($sql);
    }
}