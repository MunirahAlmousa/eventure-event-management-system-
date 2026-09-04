<?php
/**
 * Event Controller - Handles all event-related operations
 */

// Include the local events data file
require_once __DIR__ . '/../config/local_events_data.php';

class EventController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Get All Events (from database)
     */
    public function getAllEvents() {
        try {
            $stmt = $this->db->query("
                SELECT e.*,
                       (SELECT JSON_ARRAYAGG(
                           JSON_OBJECT('type', type, 'price', price, 'capacity', capacity, 'available', available)
                       ) FROM ticket_types WHERE event_id = e.id) as ticketTypes
                FROM events e
                WHERE is_sample_data = FALSE
                ORDER BY date DESC
            ");

            $events = $stmt->fetchAll();

            // Decode ticketTypes JSON
            foreach ($events as &$event) {
                $event['ticketTypes'] = json_decode($event['ticketTypes'], true) ?? [];
            }

            return [
                'success' => true,
                'message' => 'Events retrieved',
                'data' => $events
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get All Events from Local Storage (Static Array)
     * This method reads events from the local PHP array instead of database
     */
    public function getAllEventsLocal() {
        try {
            $events = LocalEventsData::getAllEvents();

            return [
                'success' => true,
                'message' => 'Events retrieved from local storage',
                'data' => $events,
                'source' => 'local'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get All Events - Combined (Database + Local)
     * This method merges events from both database and local storage
     */
    public function getAllEventsCombined() {
        try {
            // Get events from database
            $dbResult = $this->getAllEvents();
            $dbEvents = $dbResult['success'] ? $dbResult['data'] : [];

            // Get events from local storage
            $localEvents = LocalEventsData::getAllEvents();

            // Combine both arrays
            $allEvents = array_merge($dbEvents, $localEvents);

            return [
                'success' => true,
                'message' => 'Events retrieved from both database and local storage',
                'data' => $allEvents,
                'source' => 'combined',
                'db_count' => count($dbEvents),
                'local_count' => count($localEvents)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get Events with Fallback
     * Try database first, fallback to local storage if database fails
     */
    public function getAllEventsWithFallback() {
        try {
            // Try to get from database first
            $dbResult = $this->getAllEvents();

            if ($dbResult['success'] && !empty($dbResult['data'])) {
                $dbResult['source'] = 'database';
                return $dbResult;
            }

            // Fallback to local storage
            $localEvents = LocalEventsData::getAllEvents();

            return [
                'success' => true,
                'message' => 'Events retrieved from local storage (database fallback)',
                'data' => $localEvents,
                'source' => 'local_fallback'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get Event Details
     */
    public function getEventDetails($eventId) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*,
                       (SELECT JSON_ARRAYAGG(
                           JSON_OBJECT('type', type, 'price', price, 'capacity', capacity, 'available', available)
                       ) FROM ticket_types WHERE event_id = e.id) as ticketTypes
                FROM events e
                WHERE e.id = :id
            ");
            $stmt->bindParam(':id', $eventId);
            $stmt->execute();

            $event = $stmt->fetch();

            if ($event) {
                $event['ticketTypes'] = json_decode($event['ticketTypes'], true) ?? [];

                return [
                    'success' => true,
                    'message' => 'Event details retrieved',
                    'data' => $event
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Event not found'
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
     * Get Organizer Events
     */
    public function getOrganizerEvents($organizerId) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*,
                       (SELECT JSON_ARRAYAGG(
                           JSON_OBJECT('type', type, 'price', price, 'capacity', capacity, 'available', available)
                       ) FROM ticket_types WHERE event_id = e.id) as ticketTypes
                FROM events e
                WHERE e.organizer_id = :organizer_id
                ORDER BY date DESC
            ");
            $stmt->bindParam(':organizer_id', $organizerId);
            $stmt->execute();

            $events = $stmt->fetchAll();

            foreach ($events as &$event) {
                $event['ticketTypes'] = json_decode($event['ticketTypes'], true) ?? [];
            }

            return [
                'success' => true,
                'message' => 'Organizer events retrieved',
                'data' => $events
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create Event
     */
    public function createEvent($data) {
        try {
            $this->db->beginTransaction();

            // Insert event
            $stmt = $this->db->prepare("
                INSERT INTO events (
                    title, description, location, date, capacity, price, category,
                    image_url, organizer_id, organizer_name, registrations, rating, reviews, comments
                ) VALUES (
                    :title, :description, :location, :date, :capacity, :price, :category,
                    :image_url, :organizer_id, :organizer_name, 0, 0, 0, 0
                )
            ");

            $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':location' => $data['location'],
                ':date' => $data['date'],
                ':capacity' => $data['capacity'],
                ':price' => $data['price'] ?? 0,
                ':category' => $data['category'] ?? null,
                ':image_url' => $data['imageUrl'] ?? null,
                ':organizer_id' => $data['organizer_id'],
                ':organizer_name' => $data['organizer_name'] ?? ''
            ]);

            $eventId = $this->db->lastInsertId();

            // Insert ticket types
            if (!empty($data['ticketTypes'])) {
                $ticketStmt = $this->db->prepare("
                    INSERT INTO ticket_types (event_id, type, price, capacity, available)
                    VALUES (:event_id, :type, :price, :capacity, :available)
                ");

                foreach ($data['ticketTypes'] as $ticket) {
                    $ticketStmt->execute([
                        ':event_id' => $eventId,
                        ':type' => $ticket['type'],
                        ':price' => $ticket['price'],
                        ':capacity' => $ticket['capacity'],
                        ':available' => $ticket['available']
                    ]);
                }
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Event created successfully',
                'data' => ['id' => $eventId]
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Error creating event: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update Event
     */
    public function updateEvent($eventId, $data) {
        try {
            $this->db->beginTransaction();

            // Update event
            $updateFields = [];
            $params = [':id' => $eventId];

            $allowedFields = ['title', 'description', 'location', 'date', 'capacity', 'price', 'category', 'image_url'];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }

            if (!empty($updateFields)) {
                $sql = "UPDATE events SET " . implode(', ', $updateFields) . " WHERE id = :id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }

            // Update ticket types if provided
            if (isset($data['ticketTypes'])) {
                // Delete existing ticket types
                $deleteStmt = $this->db->prepare("DELETE FROM ticket_types WHERE event_id = :event_id");
                $deleteStmt->bindParam(':event_id', $eventId);
                $deleteStmt->execute();

                // Insert new ticket types
                $ticketStmt = $this->db->prepare("
                    INSERT INTO ticket_types (event_id, type, price, capacity, available)
                    VALUES (:event_id, :type, :price, :capacity, :available)
                ");

                foreach ($data['ticketTypes'] as $ticket) {
                    $ticketStmt->execute([
                        ':event_id' => $eventId,
                        ':type' => $ticket['type'],
                        ':price' => $ticket['price'],
                        ':capacity' => $ticket['capacity'],
                        ':available' => $ticket['available']
                    ]);
                }
            }

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Event updated successfully'
            ];

        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'message' => 'Error updating event: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete Event
     */
    public function deleteEvent($eventId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM events WHERE id = :id");
            $stmt->bindParam(':id', $eventId);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Event deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Delete failed'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}
?>
