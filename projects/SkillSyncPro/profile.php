<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<h2 style='text-align:center;margin-top:50px;color:#fff;'>User not found!</h2>";
    exit;
}

// Handle profile update
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $gender = trim($_POST['gender']);
    $age = trim($_POST['age']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    $update = $conn->prepare("UPDATE users SET name=?, gender=?, age=?, mobile=?, email=?, address=? WHERE id=?");
    $update->bind_param("ssisssi", $name, $gender, $age, $mobile, $email, $address, $user_id);
    if ($update->execute()) {
        $message = "Profile updated successfully!";
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $message = "Error updating profile: " . $conn->error;
    }
    $update->close();
}

// Photo path
$photoPath = $user['photo'] ? 'uploads/' . htmlspecialchars($user['photo']) : 'default-avatar.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - SkillSyncPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <style>
    body {
        margin: 0;
        font-family: 'Roboto', sans-serif;
        min-height: 100vh;
        background: linear-gradient(120deg, #0f2027, #203a43, #2c5364);
        color: #fff;
        overflow-x: hidden;
        position: relative;
    }

    #tsparticles {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 0;
    }

    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 30px;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 2;
    }

    header nav a {
        margin-right: 20px;
        text-decoration: none;
        color: #fff;
        font-weight: 500;
        transition: 0.3s;
    }

    header nav a.active {
        font-weight: 700;
    }

    header nav a:hover {
        color: #0ff;
    }

    header a.logout {
        color: #fff;
        text-decoration: none;
        font-weight: 500;
    }

    header a.logout:hover {
        color: #f0f;
    }

    .profile-wrapper {
        position: relative;
        max-width: 900px;
        margin: 60px auto;
        padding: 20px;
        background: rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(35px);
        border-radius: 25px;
        box-shadow: 0 10px 50px rgba(0, 255, 255, 0.15), 0 0 80px rgba(255, 0, 255, 0.1);

        z-index: 1;
        animation: fadeInUp 1s ease forwards;
    }

    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-bottom: 35px;
        position: relative;
    }

    .avatar-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        padding: 6px;
    }

    .avatar-wrapper::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: conic-gradient(#00f7ff, #ff00ff, #00ffb3, #ff007f, #00f7ff);
        z-index: -1;
        animation: rotateBorder 12s linear infinite;
        box-shadow: 0 0 20px #00ffff66, 0 0 40px #ff00ff66 inset;
    }

    @keyframes rotateBorder {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .avatar-wrapper img.avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        display: block;
        border: 5px solid rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 1;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
    }

    .avatar-wrapper .upload-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #6a82fb;
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 0 10px #0ff, 0 0 20px #f0f;
        transition: 0.3s;
    }

    .avatar-wrapper .upload-icon:hover {
        transform: scale(1.2);
        box-shadow: 0 0 25px #0ff, 0 0 50px #f0f;
    }

    .profile-header h1 {
        margin: 0;
        font-size: 34px;
        font-weight: 700;
        background: linear-gradient(90deg, #00f7ff, #ff00ff, #00ffff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientText 3s linear infinite alternate;
    }

    @keyframes gradientText {
        0% {
            background-position: 0%;
        }

        100% {
            background-position: 100%;
        }
    }

    .profile-header .username {
        font-size: 16px;
        color: #aaa;
        margin-top: 6px;
    }

    h2.section-title {
        font-size: 24px;
        margin-top: 35px;
        margin-bottom: 18px;
        background: linear-gradient(90deg, #ff00ff, #00ffff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientText 4s ease infinite alternate;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 150px 1fr;
        row-gap: 14px;
        margin-top: 12px;
    }

    .info-grid div {
        font-size: 15px;
        color: #eee;
        transition: 0.3s;
    }

    .info-grid div:first-child {
        color: #999;
    }

    .info-grid div:nth-child(even):hover {
        color: #0ff;
        font-weight: 600;
    }

    .edit-btn {
        display: inline-block;
        margin-top: 28px;
        padding: 14px 28px;
        background: linear-gradient(90deg, #ff00ff, #00ffff, #ff00ff);
        color: #000;
        font-weight: 600;
        border-radius: 35px;
        text-decoration: none;
        box-shadow: 0 0 20px #ff00ff88, 0 0 35px #00ffff88;
        transition: 0.3s;
        cursor: pointer;
    }

    .edit-btn:hover {
        transform: scale(1.05) rotate(-1deg);
        box-shadow: 0 0 35px #00ffffaa, 0 0 60px #ff00ffaa;
    }

    .edit-form {
        display: none;
        margin-top: 25px;
        animation: fadeInUp 0.5s ease forwards;
    }

    .edit-form input,
    .edit-form select {
        width: 100%;
        padding: 12px;
        margin-bottom: 14px;
        border-radius: 10px;
        border: none;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-weight: 500;
        transition: 0.3s;
    }

    .edit-form input:focus,
    .edit-form select:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 15px #0ff, 0 0 25px #f0f inset;
    }

    .edit-form button {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 35px;
        font-weight: 600;
        background: linear-gradient(90deg, #ff00ff, #00ffff, #ff00ff);
        color: #000;
        cursor: pointer;
        transition: 0.3s;
    }

    .edit-form button:hover {
        transform: scale(1.05);
        box-shadow: 0 0 35px #00ffff88, 0 0 50px #ff00ff88;
    }

    @media(max-width:600px) {
        .profile-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .avatar-wrapper {
            margin-bottom: 12px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .info-grid div:first-child {
            font-weight: 500;
        }
    }
    </style>
</head>

<body>
    <div id="tsparticles"></div>
    <header>
        <nav>
            <a href="dashboard.php">⬅ Back to Dashboard</a>
            <a href="#" class="active">Profile</a>
        </nav>
        <a href="logout.php" class="logout">Logout</a>
    </header>

    <div class="profile-wrapper">
        <div class="profile-header">
            <div class="avatar-wrapper">
                <img src="<?php echo $photoPath; ?>" class="avatar" alt="Profile Photo">
                <label for="photo-upload" class="upload-icon">&#128247;</label>
                <form action="upload-photo.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="photo" id="photo-upload" onchange="this.form.submit()" hidden>
                </form>
            </div>
            <div>
                <h1><?php echo htmlspecialchars($user['name']); ?></h1>
                <div class="username">@<?php echo htmlspecialchars($user['username']); ?></div>
            </div>
        </div>

        <h2 class="section-title">Basic Information</h2>
        <div class="info-grid view-mode">
            <div>Gender</div>
            <div><?php echo htmlspecialchars($user['gender']); ?></div>
            <div>Age</div>
            <div><?php echo htmlspecialchars($user['age']); ?></div>
            <div>Degree</div>
            <div><?php echo htmlspecialchars($user['degree']); ?></div>
        </div>

        <h2 class="section-title">Contact Information</h2>
        <div class="info-grid view-mode">
            <div>Phone</div>
            <div><?php echo htmlspecialchars($user['mobile']); ?></div>
            <div>Email</div>
            <div><?php echo htmlspecialchars($user['email']); ?></div>
            <div>Address</div>
            <div><?php echo htmlspecialchars($user['address']); ?></div>
        </div>

        <?php if ($message): ?>
        <div style="margin-top:12px;color:#0ff;font-weight:600;"><?php echo $message; ?></div>
        <?php endif; ?>

        <button class="edit-btn" onclick="toggleEdit()">Edit Profile</button>

        <form class="edit-form" method="POST">
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?php if ($user['gender'] == 'Male')
                    echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($user['gender'] == 'Female')
                    echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($user['gender'] == 'Other')
                    echo 'selected'; ?>>Other</option>
            </select>
            <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required>
            <input type="text" name="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            <button type="submit" name="update_profile">Save Changes</button>
        </form>
    </div>

    <script>
    function toggleEdit() {
        const form = document.querySelector('.edit-form');
        form.style.display = form.style.display === 'block' ? 'none' : 'block';
    }

    tsParticles.load("tsparticles", {
        particles: {
            number: {
                value: 100,
                density: {
                    enable: true,
                    area: 900
                }
            },
            color: {
                value: ["#00f7ff", "#ff00ff", "#ffffff"]
            },
            shape: {
                type: "circle"
            },
            opacity: {
                value: 0.25
            },
            size: {
                value: {
                    min: 1,
                    max: 4
                }
            },
            links: {
                enable: true,
                distance: 120,
                color: "#ffffff",
                opacity: 0.15,
                width: 1
            },
            move: {
                enable: true,
                speed: 1.5,
                outModes: {
                    default: "out"
                }
            }
        },
        interactivity: {
            events: {
                resize: true
            }
        },
        detectRetina: true
    });
    </script>
</body>

</html>