<?php
/**
 * Course Model
 *
 * Handles all database operations related to courses:
 *  - Retrieving (all or by ID)
 *  - Creating
 *  - Updating
 *  - Deleting
 *
 * This model uses prepared statements for security and supports
 * optional sorting and filtering.
 */

class Course
{
    /** @var PDO $pdo Database connection instance */
    private $pdo;

    /**
     * Constructor.
     *
     * @param PDO $pdo The PDO database connection.
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieve all courses with optional sorting and filtering.
     *
     * @param string $sort   Sorting option: 'a_z', 'z_a', 'date_desc', 'date_asc'
     * @param string $status Filter by status: 'active', 'inactive', or '' for all
     * @return array         List of courses
     */
    public function getAll(string $sort = 'a_z', string $status = ''): array
    {
        $query = "SELECT * FROM courses";
        $params = [];

        // Apply optional status filter
        if (!empty($status)) {
            $query .= " WHERE status = :status";
            $params[':status'] = $status;
        }

        // Apply sorting rules
        switch ($sort) {
            case 'z_a':
                $query .= " ORDER BY name DESC";
                break;
            case 'date_desc':
                $query .= " ORDER BY created_at DESC";
                break;
            case 'date_asc':
                $query .= " ORDER BY created_at ASC";
                break;
            case 'a_z':
            default:
                $query .= " ORDER BY name ASC";
                break;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        // Return all matching rows as associative arrays
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retrieve a single course by its ID.
     *
     * @param int $id Course ID
     * @return array|false The course record, or false if not found
     */
    public function getById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = :id");
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new course record.
     *
     * @param array $data Course data:
     *                    - name
     *                    - start_datetime
     *                    - end_datetime
     *                    - status (optional)
     * @return bool True on success, false on failure
     */
    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO courses (name, start_datetime, end_datetime, status)
             VALUES (:name, :start_datetime, :end_datetime, :status)"
        );

        return $stmt->execute([
            ':name'           => $data['name'],
            ':start_datetime' => $data['start_datetime'],
            ':end_datetime'   => $data['end_datetime'],
            ':status'         => $data['status'] ?? 'active',
        ]);
    }

    /**
     * Update an existing course record.
     *
     * @param int   $id   Course ID
     * @param array $data Updated course data:
     *                    - name
     *                    - start_datetime
     *                    - end_datetime
     *                    - status
     * @return bool True on success, false on failure
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            "UPDATE courses SET
                name = :name,
                start_datetime = :start_datetime,
                end_datetime = :end_datetime,
                status = :status
             WHERE id = :id"
        );

        return $stmt->execute([
            ':name'           => $data['name'],
            ':start_datetime' => $data['start_datetime'],
            ':end_datetime'   => $data['end_datetime'],
            ':status'         => $data['status'],
            ':id'             => $id,
        ]);
    }

    /**
     * Delete a course by ID.
     *
     * @param int $id Course ID
     * @return bool True on success, false on failure
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = :id");

        return $stmt->execute([':id' => $id]);
    }
}
