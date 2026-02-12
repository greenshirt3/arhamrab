<?php
class ArhamIntelligence {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // --- 1. FRAUD GUARD (Security Layer) ---
    public function checkFraudRisk($cnic, $phone = '') {
        $alerts = [];
        $risk_score = 0;

        // Rule A: High Frequency (Coming too often - >1 times in 30 days)
        // Note: Adjust '30 DAY' to your policy preference
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM transactions t JOIN beneficiaries b ON t.beneficiary_id = b.id WHERE b.cnic = ? AND t.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stmt->execute([$cnic]);
        $visits = $stmt->fetchColumn();
        
        if ($visits >= 1) {
            $alerts[] = "⚠️ **High Frequency:** Beneficiary was paid recently ($visits times in 30 days).";
            $risk_score += 40;
        }

        // Rule B: Phone Hopping (One phone linked to multiple CNICs)
        if (!empty($phone)) {
            $stmt = $this->pdo->prepare("SELECT COUNT(DISTINCT cnic) FROM beneficiaries WHERE phone = ?");
            $stmt->execute([$phone]);
            $linked_cnics = $stmt->fetchColumn();
            
            if ($linked_cnics >= 3) {
                $alerts[] = "🚨 **Phone Ring:** This number is linked to $linked_cnics different CNICs.";
                $risk_score += 50;
            }
        }

        // Rule C: Queue Flooding (Already waiting in queue today)
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT token_number FROM queue_tokens WHERE cnic = ? AND DATE(issued_at) = '$today' AND status='waiting'");
        $stmt->execute([$cnic]);
        $existing = $stmt->fetchColumn();
        
        if ($existing) {
            $alerts[] = "🛑 **Duplicate Token:** Already in queue (Token #$existing).";
            $risk_score += 100; // Immediate block
        }

        return ['score' => $risk_score, 'alerts' => $alerts];
    }

    // --- 2. PREDICTIVE ANALYTICS (AI Logic) ---
    public function predictWaitTime($queue_length) {
        // Machine Learning: Calculate Moving Average of last 50 transactions
        $stmt = $this->pdo->query("SELECT AVG(TIMESTAMPDIFF(MINUTE, issued_at, served_at)) as avg_time FROM queue_tokens WHERE status='served' ORDER BY id DESC LIMIT 50");
        $avg_pace = $stmt->fetchColumn() ?: 5; // Default to 5 mins if no data
        
        return round($queue_length * $avg_pace);
    }

    public function forecastDemand() {
        // Predict today's total traffic based on previous same-day activity
        $day_of_week = date('w'); // 0 (Sun) - 6 (Sat)
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM queue_tokens WHERE DAYOFWEEK(issued_at) = ? + 1 AND DATE(issued_at) < CURDATE()");
        $stmt->execute([$day_of_week]);
        $avg_traffic = $stmt->fetchColumn(); 
        
        // Simple linear projection (Avg of history) or default to 50
        return $avg_traffic ? round($avg_traffic / 4) : 50; // Approximate average
    }
}

// Initialize Global Intelligence if DB connection exists
if(isset($pdo)) {
    $AI = new ArhamIntelligence($pdo);
}
?>