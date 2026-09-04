<?php
/**
 * Comment Controller - Handles comment/feedback operations
 */

class CommentController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get Event Comments
     */
    public function getEventComments($eventId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.name as user_name
                FROM comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.event_id = :event_id
                ORDER BY c.created_at DESC
            ");
            $stmt->bindParam(':event_id', $eventId);
            $stmt->execute();

            $comments = $stmt->fetchAll();

            return [
                'success' => true,
                'message' => 'Comments retrieved',
                'data' => $comments
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create Comment
     */
    public function createComment($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO comments (event_id, user_id, user_name, comment)
                VALUES (:event_id, :user_id, :user_name, :comment)
            ");

            $stmt->execute([
                ':event_id' => $data['event_id'],
                ':user_id' => $data['user_id'],
                ':user_name' => $data['user_name'] ?? '',
                ':comment' => $data['comment']
            ]);

            // Update event comments count
            $updateStmt = $this->db->prepare("UPDATE events SET comments = comments + 1 WHERE id = :event_id");
            $updateStmt->bindParam(':event_id', $data['event_id']);
            $updateStmt->execute();

            return [
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => ['id' => $this->db->lastInsertId()]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
?>
