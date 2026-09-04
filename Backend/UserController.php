<?php
/**
 * User Controller - Handles all user-related operations
 */

class UserController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * User Login
     */
    public function login($data) {
        try {
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';

            if (empty($email) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'Email and password are required'
                ];
            }

            // Check if user exists
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }

            // Verify password
            // For development: direct comparison (update to password_verify in production)
            if ($user['password'] === $password || password_verify($password, $user['password'])) {
                // Remove password from response
                unset($user['password']);

                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => $user
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Login error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * User Registration
     */
    public function register($data) {
        try {
            $name = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            $phone = $data['phone'] ?? '';
            $password = $data['password'] ?? '';
            $type = $data['type'] ?? 'user';

            // Validation
            if (empty($name) || empty($email) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'Name, email, and password are required'
                ];
            }

            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Email already exists'
                ];
            }

            // Hash password (for production use password_hash)
            // For development, storing plain text temporarily
            $hashedPassword = $password; // In production: password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, phone, password, type)
                VALUES (:name, :email, :phone, :password, :type)
            ");

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':type', $type);

            if ($stmt->execute()) {
                $userId = $this->db->lastInsertId();

                // Initialize user points
                $pointsStmt = $this->db->prepare("
                    INSERT INTO user_points (user_id, points, total_registrations, total_feedback)
                    VALUES (:user_id, 0, 0, 0)
                ");
                $pointsStmt->bindParam(':user_id', $userId);
                $pointsStmt->execute();

                // Get the created user
                $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->bindParam(':id', $userId);
                $stmt->execute();
                $user = $stmt->fetch();

                unset($user['password']);

                return [
                    'success' => true,
                    'message' => 'Registration successful',
                    'data' => $user
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Registration failed'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get User Profile
     */
    public function getProfile($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $userId);
            $stmt->execute();

            $user = $stmt->fetch();

            if ($user) {
                unset($user['password']);
                return [
                    'success' => true,
                    'message' => 'Profile retrieved',
                    'data' => $user
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'User not found'
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
     * Update User Profile
     */
    public function updateProfile($userId, $data) {
        try {
            $name = $data['name'] ?? null;
            $phone = $data['phone'] ?? null;

            $updateFields = [];
            $params = [':id' => $userId];

            if ($name) {
                $updateFields[] = "name = :name";
                $params[':name'] = $name;
            }
            if ($phone) {
                $updateFields[] = "phone = :phone";
                $params[':phone'] = $phone;
            }

            if (empty($updateFields)) {
                return [
                    'success' => false,
                    'message' => 'No fields to update'
                ];
            }

            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);

            if ($stmt->execute($params)) {
                return [
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Update failed'
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
     * Get All Users (Manager only)
     */
    public function getAllUsers() {
        try {
            $stmt = $this->db->query("SELECT id, name, email, phone, type, created_at FROM users ORDER BY created_at DESC");
            $users = $stmt->fetchAll();

            return [
                'success' => true,
                'message' => 'Users retrieved',
                'data' => $users
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
