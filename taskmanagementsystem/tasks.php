<?php
@include 'config.php';
session_start();

if(!isset($_SESSION['user_name']) && !isset($_SESSION['admin_name'])){
    header('location:index.php');
    exit();
}

// Handle file upload for task cards
if(isset($_FILES['task_file']) && isset($_POST['task_id'])) {
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    
    if($_FILES['task_file']['error'] == 0) {
        $upload_dir = 'task_attachments/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $original_filename = $_FILES['task_file']['name'];
        $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if(move_uploaded_file($_FILES['task_file']['tmp_name'], $upload_path)) {
            
            $original_filename = mysqli_real_escape_string($conn, $original_filename);
            $upload_insert = "INSERT INTO task_uploads (task_id, filename, original_filename, uploaded_by) 
                            VALUES ('$task_id', '$new_filename', '$original_filename', '$user_id')";
            mysqli_query($conn, $upload_insert);
            
           
            $update = "UPDATE tasks SET attachment = '$new_filename' WHERE id = '$task_id'";
            mysqli_query($conn, $update);
            header('location: tasks.php');
            exit();
        }
    }
}

// Get all users for assignment dropdown
$users_query = "SELECT id, name FROM user_form WHERE user_type = 'user'";
$users_result = mysqli_query($conn, $users_query);
$users = [];
while($user = mysqli_fetch_assoc($users_result)) {
    $users[$user['id']] = $user['name'];
}

// Handle task assignment
if(isset($_POST['assign_task'])) {
    $task_id = mysqli_real_escape_string($conn, $_POST['task_id']);
    $assigned_to = mysqli_real_escape_string($conn, $_POST['assigned_to']);
    
    $update = "UPDATE tasks SET assigned_to = '$assigned_to' WHERE id = '$task_id'";
    mysqli_query($conn, $update);
    header('location: tasks.php');
}


$user_id = $_SESSION['user_id'] ?? 0; 

// Handle task operations
if(isset($_POST['add_task'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $assigned_to = isset($_POST['assigned_to']) ? mysqli_real_escape_string($conn, $_POST['assigned_to']) : null;
    
    // Handle file upload
    $attachment = '';
    if(isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $upload_dir = 'task_attachments/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $original_filename = $_FILES['attachment']['name'];
        $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if(move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
            $attachment = $new_filename;
        }
    }

    $insert = "INSERT INTO tasks (user_id, title, description, due_date, priority, attachment, assigned_to) 
               VALUES ('$user_id', '$title', '$description', '$due_date', '$priority', '$attachment', " . ($assigned_to ? "'$assigned_to'" : "NULL") . ")";
    mysqli_query($conn, $insert);
}

// Delete task
if(isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM tasks WHERE id = $id");
    header('location: tasks.php');
}

// Update task status
if(isset($_GET['complete'])) {
    $id = $_GET['complete'];
    mysqli_query($conn, "UPDATE tasks SET status = 'completed' WHERE id = $id");
    header('location: tasks.php');
}

// Handle task completion with attachment
if(isset($_POST['complete_task'])) {
    $task_id = mysqli_real_escape_string($conn, $_POST['complete_task']);
    
    // Update task status
    mysqli_query($conn, "UPDATE tasks SET status = 'completed' WHERE id = '$task_id'");
    
    // Handle completion attachment if provided
    if(isset($_FILES['completion_file']) && $_FILES['completion_file']['error'] == 0) {
        $upload_dir = 'task_attachments/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $original_filename = $_FILES['completion_file']['name'];
        $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_completion.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if(move_uploaded_file($_FILES['completion_file']['tmp_name'], $upload_path)) {
            $update = "UPDATE tasks SET completion_attachment = '$new_filename' WHERE id = '$task_id'";
            mysqli_query($conn, $update);
        }
    }
    
    header('location: tasks.php');
    exit();
}
// assignment information and assigner name
$user_id = $_SESSION['user_id'] ?? 0;
if(isset($_SESSION['admin_name'])) {
    // Admins can see all tasks
    $select = "SELECT t.*, 
               u.name as assigned_user_name,
               CASE 
                   WHEN a.user_type = 'admin' THEN CONCAT('Admin: ', a.name)
                   ELSE a.name 
               END as assigned_by_name 
               FROM tasks t 
               LEFT JOIN user_form u ON t.assigned_to = u.id 
               LEFT JOIN user_form a ON t.user_id = a.id 
               ORDER BY due_date ASC";
} else {
    // Users can only see their created or assigned tasks
    $select = "SELECT t.*, 
               u.name as assigned_user_name,
               CASE 
                   WHEN a.user_type = 'admin' THEN CONCAT('Admin: ', a.name)
                   ELSE a.name 
               END as assigned_by_name 
               FROM tasks t 
               LEFT JOIN user_form u ON t.assigned_to = u.id 
               LEFT JOIN user_form a ON t.user_id = a.id 
               WHERE t.user_id = '$user_id' OR t.assigned_to = '$user_id' 
               ORDER BY due_date ASC";
}
$result = mysqli_query($conn, $select);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management</title>
    <link rel="stylesheet" href="tasks.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Task Management</h1>
            <nav>
                <a href="<?php echo isset($_SESSION['admin_name']) ? 'admin_page.php' : 'user_page.php'; ?>">Back to Dashboard</a>
               
            </nav>
        </header>

        <?php if(isset($_SESSION['admin_name'])) { ?>
        <div class="add-task-form">
            <h2>Add New Task</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="title" required placeholder="Task Title">
                </div>
                <div class="form-group">
                    <textarea name="description" placeholder="Task Description"></textarea>
                </div>
                <div class="assign-section">
                    <div class="assign-group">
                        <label class="assign-label">Assign To</label>
                        <select name="assigned_to" class="assign-select">
                            <option value="">Select User</option>
                            <?php foreach($users as $id => $name) { ?>
                                <option value="<?php echo $id; ?>">
                                    <?php echo htmlspecialchars($name); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="assign-group">
                        <label class="assign-label">Due Date</label>
                        <input type="date" name="due_date" required>
                    </div>
                </div>
                <div class="form-group">
                    <select name="priority" required>
                        <option value="low">Low Priority</option>
                        <option value="medium" selected>Medium Priority</option>
                        <option value="high">High Priority</option>
                    </select>
                </div>
                <div class="form-group">
                    <input type="file" name="attachment" class="file-input">
                </div>
                <button type="submit" name="add_task">Add Task</button>
            </form>
        </div>
        <?php } ?>

        <div class="tasks-list">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <div class="task-card <?php echo $row['status']; ?>">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <div class="task-meta">
                        <span class="priority <?php echo $row['priority']; ?>">
                            <?php echo ucfirst($row['priority']); ?>
                        </span>
                        <span class="due-date">Due: <?php echo $row['due_date']; ?></span>
                        <span class="assigned-to">
                            Assigned to: <?php echo $row['assigned_user_name'] ? htmlspecialchars($row['assigned_user_name']) : 'Unassigned'; ?>
                        </span>
                        <span class="assigned-by">
                            Created by: <?php echo htmlspecialchars($row['assigned_by_name']); ?>
                        </span>
                        <?php if(!empty($row['attachment'])) { ?>
                            <span class="attachment">
                                <a href="task_attachments/<?php echo htmlspecialchars($row['attachment']); ?>" 
                                   target="_blank" 
                                   class="btn-view-attachment">
                                    View Task Attachment
                                </a>
                            </span>
                        <?php } ?>
                        <?php if(!empty($row['completion_attachment'])) { ?>
                            <span class="attachment">
                                <a href="task_attachments/<?php echo htmlspecialchars($row['completion_attachment']); ?>" 
                                   target="_blank" 
                                   class="btn-view-attachment completion-attachment">
                                    View Completion Attachment
                                </a>
                            </span>
                        <?php } ?>
                    </div>
                    <div class="task-actions">
                        <?php if($row['status'] != 'completed') { ?>
                            <form action="tasks.php" method="post" enctype="multipart/form-data" class="complete-form">
                                <input type="hidden" name="complete_task" value="<?php echo $row['id']; ?>">
                                <div class="complete-upload-group">
                                    <input type="file" name="completion_file" id="completion-<?php echo $row['id']; ?>" class="completion-file">
                                    <button type="submit" class="btn-complete">Complete Task</button>
                                </div>
                            </form>
                        <?php } ?>
                        <a href="tasks.php?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>
