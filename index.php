<?php

// Database Connection
$host = 'localhost';
$dbname = 'task_management';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Notification Logic
$notification = "";

// Create Task
if (isset($_POST['create_task'])) {
    $title = htmlspecialchars($_POST['title']);
    $priority = htmlspecialchars($_POST['priority']);
    $deadline = htmlspecialchars($_POST['deadline']);

    try {
        $stmt = $pdo->prepare("INSERT INTO tasks (title, priority, deadline) VALUES (?, ?, ?)");
        if ($stmt->execute([$title, $priority, $deadline])) {
            $notification = "Task created successfully!";
        } else {
            $notification = "Failed to create task.";
        }
    } catch (Exception $e) {
        $notification = "Error: " . $e->getMessage();
    }
}

// Update Task
if (isset($_POST['update_task'])) {
    $id = intval($_POST['id']);
    $title = htmlspecialchars($_POST['title']);
    $priority = htmlspecialchars($_POST['priority']);
    $deadline = htmlspecialchars($_POST['deadline']);

    try {
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, priority = ?, deadline = ? WHERE id = ?");
        if ($stmt->execute([$title, $priority, $deadline, $id])) {
            $notification = "Task updated successfully!";
        } else {
            $notification = "Failed to update task.";
        }
    } catch (Exception $e) {
        $notification = "Error: " . $e->getMessage();
    }
}

// Delete Task
if (isset($_GET['delete_task'])) {
    $id = intval($_GET['delete_task']);

    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
        if ($stmt->execute([$id])) {
            $notification = "Task deleted successfully!";
        } else {
            $notification = "Failed to delete task.";
        }
    } catch (Exception $e) {
        $notification = "Error: " . $e->getMessage();
    }
}

// Fetch Tasks
try {
    $tasks = $pdo->query("SELECT * FROM tasks ORDER BY deadline ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error fetching tasks: " . $e->getMessage());
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-white min-h-screen flex flex-col items-center py-8">
    <h1 class="text-4xl font-bold mb-6">Task Management</h1>

    <?php if ($notification): ?>
        <div class="bg-green-500 text-white px-4 py-2 rounded mb-4 shadow-lg">
            <?php echo htmlspecialchars($notification); ?>
        </div>
    <?php endif; ?>

    <!-- Create Task Form -->
    <form method="POST" class="bg-gray-800 p-6 rounded-lg shadow-md w-full max-w-md">
        <h3 class="text-2xl font-semibold mb-4">Create Task</h3>
        <input type="text" name="title" placeholder="Task Title" required class="w-full p-2 mb-3 rounded bg-gray-700 text-white">
        <select name="priority" required class="w-full p-2 mb-3 rounded bg-gray-700 text-white">
            <option value="Low">Low</option>
            <option value="Medium">Medium</option>
            <option value="High">High</option>
        </select>
        <input type="date" name="deadline" required class="w-full p-2 mb-3 rounded bg-gray-700 text-white">
        <button type="submit" name="create_task" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">Create Task</button>
    </form>

    <!-- Task List -->
    <h3 class="text-2xl font-semibold mt-8">Task List</h3>
    <table class="w-full max-w-4xl bg-gray-800 rounded-lg shadow-md mt-4">
        <thead>
            <tr class="bg-gray-700 text-left">
                <th class="p-3">Title</th>
                <th class="p-3">Priority</th>
                <th class="p-3">Deadline</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr class="border-b border-gray-700">
                    <td class="p-3"><?php echo htmlspecialchars($task['title']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($task['priority']); ?></td>
                    <td class="p-3"><?php echo htmlspecialchars($task['deadline']); ?></td>
                    <td class="p-3">
                        <form method="POST" class="inline">
                            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                            <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required class="w-40 p-2 rounded bg-gray-700 text-white">
                            <select name="priority" class="w-24 p-2 rounded bg-gray-700 text-white">
                                <option value="Low" <?php if ($task['priority'] === 'Low') echo 'selected'; ?>>Low</option>
                                <option value="Medium" <?php if ($task['priority'] === 'Medium') echo 'selected'; ?>>Medium</option>
                                <option value="High" <?php if ($task['priority'] === 'High') echo 'selected'; ?>>High</option>
                            </select>
                            <input type="date" name="deadline" value="<?php echo htmlspecialchars($task['deadline']); ?>" required class="w-36 p-2 rounded bg-gray-700 text-white">
                            <button type="submit" name="update_task" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded">Update</button>
                        </form>
                        <a href="?delete_task=<?php echo $task['id']; ?>" class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded inline-block">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>

                
