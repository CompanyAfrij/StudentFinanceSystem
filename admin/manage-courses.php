<?php
session_start();
include '../includes/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "pages/login.php");
    exit();
}

$message = "";
$alertClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_course'])) {
    $course_id = $_POST['course_id'] ?? null;
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];

    if ($course_id) {
        $stmt = $conn->prepare("UPDATE courses SET course_name=?, description=?, duration=?, price=? WHERE id=?");
        $stmt->bind_param("sssdi", $course_name, $description, $duration, $price, $course_id);
        $stmt->execute();
        $message = "Course updated successfully.";
        $alertClass = "success";
    } else {
        $stmt = $conn->prepare("INSERT INTO courses (course_name, description, duration, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $course_name, $description, $duration, $price);
        $stmt->execute();
        $message = "Course added successfully.";
        $alertClass = "success";
    }
    $stmt->close();
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM courses WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Course deleted successfully.";
        $alertClass = "danger";
    } else {
        $message = "Error deleting course.";
        $alertClass = "danger";
    }
    $stmt->close();
}

$result = $conn->query("SELECT * FROM courses ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Manage Courses</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $alertClass ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#courseModal">Add New Course</button>
        <input type="text" id="searchInput" class="form-control w-50" placeholder="Search courses...">
    </div>

    <table class="table table-bordered table-hover" id="coursesTable">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Course Name</th>
            <th>Description</th>
            <th>Duration</th>
            <th>Price (LKR)</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['duration']) ?></td>
                <td><?= number_format($row['price'], 2) ?></td>
                <td>
                    <button class="btn btn-sm btn-info text-white" onclick='editCourse(<?= json_encode($row) ?>)'>Edit</button>
                    <a class="btn btn-sm btn-danger" href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Delete this course?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Modal for Add/Edit -->
    <div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseModalLabel">Add / Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="course_id" id="course_id">
                    <div class="mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="course_name" id="course_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration</label>
                        <input type="text" name="duration" id="duration" class="form-control" placeholder="e.g. 6 Months" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (LKR)</label>
                        <input type="number" name="price" id="price" class="form-control" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="save_course" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap & JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function editCourse(course) {
        document.getElementById('course_id').value = course.id;
        document.getElementById('course_name').value = course.course_name;
        document.getElementById('description').value = course.description;
        document.getElementById('duration').value = course.duration;
        document.getElementById('price').value = course.price;
        new bootstrap.Modal(document.getElementById('courseModal')).show();
    }

    // Simple search filter
    document.getElementById('searchInput').addEventListener('input', function () {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#coursesTable tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });
</script>
</body>
</html>
