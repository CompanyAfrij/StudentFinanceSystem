<?php
include '../includes/config.php';

$query = "SELECT * FROM courses";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Courses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h2 {
            text-align: center;
            color: #800000;
            margin-bottom: 40px;
            font-size: 2rem;
            font-weight: 600;
            position: relative;
            padding-bottom: 15px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: #800000;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .card h3 {
            margin: 0 0 15px;
            color: #800000;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .card p {
            margin: 8px 0;
            color: #555;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .card .duration {
            font-weight: 500;
            color: #666;
            margin-top: 15px;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #800000;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-weight: 500;
            text-align: center;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .btn:hover {
            background: #6d0000;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 15px;
            }
            
            h2 {
                font-size: 1.6rem;
                margin-bottom: 30px;
            }
            
            .course-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Available Courses</h2>
    <div class="course-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
            <h3><?= htmlspecialchars($row['course_name']) ?></h3>
            <p><?= htmlspecialchars($row['description']) ?></p>
            <p class="duration">Duration: <?= htmlspecialchars($row['duration']) ?></p>
            <a href="course-details.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn">View Details</a>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>