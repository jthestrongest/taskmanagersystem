<?php
@include 'config.php';
session_start();

if (!isset($_SESSION['user_name'])) {
    header('location:index.php');
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'] ?? 0;

// Fetch completed tasks for the user
$completed_tasks = "SELECT t.*, 
                   u.name as assigned_user_name,
                   a.name as assigned_by_name 
                   FROM tasks t 
                   LEFT JOIN user_form u ON t.assigned_to = u.id 
                   LEFT JOIN user_form a ON t.user_id = a.id 
                   WHERE (t.user_id = '$user_id' OR t.assigned_to = '$user_id')
                   AND t.status = 'completed' 
                   ORDER BY t.due_date DESC 
                   LIMIT 10";
$completed_tasks_result = mysqli_query($conn, $completed_tasks);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Dashboard</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="user_page.php" class="active">Dashboard</a>
                <a href="tasks.php">Task Management</a>
                <a href="logout.php" onclick="return confirmLogout();">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            </header>

            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>My Tasks</h3>
                    <p>Manage your daily tasks and stay organized</p>
                    <a href="tasks.php" class="card-button">View Tasks</a>
                </div>
            </div>

            <div class="users-list">
                <h2>My Completed Tasks</h2>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Task Title</th>
                            <th>Assigned To</th>
                            <th>Created By</th>
                            <th>Due Date</th>
                            <th>Priority</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($task = mysqli_fetch_assoc($completed_tasks_result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['title']); ?></td>
                                <td><?php echo htmlspecialchars($task['assigned_user_name'] ?? 'Unassigned'); ?></td>
                                <td><?php echo htmlspecialchars($task['assigned_by_name']); ?></td>
                                <td><?php echo $task['due_date']; ?></td>
                                <td>
                                    <span class="priority <?php echo $task['priority']; ?>">
                                        <?php echo ucfirst($task['priority']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                        
                    </tbody>
                </table>
                <div class="view-all">
                    <a href="tasks.php" class="card-button">View All Tasks</a>
                </div>
            </div>
        </main>
    </div>
    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to logout?');
        }
    </script>
</body>
</html>
