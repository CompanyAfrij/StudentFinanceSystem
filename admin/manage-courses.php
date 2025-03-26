<?php
session_start();
include '../includes/config.php'; // Include config.php instead of database.php

// Ensure only admin access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}

// Handle course addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $query = "INSERT INTO courses (course_name, description, price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssd", $course_name, $description, $price);
    
    if ($stmt->execute()) {
        echo "<script>alert('Course added successfully.'); window.location.href='manage-courses.php';</script>";
    } else {
        echo "<script>alert('Error adding course.');</script>";
    }
    $stmt->close();
}

// Handle course deletion
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM courses WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: manage-courses.php");
        exit();
    } else {
        echo "<script>alert('Error deleting course.');</script>";
    }
    $stmt->close();
}

// Fetch all courses
$query = "SELECT * FROM courses";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #800000;
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
            width: 100%;
        }
        button {
            background-color: #800000;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background-color: #5a0000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #800000;
            color: white;
        }
        .action-links a {
            margin-right: 10px;
            text-decoration: none;
            padding: 5px 10px;
            color: white;
            border-radius: 5px;
            transition: 0.3s;
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

    <!-- Course Add Form -->
    <h3>Add New Course</h3>
    <form method="POST">
        <input type="text" name="course_name" placeholder="Course Name" required>
        <textarea name="description" placeholder="Course Description" required></textarea>
        <input type="number" name="price" step="0.01" min="0" placeholder="Price" required>
        <button type="submit" name="add_course">Add Course</button>
    </form>

    <h3>Existing Courses</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Course Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['course_name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td class="action-links">
                <a class="edit-link" href="manage-courses.php?edit_id=<?= $row['id'] ?>">Edit</a>
                <a class="delete-link" href="manage-courses.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
