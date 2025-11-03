<b>Swiftqueue Course Manager</b>

This is a full-stack web application for managing courses at the Swiftqueue School of High Tech. It allows authenticated users to view, filter, and perform CRUD operations on courses stored in a database, improving resource management efficiency.

This project was built as part of a developer test to demonstrate full-stack skills in backend (PHP), frontend, and databases.

<b>Features</b>
<ul>
    <li><b>Authentication:</b> Login/logout functionality;  user registration</li>
    <li><b>Courses Dashboard:</b> Displays all courses from the database on load.</li>
    <li><b>Filtering:</b> Filter courses by status (Active, Inactive).</li>
    <li><b>Sorting:</b> Sorting by name or date.</li>
    <li><b>CRUD Operations:</b> Create, Read, Update, Delete courses.</li>
    <li><b>Course Attributes:</b> Name, Start/End datetime, Status.</li>
    <li><b>Client-side Validation:</b> Uses <code>Validate.js</code> for registration form validation before submission.</li>
    <li><b>reCAPTCHA Integration:</b> Uses Google reCAPTCHA v3 to protect forms from bots.</li>
</ul>

<b>Technologies Used</b>
<ul>
    <li><b>Backend:</b> PHP, PDO for database access.</li>
    <li><b>Frontend:</b> HTML5, Tailwind CSS</li>
    <li><b>Database:</b> MySQL.</li>
    <li><b>Security:</b> Session-based auth, PDO prepared statements.</li>
</ul>

<b>Setup Instructions</b>
<ol>
    <li><b>Clone the repository:</b> </li>
    <li><b>Set up the database:</b>
        <pre><code>CREATE DATABASE swiftqueue_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE swiftqueue_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    school VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO users (username, password) VALUES ('admin', '$2y$10$examplehashedpassword');</code></pre>
    </li>
    <li><b>Configure database connection:</b>
  
<b>Usage</b>
<ul>
    <li><b>Login:</b> /login</li>
    <li><b>Register:</b> /register</li>
    <li><b>Dashboard:</b> /dashboard (view/add/edit/delete courses)</li>
    <li><b>Logout:</b> /logout</li>
</ul>

<b>Contributing</b>
<ul>
    <li>Fork the repository, create a feature branch, commit your changes, push to your branch, and submit a pull request.</li>
</ul>

