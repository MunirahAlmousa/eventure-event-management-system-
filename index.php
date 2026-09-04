<?php
/**
 * Main API Entry Point for Eventure Event Management System
 * Handles all API requests from the front-end
 */

// Enable error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include database configuration
require_once '../config/database.php';

// Include API controllers
require_once '../controllers/UserController.php';
require_once '../controllers/EventController.php';
require_once '../controllers/TicketController.php';
require_once '../controllers/AttendeeController.php';
require_once '../controllers/CommentController.php';
require_once '../controllers/RatingController.php';

// Get request method and data
$method = $_SERVER['REQUEST_METHOD'];
$request = [];

// Parse URL path
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Get endpoint (e.g., users, events, tickets)
$endpoint = isset($uri[3]) ? $uri[3] : '';
$action = isset($uri[4]) ? $uri[4] : '';
$id = isset($uri[5]) ? $uri[5] : '';

// Get request data
$data = json_decode(file_get_contents("php://input"), true);

// Initialize database
$database = new Database();
$db = $database->connect();

// Response array
$response = [
    'success' => false,
    'message' => '',
    'data' => null
];

try {
    // Route to appropriate controller
    switch ($endpoint) {
        case 'users':
            $controller = new UserController($db);
            $response = handleUserRequest($controller, $method, $action, $id, $data);
            break;

        case 'events':
            $controller = new EventController($db);
            $response = handleEventRequest($controller, $method, $action, $id, $data);
            break;

        case 'tickets':
            $controller = new TicketController($db);
            $response = handleTicketRequest($controller, $method, $action, $id, $data);
            break;

        case 'attendees':
            $controller = new AttendeeController($db);
            $response = handleAttendeeRequest($controller, $method, $action, $id, $data);
            break;

        case 'comments':
            $controller = new CommentController($db);
            $response = handleCommentRequest($controller, $method, $action, $id, $data);
            break;

        case 'ratings':
            $controller = new RatingController($db);
            $response = handleRatingRequest($controller, $method, $action, $id, $data);
            break;

        default:
            $response = [
                'success' => false,
                'message' => 'Invalid endpoint',
                'data' => null
            ];
            http_response_code(404);
            break;
    }

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'data' => null
    ];
    http_response_code(500);
}

// Output response
echo json_encode($response);

// ============================================
// REQUEST HANDLERS
// ============================================

function handleUserRequest($controller, $method, $action, $id, $data) {
    switch ($action) {
        case 'login':
            return $controller->login($data);
        case 'register':
            return $controller->register($data);
        case 'profile':
            return $controller->getProfile($id);
        case 'update':
            return $controller->updateProfile($id, $data);
        case 'all':
            return $controller->getAllUsers();
        default:
            if ($method === 'GET' && $id) {
                return $controller->getProfile($id);
            }
            return ['success' => false, 'message' => 'Invalid user action'];
    }
}

function handleEventRequest($controller, $method, $action, $id, $data) {
    switch ($action) {
        case 'all':
            return $controller->getAllEvents();
        case 'local':
            // Get events from local storage only
            return $controller->getAllEventsLocal();
        case 'combined':
            // Get events from both database and local storage
            return $controller->getAllEventsCombined();
        case 'fallback':
            // Get events with database fallback to local
            return $controller->getAllEventsWithFallback();
        case 'organizer':
            return $controller->getOrganizerEvents($id);
        case 'create':
            return $controller->createEvent($data);
        case 'update':
            return $controller->updateEvent($id, $data);
        case 'delete':
            return $controller->deleteEvent($id);
        case 'details':
            return $controller->getEventDetails($id);
        default:
            if ($method === 'GET' && !$id) {
                return $controller->getAllEvents();
            } elseif ($method === 'GET' && $id) {
                return $controller->getEventDetails($id);
            }
            return ['success' => false, 'message' => 'Invalid event action'];
    }
}

function handleTicketRequest($controller, $method, $action, $id, $data) {
    switch ($action) {
        case 'user':
            return $controller->getUserTickets($id);
        case 'create':
            return $controller->createTicket($data);
        case 'event':
            return $controller->getEventTickets($id);
        default:
            return ['success' => false, 'message' => 'Invalid ticket action'];
    }
}

function handleAttendeeRequest($controller, $method, $action, $id, $data) {
    switch ($action) {
        case 'event':
            return $controller->getEventAttendees($id);
        case 'checkin':
            return $controller->checkInAttendee($data);
        case 'all':
            return $controller->getAllAttendees();
        default:
            return ['success' => false, 'message' => 'Invalid attendee action'];
    }
}

function handleCommentRequest($controller, $method, $action, $id, $data) {
    switch ($action) {
        case 'event':
            return $controller->getEventComments($id);
        case 'create':
            return $controller->createComment($data);
        default:
            return ['success' => false, 'message' => 'Invalid comment action'];
    }
}

function handleRatingRequest($controller, $method, $action, $id, $data) {
    switch ($action) {
        case 'event':
            return $controller->getEventRatings($id);
        case 'submit':
            return $controller->submitRating($data);
        case 'user':
            return $controller->getUserRating($data['eventId'], $data['userId']);
        default:
            return ['success' => false, 'message' => 'Invalid rating action'];
    }
}
?>
