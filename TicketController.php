<?php
/**
 * Ticket Controller - Handles all ticket-related operations
 */

class TicketController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get User Tickets
     */
    public function getUserTickets($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT ut.*, e.title as event_title, e.date as event_date,
                       e.location as event_location, e.price as event_price
                FROM user_tickets ut
                JOIN events e ON ut.event_id = e.id
                WHERE ut.user_id = :user_id
                ORDER BY ut.created_at DESC
            ");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            $tickets = $stmt->fetchAll();

            return [
                'success' => true,
                'message' => 'Tickets retrieved',
                'data' => $tickets
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create Ticket (Register for event)
     */
    public function createTicket($data) {
        try {
            $this->db->beginTransaction();

            // Check event capacity
            $stmt = $this->db->prepare("SELECT capacity, registrations FROM events WHERE id = :event_id");
            $stmt->bindParam(':event_id', $data['event_id']);
            $stmt->execute();
            $event = $stmt->fetch();

            if ($event['registrations'] >= $event['capacity']) {
                $this->db->rollBack();
                return [
                    'success' => false,
                    'message' => 'Event is full'
                ];
            }

            // Generate QR code
            $qrCode = 'EVT-' . $data['event_id'] . '-' . $data['user_id'] . '-' . time();

            // Insert ticket
            $stmt = $this->db->prepare("
                INSERT INTO user_tickets (
                    user_id, event_id, ticket_type, qr_code, amount, payment_method, payment_date
                ) VALUES (
                    :user_id, :event_id, :ticket_type, :qr_code, :amount, :payment_method, NOW()
                )
            ");

            $stmt->execute([
                ':user_id' => $data['user_id'],
                ':event_id' => $data['event_id'],
                ':ticket_type' => $data['ticket_type'] ?? null,
                ':qr_code' => $qrCode,
                ':amount' => $data['amount'] ?? 0,
                ':payment_method' => $data['payment_method'] ?? null
            ]);

            $ticketId = $this->db->lastInsertId();

            // Update event registrations
            $updateStmt = $this->db->prepare("UPDATE events SET registrations = registrations + 1 WHERE id = :event_id");
            $updateStmt->bindParam(':event_id', $data['event_id']);
            $updateStmt->execute();

            // Update ticket type availability if applicable
            if (!empty($data['ticket_type'])) {
                $ticketTypeStmt = $this->db->prepare("
                    UPDATE ticket_types
                    SET available = available - 1
                    WHERE event_id = :event_id AND type = :type
                ");
                $ticketTypeStmt->execute([
                    ':event_id' => $data['event_id'],
                    ':type' => $data['ticket_type']
                ]);
            }

            // Add attendee record
            $attendeeStmt = $this->db->prepare("
                INSERT INTO attendees (event_id, user_id, user_name, registration_date)
                VALUES (:event_id, :user_id, :user_name, NOW())
            ");
            $attendeeStmt->execute([
                ':event_id' => $data['event_id'],
                ':user_id' => $data['user_id'],
                ':user_name' => $data['user_name'] ?? ''
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Ticket created successfully',
                'data' => [
                    'ticket_id' => $ticketId,
                    'qr_code' => $qrCode
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
     * Get Event Tickets
     */
    public function getEventTickets($eventId) {
        try {
            $stmt = $this->db->prepare("
                SELECT ut.*, u.name as user_name, u.email as user_email
                FROM user_tickets ut
                JOIN users u ON ut.user_id = u.id
                WHERE ut.event_id = :event_id
                ORDER BY ut.created_at DESC
            ");
            $stmt->bindParam(':event_id', $eventId);
            $stmt->execute();

            $tickets = $stmt->fetchAll();

            return [
                'success' => true,
                'message' => 'Event tickets retrieved',
                'data' => $tickets
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
