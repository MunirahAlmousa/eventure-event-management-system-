/**
 * API Service for Eventure Event Management System
 * Handles all database operations via backend API
 */

const API_BASE_URL = 'http://localhost/EventManagmentSystem_La/backend/api/index.php';

class APIService {

    // ============================================
    // USER OPERATIONS
    // ============================================

    /**
     * User Login
     */
    static async login(email, password) {
        try {
            const response = await fetch(`${API_BASE_URL}/users/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Login error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * User Registration
     */
    static async register(userData) {
        try {
            const response = await fetch(`${API_BASE_URL}/users/register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(userData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Registration error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Get User Profile
     */
    static async getUserProfile(userId) {
        try {
            const response = await fetch(`${API_BASE_URL}/users/profile/${userId}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get profile error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Get All Users (Manager only)
     */
    static async getAllUsers() {
        try {
            const response = await fetch(`${API_BASE_URL}/users/all`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get users error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    // ============================================
    // EVENT OPERATIONS
    // ============================================

    /**
     * Get All Events
     */
    static async getAllEvents() {
        try {
            const response = await fetch(`${API_BASE_URL}/events/all`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get events error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Get Event Details
     */
    static async getEventDetails(eventId) {
        try {
            const response = await fetch(`${API_BASE_URL}/events/details/${eventId}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get event details error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Get Organizer Events
     */
    static async getOrganizerEvents(organizerId) {
        try {
            const response = await fetch(`${API_BASE_URL}/events/organizer/${organizerId}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get organizer events error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Create Event
     */
    static async createEvent(eventData) {
        try {
            const response = await fetch(`${API_BASE_URL}/events/create`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(eventData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Create event error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Update Event
     */
    static async updateEvent(eventId, eventData) {
        try {
            const response = await fetch(`${API_BASE_URL}/events/update/${eventId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(eventData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Update event error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Delete Event
     */
    static async deleteEvent(eventId) {
        try {
            const response = await fetch(`${API_BASE_URL}/events/delete/${eventId}`, {
                method: 'POST'
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Delete event error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    // ============================================
    // TICKET OPERATIONS
    // ============================================

    /**
     * Get User Tickets
     */
    static async getUserTickets(userId) {
        try {
            const response = await fetch(`${API_BASE_URL}/tickets/user/${userId}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get tickets error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Create Ticket (Register for Event)
     */
    static async createTicket(ticketData) {
        try {
            const response = await fetch(`${API_BASE_URL}/tickets/create`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(ticketData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Create ticket error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Get Event Tickets
     */
    static async getEventTickets(eventId) {
        try {
            const response = await fetch(`${API_BASE_URL}/tickets/event/${eventId}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get event tickets error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    // ============================================
    // ATTENDEE OPERATIONS
    // ============================================

    /**
     * Get Event Attendees
     */
    static async getEventAttendees(eventId) {
        try {
            const response = await fetch(`${API_BASE_URL}/attendees/event/${eventId}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get attendees error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Check In Attendee
     */
    static async checkInAttendee(eventId, userId) {
        try {
            const response = await fetch(`${API_BASE_URL}/attendees/checkin`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_id: eventId, user_id: userId })
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Check-in error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Get All Attendees
     */
    static async getAllAttendees() {
        try {
            const response = await fetch(`${API_BASE_URL}/attendees/all`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get all attendees error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    // ============================================
    // COMMENT OPERATIONS
    // ============================================

    /**
     * Get Event Comments
     */
    static async getEventComments(eventId) {
        try {
            const response = await fetch(`${API_BASE_URL}/comments/event/${eventId}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get comments error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Create Comment
     */
    static async createComment(commentData) {
        try {
            const response = await fetch(`${API_BASE_URL}/comments/create`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(commentData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Create comment error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    // ============================================
    // RATING OPERATIONS
    // ============================================

    /**
     * Get Event Ratings
     */
    static async getEventRatings(eventId) {
        try {
            const response = await fetch(`${API_BASE_URL}/ratings/event/${eventId}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get ratings error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Submit Rating
     */
    static async submitRating(ratingData) {
        try {
            const response = await fetch(`${API_BASE_URL}/ratings/submit`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(ratingData)
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Submit rating error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }

    /**
     * Get User Rating for Event
     */
    static async getUserRating(eventId, userId) {
        try {
            const response = await fetch(`${API_BASE_URL}/ratings/user`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ eventId, userId })
            });
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Get user rating error:', error);
            return { success: false, message: 'Network error: ' + error.message };
        }
    }
}

// Make APIService available globally
window.APIService = APIService;
