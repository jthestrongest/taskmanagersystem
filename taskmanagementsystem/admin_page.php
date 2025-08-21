<?php
@include 'config.php';
session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:index.php');
}

// Fetch all users
$select_users = "SELECT * FROM user_form ORDER BY user_type, name";
$users_result = mysqli_query($conn, $select_users);

// Fetch completed tasks
$completed_tasks = "SELECT t.*, 
                   u.name as assigned_user_name,
                   a.name as assigned_by_name 
                   FROM tasks t 
                   LEFT JOIN user_form u ON t.assigned_to = u.id 
                   LEFT JOIN user_form a ON t.user_id = a.id 
                   WHERE t.status = 'completed' 
                   ORDER BY t.due_date DESC 
                   LIMIT 10";
$completed_tasks_result = mysqli_query($conn, $completed_tasks);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>Dashboard</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="admin_page.php" class="active">Dashboard</a>
                <a href="tasks.php">Task Management</a>
                <a href="register_form.php">Add User</a>
                <a href="logout.php" onclick="return confirmLogout();">Logout</a>
            </nav>
        </aside>

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></h1>
            </header>

            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h3>Task Management</h3>
                    <p>Manage and monitor all tasks</p>
                    <a href="tasks.php" class="card-button">View Tasks</a>
                </div>
                
                <div class="dashboard-card">
                    <h3>User Management</h3>
                    <p>Add and manage system users</p>
                    <a href="register_form.php" class="card-button">Add New User</a>
                </div>

            </div>

            <div class="users-list">
                <h2>Registered Users</h2>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>User Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($users_result)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($user['user_type'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="users-list">
                <h2>Recently Completed Tasks</h2>
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
