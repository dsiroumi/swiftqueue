<?php
/**
 * api/views/dashboard.php
 *
 * Dashboard view with course management features: add/edit courses, sorting/filtering,
 * and a table displaying courses. Layout includes header/footer.
 *
 * Variables expected from controller:
 * - $message: array with 'type' and 'text' keys for flash messages
 * - $editCourse: array with course data when editing
 * - $courses: array of all courses
 * - $sort: current sort option
 * - $status: current filter status
 * - $userName: logged-in user name
 */

// Include the main layout (header, nav, etc.)
include 'layout.php';
?>

<main class="flex-grow container mx-auto p-4 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-md w-full">

        <!-- Flash message (success or error) -->
        <?php if (isset($message)): ?>
            <div class="p-4 mb-6 rounded-lg shadow-md <?= $message['type'] === 'success' ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-red-100 text-red-800 border border-red-300' ?>">
                <strong><?= $message['type'] === 'success' ? 'Success!' : 'Error!' ?></strong>
                <?= htmlspecialchars($message['text']) ?>
            </div>
        <?php endif; ?>

        

        <!-- Add/Edit Course Form -->
        <h2 class="text-2xl font-semibold mb-4 text-gray-800"><?= $editCourse ? 'Edit Course' : 'Add New Course' ?></h2>
        <form method="POST" action="/dashboard" class="mb-8 space-y-4 bg-white p-6 rounded-lg shadow-md">
            <!-- Hidden fields for action and course ID -->
            <input type="hidden" name="action" value="<?= $editCourse ? 'update' : 'create' ?>">
            <input type="hidden" name="id" value="<?= $editCourse['id'] ?? '' ?>">
        
            <!-- Course name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Course Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($editCourse['name'] ?? '') ?>" required
                       class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
            </div>
        
            <!-- Start date and time -->
            <div class="flex space-x-4">
                <div class="flex-1">
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($editCourse['start_date'] ?? '') ?>" required
                           class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex-1">
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                    <input type="time" id="start_time" name="start_time" value="<?= htmlspecialchars($editCourse['start_time'] ?? '') ?>" required
                           class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        
            <!-- End date and time -->
            <div class="flex space-x-4">
                <div class="flex-1">
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($editCourse['end_date'] ?? '') ?>" required
                           class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex-1">
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                    <input type="time" id="end_time" name="end_time" value="<?= htmlspecialchars($editCourse['end_time'] ?? '') ?>" required
                           class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        
            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status"
                        class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="active" <?= ($editCourse['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($editCourse['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
        
            <!-- Submit / Cancel -->
            <div class="flex items-center">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                    <?= $editCourse ? 'Update Course' : 'Add Course' ?>
                </button>
                <?php if ($editCourse): ?>
                    <a href="/dashboard" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Sort and Filter -->
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Courses</h2>
        <form method="GET" action="/dashboard" class="mb-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
            <!-- Sort by name or date -->
            <select name="sort" class="p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="a_z" <?= $sort === 'a_z' ? 'selected' : '' ?>>A-Z (Name)</option>
                <option value="z_a" <?= $sort === 'z_a' ? 'selected' : '' ?>>Z-A (Name)</option>
                <option value="date_desc" <?= $sort === 'date_desc' ? 'selected' : '' ?>>Date (Newest First)</option>
                <option value="date_asc" <?= $sort === 'date_asc' ? 'selected' : '' ?>>Date (Oldest First)</option>
            </select>

            <!-- Filter by status -->
            <select name="status" class="p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                <option value="" <?= empty($status) ? 'selected' : '' ?>>All Statuses</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>

            <!-- Apply filters -->
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition duration-200">Apply</button>
        </form>

        <!-- Courses Table -->
        <?php if (empty($courses)): ?>
            <p class="text-gray-600">No courses found.</p>
        <?php else: ?>
            <div class="overflow-x-auto shadow-md rounded-lg">
                <table class="w-full border-collapse bg-white">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="p-3 text-left">Name</th>
                            <th class="p-3 text-left">Start</th>
                            <th class="p-3 text-left">End</th>
                            <th class="p-3 text-left">Status</th>
                            <th class="p-3 text-left">Created At</th>
                            <th class="p-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $index => $course): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' ?> hover:bg-gray-100 transition duration-150">
                                <td class="p-3 border-t"><?= htmlspecialchars($course['name']) ?></td>
                                <td class="p-3 border-t"><?= htmlspecialchars($course['start_datetime']) ?></td>
                                <td class="p-3 border-t"><?= htmlspecialchars($course['end_datetime']) ?></td>
                                <td class="p-3 border-t"><?= htmlspecialchars($course['status']) ?></td>
                                <td class="p-3 border-t"><?= htmlspecialchars($course['created_at']) ?></td>
                                <td class="p-3 border-t flex space-x-2">
                                    <!-- Edit button -->
                                    <a href="/dashboard?edit_id=<?= $course['id'] ?>"
                                       class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 text-sm transition duration-200">
                                        Edit
                                    </a>

                                    <!-- Delete form -->
                                    <form method="POST" action="/dashboard" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $course['id'] ?>">
                                        <button type="submit"
                                                class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-sm transition duration-200">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</main>
