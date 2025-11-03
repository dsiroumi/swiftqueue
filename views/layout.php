<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swiftqueue Course Manager</title>

    <!-- Tailwind CSS CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

    <!-- Header Section -->
    <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
        <!-- Application title -->
        <h1 class="text-2xl font-bold">Swiftqueue Course Management Application</h1>

        <!-- Logout link, only visible if user is logged in --> 
        <?php if (isset($_SESSION['user_id'])): ?> <a href="/logout" class="text-white hover:underline">Logout</a>
        <?php endif; ?>
    </header>
</body>
</html>
