<?php
require_once __DIR__ . '/../config/database.php';

class LogDAO {

    /**
     * Fetch all logs, ordered by newest first.
     * Joins with 'user' table to get the Admin's name.
     */
    public static function getAllLogs() {
        $con = Database::connect();
        
        // We join admin_log with user table to show WHO did the action
        $sql = "SELECT 
                    l.log_id, 
                    l.action, 
                    l.affected_entity, 
                    l.timestamp,
                    u.name as admin_name
                FROM admin_log l
                LEFT JOIN user u ON l.admin_user = u.user_id
                ORDER BY l.timestamp DESC";

        $result = $con->query($sql);
        $logs = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
        }

        $con->close();
        return $logs;
    }

    /**
     * Record a new action in the log
     * Usage: LogDAO::logAction($_SESSION['user_id'], 'Updated Order', 'Order #123');
     */
    public static function logAction($adminId, $action, $entity) {
        $con = Database::connect();
        
        $stmt = $con->prepare("INSERT INTO admin_log (admin_user, action, affected_entity) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $adminId, $action, $entity);
        
        $stmt->execute();
        $stmt->close();
        $con->close();
    }
}
?>