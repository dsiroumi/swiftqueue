<?php
/**
 * DashboardController
 *
 * Handles all dashboard-related actions:
 *  - Displaying courses
 *  - CRUD operations (Create, Read, Update, Delete)
 *  - Authentication enforcement
 *
 * Depends on:
 *  - Course model
 *  - Active PDO connection
 */

declare(strict_types=1);  // Enable strict typing for better type safety

include_once __DIR__ . '/../models/Course.php'; 

/**
 * Class DashboardController
 */
class DashboardController {
    private $pdo;              // PDO instance for database connection
    private $courseModel;   // Course model instance for data operations

    /**
     * Constructor to initialize PDO and instantiate the Course model.
     *
     * @param PDO $pdo The PDO database connection
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->courseModel = new Course($pdo);
    }

    /**
     * Main index method to handle requests for the dashboard.
     * Checks authentication, processes POST actions (CRUD), fetches data, and renders the view.
     */
    public function index(): void {
        // Ensure the user is authenticated
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        // Handle CRUD actions via POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];

            
        if ($action === 'create' || $action === 'update') {
            // Extract and sanitize form data
            $id = isset($_POST['id']) ? (int) $_POST['id'] : null;  // Cast to int for safety
            $name = trim($_POST['name'] ?? '');
            
            // Combine date and time for start/end
            $start_date = $_POST['start_date'] ?? '';
            $start_time = $_POST['start_time'] ?? '';
            $end_date = $_POST['end_date'] ?? '';
            $end_time = $_POST['end_time'] ?? '';
            
            $start_datetime = !empty($start_date) && !empty($start_time) ? "{$start_date} {$start_time}:00" : '';  // Add seconds if needed
            $end_datetime = !empty($end_date) && !empty($end_time) ? "{$end_date} {$end_time}:00" : '';
            
            $status = $_POST['status'] ?? 'active';
    
            // Basic server-side validation (updated for split fields)
            if (empty($name) || empty($start_date) || empty($start_time) || empty($end_date) || empty($end_time)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'All fields are required.'];
            } else {
                // Prepare data array for model
                $data = [
                    'name' => $name,
                    'start_datetime' => $start_datetime,
                    'end_datetime' => $end_datetime,
                    'status' => $status
                ];
    
                if ($action === 'create') {
                    if ($this->courseModel->create($data)) {
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'Course created successfully.'];
                    } else {
                        $_SESSION['message'] = ['type' => 'error', 'text' => 'Failed to create course. Please try again.'];
                    }
                } elseif ($action === 'update') {
                    // Ensure ID is valid for update
                    if ($id === null || $id <= 0) {
                        $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid course ID for update.'];
                    } elseif ($this->courseModel->update($id, $data)) {
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'Course updated successfully.'];
                    } else {
                        $_SESSION['message'] = ['type' => 'error', 'text' => 'Failed to update course. Please try again.'];
                    }
                }
            }
            } elseif ($action === 'delete') {
                // Extract and validate ID for deletion
                $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
                if ($id !== null && $id > 0) {
                    if ($this->courseModel->delete($id)) {
                        $_SESSION['message'] = ['type' => 'success', 'text' => 'Course deleted successfully.'];
                    } else {
                        $_SESSION['message'] = ['type' => 'error', 'text' => 'Failed to delete course. Please try again.'];
                    }
                } else {
                    $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid course ID for deletion.'];
                }
            }

            // Redirect to prevent form resubmission (Post/Redirect/Get pattern)
            header('Location: /dashboard');
            exit();
        }

        // Retrieve filter and sort parameters from GET (with defaults)
        $status = $_GET['status'] ?? '';         // Filter: 'active', 'inactive', or empty for all
        $sort = $_GET['sort'] ?? 'date_desc';    // Sort: 'a_z', 'z_a', 'date_desc', 'date_asc'

        // Fetch courses using the model
        $courses = $this->courseModel->getAll($sort, $status);

        // Fetch a single course for editing if edit_id is provided
        $editCourse = null;
        if (isset($_GET['edit_id'])) {
            $editId = (int) $_GET['edit_id'];  // Cast to int for safety
            if ($editId > 0) {
                $editCourse = $this->courseModel->getById($editId);
                if ($editCourse) {
                    // Split datetime into date and time for the form using DateTime for robustness
                    try {
                        if (!empty($editCourse['start_datetime'])) {
                            $startDt = new DateTime($editCourse['start_datetime']);
                            $editCourse['start_date'] = $startDt->format('Y-m-d');  // e.g., "2023-10-01"
                            $editCourse['start_time'] = $startDt->format('H:i');    // e.g., "14:30" (hours:minutes, no seconds)
                        }
                        if (!empty($editCourse['end_datetime'])) {
                            $endDt = new DateTime($editCourse['end_datetime']);
                            $editCourse['end_date'] = $endDt->format('Y-m-d');
                            $editCourse['end_time'] = $endDt->format('H:i');
                        }
                    } catch (Exception $e) {
                        // Log the error if parsing fails (e.g., invalid format)
                        error_log("Datetime parsing error: " . $e->getMessage());
                        // Fallback to empty values
                        $editCourse['start_date'] = $editCourse['start_time'] = '';
                        $editCourse['end_date'] = $editCourse['end_time'] = '';
                    }
                }
            }
        }

        // Retrieve and clear any flash message from session
        $message = $_SESSION['message'] ?? null;
        unset($_SESSION['message']);

        include __DIR__ . '/../views/dashboard.php';
    }
}
