<?php
/**
 * Rating Controller - Handles event rating operations
 */

class RatingController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get Event Ratings
     */
    public function getEventRatings($eventId) {
        try {
            $stmt = $this->db->prepare("
                SELECT er.*, u.name as user_name
                FROM event_ratings er
                LEFT JOIN users u ON er.user_id = u.id
                WHERE er.event_id = :event_id
                ORDER BY er.created_at DESC
            ");
            $stmt->bindParam(':event_id', $eventId);
            $stmt->execute();

            $ratings = $stmt->fetchAll();

            // Calculate average rating
            $avgStmt = $this->db->prepare("
                SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings
                FROM event_ratings
                WHERE event_id = :event_id
            ");
            $avgStmt->bindParam(':event_id', $eventId);
            $avgStmt->execute();
            $stats = $avgStmt->fetch();

            return [
                'success' => true,
                'message' => 'Ratings retrieved',
                'data' => [
                    'ratings' => $ratings,
                    'average' => round($stats['avg_rating'], 2),
                    'total' => $stats['total_ratings']
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Submit Rating
     */
    public function submitRating($data) {
        try {
            $this->db->beginTransaction();

            // Check if user already rated this event
            $checkStmt = $this->db->prepare("
                SELECT id FROM event_ratings
                WHERE event_id = :event_id AND user_id = :user_id
            ");
            $checkStmt->execute([
                ':event_id' => $data['event_id'],
                ':user_id' => $data['user_id']
            ]);

            if ($checkStmt->fetch()) {
                // Update existing rating
                $stmt = $this->db->prepare("
                    UPDATE event_ratings
                    SET rating = :rating
                    WHERE event_id = :event_id AND user_id = :user_id
                ");
            } else {
                // Insert new rating
                $stmt = $this->db->prepare("
                    INSERT INTO event_ratings (event_id, user_id, rating)
                    VALUES (:event_id, :user_id, :rating)
                ");
            }

            $stmt->execute([
                ':event_id' => $data['event_id'],
                ':user_id' => $data['user_id'],
                ':rating' => $data['rating']
            ]);

            // Update event rating and reviews count
            $avgStmt = $this->db->prepare("
                SELECT AVG(rating) as avg_rating, COUNT(*) as total_ratings
                FROM event_ratings
                WHERE event_id = :event_id
            ");
            $avgStmt->bindParam(':event_id', $data['event_id']);
            $avgStmt->execute();
            $stats = $avgStmt->fetch();

            $updateStmt = $this->db->prepare("
                UPDATE events
                SET rating = :rating, reviews = :reviews
                WHERE id = :event_id
            ");
            $updateStmt->execute([
                ':rating' => round($stats['avg_rating'], 2),
                ':reviews' => $stats['total_ratings'],
                ':event_id' => $data['event_id']
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Rating submitted successfully',
                'data' => [
                    'average' => round($stats['avg_rating'], 2),
                    'total' => $stats['total_ratings']
                ]
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get User Rating for Event
     */
    public function getUserRating($eventId, $userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT rating
                FROM event_ratings
                WHERE event_id = :event_id AND user_id = :user_id
            ");
            $stmt->execute([
                ':event_id' => $eventId,
                ':user_id' => $userId
            ]);

            $result = $stmt->fetch();

            return [
                'success' => true,
                'message' => 'User rating retrieved',
                'data' => $result ? $result['rating'] : 0
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
