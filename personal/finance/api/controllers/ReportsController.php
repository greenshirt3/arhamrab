<?php
namespace Controllers;
use Lib\DB;

class ReportsController { 
    public function pnl() { 
        $pdo = DB::pdo(); 
        
        // Income accounts: Credits increase the balance, Debits decrease it [cite: 16, 18]
        $incomeSql = "SELECT SUM(credit - debit) AS s 
                      FROM transaction_lines tl 
                      JOIN accounts a ON a.id = tl.account_id 
                      WHERE a.type = 'INCOME'";
        $income = $pdo->query($incomeSql)->fetch()['s'] ?? 0; 

        // Expense accounts: Debits increase the balance, Credits decrease it [cite: 16, 18]
        $expenseSql = "SELECT SUM(debit - credit) AS s 
                       FROM transaction_lines tl 
                       JOIN accounts a ON a.id = tl.account_id 
                       WHERE a.type = 'EXPENSE'";
        $expense = $pdo->query($expenseSql)->fetch()['s'] ?? 0; 
        
        return array(
            'income'  => floatval($income),
            'expense' => floatval($expense),
            'profit'  => floatval($income) - floatval($expense)
        ); 
    } 
}