<?php
session_start();
require_once "php/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resume'])) {
    $file = $_FILES['resume'];

    // Check file type
    $allowed = ['pdf', 'docx'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        $message = "Only PDF or DOCX files allowed.";
    } else {
        // Upload path
        $new_name = 'resume_' . $user_id . '_' . time() . '.' . $ext;
        $upload_path = __DIR__ . '/uploads/' . $new_name;

        if (!is_dir(__DIR__ . '/uploads/')) {
            mkdir(__DIR__ . '/uploads/', 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Save path in users table
            $stmt = $conn->prepare("UPDATE users SET resume_path = ? WHERE id = ?");
            $stmt->bind_param("si", $new_name, $user_id);
            $stmt->execute();
            $stmt->close();

            $message = "Resume uploaded successfully!";
        } else {
            $message = "Failed to upload file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Resume - SkillSyncPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: linear-gradient(135deg, #4e54c8, #8f94fb);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Poppins', sans-serif;
    }

    .upload-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        border-radius: 20px;
        padding: 40px;
        color: #fff;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        animation: fadeIn 1s ease-in-out;
    }

    h2 {
        color: #fff;
        text-align: center;
        margin-bottom: 20px;
    }

    .form-control {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: #fff;
    }

    .form-control:focus {
        background: rgba(255, 255, 255, 0.3);
        color: #fff;
        box-shadow: none;
    }

    .btn-neon {
        background: #ffdd57;
        color: #000;
        font-weight: 600;
        border: none;
        border-radius: 30px;
        transition: 0.3s;
        width: 100%;
    }

    .btn-neon:hover {
        background: #ffe97f;
        box-shadow: 0 0 15px #ffdd57, 0 0 30px #ffdd57;
        transform: scale(1.05);
    }

    a {
        color: #ffdd57;
    }

    @keyframes fadeIn {
        0% {
            opacity: 0;
            transform: translateY(-20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body>
    <div class="upload-card">
        <h2>Upload Your Resume</h2>
        <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Select Resume (PDF/DOCX)</label>
                <input type="file" name="resume" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-neon">Upload Resume</button>
            <p class="mt-3 text-center"><a href="dashboard.php">Back to Dashboard</a></p>
        </form>
    </div>
</body>

</html>