<?php
session_start();
include '../includes/config.php'; // Make sure config.php defines BASE_URL and $conn

// Redirect non-admin users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}

// Handle flash messages
$message = "";
$alertClass = "";

// Handle course addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $query = "INSERT INTO courses (course_name, description, price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssd", $course_name, $description, $price);
    
    if ($stmt->execute()) {
        $message = "Course added successfully.";
        $alertClass = "success";
    } else {
        $message = "Error adding course.";
        $alertClass = "error";
    }
    $stmt->close();
}

// Handle course update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_course'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $query = "UPDATE courses SET course_name=?, description=?, price=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdi", $course_name, $description, $price, $course_id);
    
    if ($stmt->execute()) {
        $message = "Course updated successfully.";
        $alertClass = "success";
    } else {
        $message = "Error updating course.";
        $alertClass = "error";
    }
    $stmt->close();
}

// Handle course deletion
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM courses WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: manage-courses.php?msg=deleted");
        exit();
    } else {
        $message = "Error deleting course.";
        $alertClass = "error";
    }
    $stmt->close();
}

// Check if editing
$edit_mode = false;
$edit_course = null;

if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_course = $result->fetch_assoc();
    $stmt->close();

    if ($edit_course) {
        $edit_mode = true;
    }
}

// Fetch all courses
$result = $conn->query("SELECT * FROM courses");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2, h3 {
            color: #800000;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        input, textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #800000;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #5a0000;
        }
        .cancel-btn {
            background-color: gray;
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #800000;
            color: white;
        }
        .action-links a {
            padding: 5px 10px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 5px;
        }
        .edit-link {
            background-color: #007bff;
        }
        .edit-link:hover {
            background-color: #0056b3;
        }
        .delete-link {
            background-color: #dc3545;
        }
        .delete-link:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Manage Courses</h2>

    <!-- Flash Messages -->
    <?php if (!empty($message)): ?>
        <div class="alert <?= $alertClass ?>"><?= $message ?></div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert success">Course deleted successfully.</div>
    <?php endif; ?>

    <!-- Course Form -->
    <?php if ($edit_mode): ?>
        <h3>Edit Course</h3>
        <form method="POST">
            <input type="hidden" name="course_id" value="<?= $edit_course['id'] ?>">
            <input type="text" name="course_name" value="<?= htmlspecialchars($edit_course['course_name']) ?>" required>
            <textarea name="description" required><?= htmlspecialchars($edit_course['description']) ?></textarea>
            <input type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars($edit_course['price']) ?>" required>
            <div style="display:flex;">
                <button type="submit" name="update_course">Update Course</button>
                <a href="manage-courses.php"><button type="button" class="cancel-btn">Cancel</button></a>
            </div>
        </form>
    <?php else: ?>
        <h3>Add New Course</h3>
        <form method="POST">
            <input type="text" name="course_name" placeholder="Course Name" required>
            <textarea name="description" placeholder="Course Description" required></textarea>
            <input type="number" name="price" step="0.01" min="0" placeholder="Price" required>
            <button type="submit" name="add_course">Add Course</button>
        </form>
    <?php endif; ?>

    <!-- Courses Table -->
    <h3>Existing Courses</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Description</th>
                <th>Price (LKR)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td class="action-links">
                        <a class="edit-link" href="manage-courses.php?edit_id=<?= $row['id'] ?>">Edit</a>
                        <a class="delete-link" href="manage-courses.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
