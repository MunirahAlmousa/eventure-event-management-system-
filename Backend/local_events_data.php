<?php
/**
 * Local Static Events Data
 * These events are stored locally in the code as a backup/fallback
 * and can be used alongside the MySQL database
 */

class LocalEventsData {

    /**
     * Get all locally stored events
     * @return array Array of event objects
     */
    public static function getAllEvents() {
        return [
            // Event 1: LEAP 2025
            [
                'event_id' => 'local_1',
                'organizer_id' => 1,
                'title' => 'LEAP 2025 – Global Tech Conference',
                'description' => 'A major global technology event featuring AI innovators, startup exhibitions, and future-tech showcases.',
                'location' => 'Riyadh',
                'venue_name' => 'Riyadh Front Exhibition & Conference Center',
                'event_date' => '2025-03-03',
                'event_time' => '10:00:00',
                'category' => 'Conference',
                'max_capacity' => 25000,
                'current_attendees' => 0,
                'base_price' => 0.00,
                'currency' => 'SAR',
                'event_image' => 'https://share.google/ewyb5uewIWBrCv2eL',
                'status' => 'published',
                'is_featured' => true
            ],

            // Event 2: Black Hat MEA 2025
            [
                'event_id' => 'local_2',
                'organizer_id' => 2,
                'title' => 'Black Hat MEA 2025 – Cybersecurity Event',
                'description' => 'A world-leading cybersecurity conference offering high-level training, hacking labs, and global security exhibitions.',
                'location' => 'Riyadh',
                'venue_name' => 'Riyadh Front Expo',
                'event_date' => '2025-11-18',
                'event_time' => '09:00:00',
                'category' => 'Training',
                'max_capacity' => 12000,
                'current_attendees' => 0,
                'base_price' => 350.00,
                'currency' => 'SAR',
                'event_image' => 'https://share.google/b37VbgMyTpq2J8e8e',
                'status' => 'published',
                'is_featured' => true
            ],

            // Event 3: Riyadh Season Winter Wonderland
            [
                'event_id' => 'local_3',
                'organizer_id' => 3,
                'title' => 'Riyadh Season – Winter Wonderland Opening',
                'description' => 'A large entertainment zone featuring rides, shows, food courts, attractions, and winter-themed experiences.',
                'location' => 'Riyadh',
                'venue_name' => 'Winter Wonderland Zone',
                'event_date' => '2025-10-25',
                'event_time' => '17:00:00',
                'category' => 'Meetup',
                'max_capacity' => 50000,
                'current_attendees' => 0,
                'base_price' => 55.00,
                'currency' => 'SAR',
                'event_image' => 'https://share.google/images/jgbtZVTIJgndibyzU',
                'status' => 'published',
                'is_featured' => true
            ],

            // Event 4: MDLBEAST Soundstorm 2025
            [
                'event_id' => 'local_4',
                'organizer_id' => 4,
                'title' => 'MDLBEAST Soundstorm 2025 – Music Festival',
                'description' => 'One of the biggest music festivals with international DJs and immersive entertainment zones.',
                'location' => 'Banban, Riyadh',
                'venue_name' => 'MDLBEAST Festival Grounds',
                'event_date' => '2025-12-11',
                'event_time' => '16:00:00',
                'category' => 'Seminar',
                'max_capacity' => 40000,
                'current_attendees' => 0,
                'base_price' => 199.00,
                'currency' => 'SAR',
                'event_image' => 'https://share.google/EBtowHW7c2k58tziF',
                'status' => 'published',
                'is_featured' => true
            ],

            // Event 5: Saudi Anime Expo 2025
            [
                'event_id' => 'local_5',
                'organizer_id' => 5,
                'title' => 'Saudi Anime Expo 2025',
                'description' => 'A major anime and Japanese pop-culture event with cosplay, exhibitions, and interactive activities.',
                'location' => 'Riyadh',
                'venue_name' => 'Riyadh Front – Anime Village',
                'event_date' => '2025-08-10',
                'event_time' => '13:00:00',
                'category' => 'Conference',
                'max_capacity' => 15000,
                'current_attendees' => 0,
                'base_price' => 25.00,
                'currency' => 'SAR',
                'event_image' => 'https://share.google/images/Hc6BCaaaCBdpKq0wX',
                'status' => 'published',
                'is_featured' => false
            ],

            // Event 6: Saudi Food Expo
            [
                'event_id' => 'local_6',
                'organizer_id' => 6,
                'title' => 'Saudi Food Expo – Jeddah Edition',
                'description' => 'A large food industry exhibition showcasing restaurants, chefs, suppliers, and live cooking demos.',
                'location' => 'Jeddah',
                'venue_name' => 'Jeddah Superdome',
                'event_date' => '2025-05-14',
                'event_time' => '11:00:00',
                'category' => 'Seminar',
                'max_capacity' => 8000,
                'current_attendees' => 0,
                'base_price' => 30.00,
                'currency' => 'SAR',
                'event_image' => 'https://share.google/images/JGcFp7cmO78Nx3LVX',
                'status' => 'published',
                'is_featured' => false
            ],

            // Event 7: Saudi UX Design Bootcamp
            [
                'event_id' => 'local_7',
                'organizer_id' => 7,
                'title' => 'Saudi UX Design Bootcamp – Workshop',
                'description' => 'A hands-on workshop teaching UX fundamentals, wireframing, and prototyping.',
                'location' => 'Riyadh',
                'venue_name' => 'Digital City Hub',
                'event_date' => '2025-04-18',
                'event_time' => '14:00:00',
                'category' => 'Workshop',
                'max_capacity' => 120,
                'current_attendees' => 0,
                'base_price' => 149.00,
                'currency' => 'SAR',
                'event_image' => 'https://share.google/images/6nol2iZQnVvR6g0S8',
                'status' => 'published',
                'is_featured' => false
            ],

            // Event 8: Cybersecurity Awareness Webinar
            [
                'event_id' => 'local_8',
                'organizer_id' => 8,
                'title' => 'Cybersecurity Awareness Webinar – Online Security Training',
                'description' => 'A live webinar about phishing prevention, password hygiene, and safe browsing.',
                'location' => 'Online',
                'venue_name' => 'Zoom Live',
                'event_date' => '2025-06-22',
                'event_time' => '19:00:00',
                'category' => 'Webinar',
                'max_capacity' => 500,
                'current_attendees' => 0,
                'base_price' => 0.00,
                'currency' => 'SAR',
                'event_image' => 'https://share.google/images/ZpcRZowXDxjLp0t6F',
                'status' => 'published',
                'is_featured' => false
            ]
        ];
    }

    /**
     * Get a single event by local ID
     * @param string $localId The local event ID (e.g., 'local_1')
     * @return array|null Event data or null if not found
     */
    public static function getEventById($localId) {
        $events = self::getAllEvents();
        foreach ($events as $event) {
            if ($event['event_id'] === $localId) {
                return $event;
            }
        }
        return null;
    }

    /**
     * Get events by category
     * @param string $category The category to filter by
     * @return array Filtered array of events
     */
    public static function getEventsByCategory($category) {
        $events = self::getAllEvents();
        return array_filter($events, function($event) use ($category) {
            return strcasecmp($event['category'], $category) === 0;
        });
    }

    /**
     * Get featured events only
     * @return array Array of featured events
     */
    public static function getFeaturedEvents() {
        $events = self::getAllEvents();
        return array_filter($events, function($event) {
            return $event['is_featured'] === true;
        });
    }

    /**
     * Search events by title or description
     * @param string $searchTerm The term to search for
     * @return array Array of matching events
     */
    public static function searchEvents($searchTerm) {
        $events = self::getAllEvents();
        $searchTerm = strtolower($searchTerm);

        return array_filter($events, function($event) use ($searchTerm) {
            return strpos(strtolower($event['title']), $searchTerm) !== false ||
                   strpos(strtolower($event['description']), $searchTerm) !== false;
        });
    }
}
