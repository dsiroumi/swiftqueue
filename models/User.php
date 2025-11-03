<?php
/**
 * User Model
 *
 * Handles all database operations related to user accounts:
 *  - Creating new users
 *  - Finding users by email
 *
 * This class uses prepared statements to protect against SQL injection.
 */

class User
{
    /** @var PDO $pdo Database connection instance */
    private $pdo;

    /**
     * Constructor.
     *
     * @param PDO $pdo The PDO database connection.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a new user record in the database.
     *
     * @param string $firstname The user's first name.
     * @param string $lastname  The user's last name.
     * @param string $school    The user's school (optional).
     * @param string $email     The user's email address.
     * @param string $password  The user's hashed password.
     *
     * @return bool True on success, false on failure.
     */
    public function create(string $firstname, string $lastname, ?string $school, string $email, string $password): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (firstname, lastname, school, email, password)
            VALUES (:firstname, :lastname, :school, :email, :password)
        ");

        return $stmt->execute([
            ':firstname' => $firstname,
            ':lastname'  => $lastname,
            ':school'    => $school,
            ':email'     => $email,
            ':password'  => $password,
        ]);
    }

    /**
     * Find a user record by email address.
     *
     * @param string $email The user's email address.
     * @return array|false  The user record as an associative array, or false if not found.
     */
    public function findByEmail(string $email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
