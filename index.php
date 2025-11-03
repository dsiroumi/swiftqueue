<?php
/**
 * index.php
 *
 * Front controller: handles routing and dispatching requests to the appropriate controller/action.
 */

// Start output buffering
ob_start();

// Load configuration
$config = include __DIR__ . '/config.php';

// Start session (if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include 'db.php';

// -------------------------
// Simple Router
// -------------------------

// Get the requested path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/'); // Remove leading/trailing slashes
$pathParts = explode('/', $path);

// Determine controller and action
if (empty($pathParts[0])) {
    // Default route: '/' → auth/login
    $controllerName = 'auth';
    $action = 'login';
} elseif (count($pathParts) === 1) {
    $segment = $pathParts[0];

    // Check if it's an auth-related action
    $authActions = ['login', 'register', 'logout'];
    if (in_array($segment, $authActions, true)) {
        $controllerName = 'auth';
        $action = $segment;
    } else {
        // Treat as controller with default 'index' action
        $controllerName = $segment;
        $action = 'index';
    }
} else {
    // Multi-segment route: /controller/action
    $controllerName = $pathParts[0];
    $action = $pathParts[1] ?? 'index'; // Default action if none specified
}

// -------------------------
// Debug info (can be removed in production)
// -------------------------
echo "<!-- Debug: Requested path: /$path | Controller: $controllerName | Action: $action -->";

// -------------------------
// Load Controller
// -------------------------
$controllerFile = __DIR__ . '/controllers/' . ucfirst($controllerName) . 'Controller.php';

if (file_exists($controllerFile)) {
    include $controllerFile;

    $controllerClass = ucfirst($controllerName) . 'Controller';
    $controller = new $controllerClass($pdo);

    if (method_exists($controller, $action)) {
        // Call the requested action
        $controller->$action();
    } else {
        // Action not found → show 404
        http_response_code(404);
        include __DIR__ . '/views/error.php';
    }
} else {
    // Controller not found → show 404
    http_response_code(404);
    include __DIR__ . '/views/error.php';
}

// Flush output buffer
ob_end_flush();
