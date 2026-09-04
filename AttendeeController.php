<?php
/**
 * Attendee Controller - Handles attendee operations
 */

class AttendeeController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get Event Attendees
     */
    public function getEventAttendees($eventId) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, u.email, u.phone
                FROM attendees a
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.event_id = :event_id
                ORDER BY a.registration_date DESC
            ");
            $stmt->bindParam(':event_id', $eventId);
            $stmt->execute();

            $attendees = $stmt->fetchAll();

            return [
                'success' => true,
                'message' => 'Attendees retrieved',
                'data' => $attendees
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check In Attendee
     */
    public function checkInAttendee($data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE attendees
                SET checked_in = TRUE, check_in_time = NOW()
                WHERE event_id = :event_id AND user_id = :user_id
            ");

            $stmt->execute([
                ':event_id' => $data['event_id'],
                ':user_id' => $data['user_id']
            ]);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Attendee checked in successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Attendee not found'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get All Attendees
     */
    public function getAllAttendees() {
        try {
            $stmt = $this->db->query("
                SELECT a.*, e.title as event_title, u.email, u.phone
                FROM attendees a
                LEFT JOIN events e ON a.event_id = e.id
                LEFT JOIN users u ON a.user_id = u.id
                ORDER BY a.registration_date DESC
            ");

            $attendees = $stmt->fetchAll();

            return [
                'success' => true,
                'message' => 'All attendees retrieved',
                'data' => $attendees
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
