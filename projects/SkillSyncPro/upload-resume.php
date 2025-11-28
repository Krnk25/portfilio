<?php
session_start();
require_once "db.php"; // make sure this file defines $conn

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if file uploaded
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {

        $file_name = $_FILES['resume']['name'];
        $file_tmp = $_FILES['resume']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // ✅ Allow more file types
        $allowed = ['pdf', 'doc', 'docx', 'txt', 'rtf'];
        if (!in_array($file_ext, $allowed)) {
            echo "Only PDF, DOC, DOCX, TXT or RTF files allowed.";
            exit;
        }

        // Create uploads folder if not exists
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move uploaded file
        $new_name = time() . "_" . $file_name;
        $file_path = $upload_dir . $new_name;
        move_uploaded_file($file_tmp, $file_path);

        // Extract text (basic — for txt read file contents, for others keep placeholder)
        $extracted_text = "";
        if ($file_ext === "txt") {
            $extracted_text = file_get_contents($file_path);
        } else {
            // For now placeholder; later integrate PDF/DOC extractor
            $extracted_text = "Extracted text from resume goes here...";
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO resumes (user_id, filename, extracted_text, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iss", $user_id, $new_name, $extracted_text);
        if ($stmt->execute()) {
            // ✅ redirect to dashboard after upload
            header("Location: dashboard.php?uploaded=success");
            exit;
        } else {
            echo "Database insert failed: " . $conn->error;
        }

    } else {
        echo "Please select a resume file to upload.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Resume - SkillSyncPro</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: #fff;
            background: linear-gradient(120deg, #00111f, #003366, #00111f);
            background-size: 600% 600%;
            animation: gradientBG 20s ease infinite;
            overflow-x: hidden;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        canvas#particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .container-box {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            padding: 20px;
        }

        h1 {
            font-size: 2.5rem;
            font-family: 'Algerian', 'Times New Roman', serif;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #ff00ff, #00f7ff, #00ff1a);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientMove 5s ease infinite;
            text-shadow: 0 0 8px #ff00ff, 0 0 15px #00f7ff;
        }

        @keyframes gradientMove {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .upload-box {
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.1), 0 0 80px rgba(0, 255, 255, 0.05);

            border-radius: 20px;
            padding: 60px;
            /* bigger */
            max-width: 500px;
            /* bigger */
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 2px solid #00f7ff;
            box-shadow: 0 0 10px #00f7ff, 0 0 20px #ff00ff;
        }

        .upload-box:hover {
            transform: scale(1.05);
            box-shadow: 0 0 40px rgba(0, 255, 255, 0.3), 0 0 90px rgba(255, 0, 255, 1);

        }

        input[type="file"] {
            display: block;
            margin: 20px auto;
            color: #fff;
            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.05);
            padding: 10px;
            border-radius: 8px;
            transition: border 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
            text-align: center;


        }

        input[type="file"]:hover {

            border: 2px solid #00f7ff;
            box-shadow: 0 0 10px #00f7ff, 0 0 20px #ff00ff;
        }

        .btn-back {
            text-decoration: none;
        }

        .btn-custom,
        .btn-back {
            background: linear-gradient(90deg, #ff00ff, #00f7ff, #00ff1a);
            background-size: 300% 300%;
            color: #fff;
            padding: 8px;
            border: none;
            border-radius: 8px;

            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
            animation: gradientMove 5s ease infinite;
            text-shadow: 0 0 8px #ff00ff, 0 0 15px #00f7ff;
            margin-top: 10px;
        }

        .btn-back:hover {
            box-shadow: 0 0 20px #00f7ff, 0 0 40px #ff00ff;
            transform: scale(1.08);

        }

        .btn-custom:hover {
            box-shadow: 0 0 20px #00f7ff, 0 0 40px #ff00ff;
            transform: scale(1.08);
        }

        .message {
            margin-top: 15px;
            font-size: 0.95rem;
        }

        @media(max-width:600px) {
            .upload-box {
                padding: 30px;
            }

            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <canvas id="particles"></canvas>
    <?php if (isset($_GET['uploaded']) && $_GET['uploaded'] === 'success'): ?>
        <div class="alert alert-success">✅ Resume uploaded successfully!</div>
    <?php endif; ?>

    <div class="container-box">
        <h1>Upload Your Resume</h1>
        <form class="upload-box" action="" method="POST" enctype="multipart/form-data">

            <input type="file" name="resume" accept=".pdf,.doc,.docx,.txt">
            <button type="submit" class="btn-custom">Upload Resume</button>
            <a href="dashboard.php" class="btn-back">⬅ Back to Dashboard</a>
            <?php if (!empty($error)): ?>
                <div class="message" style="color:#ffcccc;">
                    <?php echo $error; ?>
                </div>
            <?php elseif (!empty($success)): ?>
                <div class="message" style="color:#ccffcc;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <script>
        const canvas = document.getElementById('particles');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        class Particle {
            constructor(x, y, size, color, speedX, speedY) {
                this.x = x;
                this.y = y;
                this.size = size;
                this.color = color;
                this.speedX = speedX;
                this.speedY = speedY;
            }
            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
                if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
            }
            draw() {
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();
            }
        }

        let particlesArray = [];

        function initParticles() {
            particlesArray = [];
            for (let i = 0; i < 100; i++) {
                let size = Math.random() * 3 + 1;
                let x = Math.random() * canvas.width;
                let y = Math.random() * canvas.height;
                let colors = ["#ff00ff", "#00f7ff", "#ffffff"];
                let color = colors[Math.floor(Math.random() * colors.length)];
                let speedX = (Math.random() - 0.5);
                let speedY = (Math.random() - 0.5);
                particlesArray.push(new Particle(x, y, size, color, speedX, speedY));
            }
        }

        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particlesArray.forEach(p => {
                p.update();
                p.draw();
            });
            requestAnimationFrame(animateParticles);
        }

        initParticles();
        animateParticles();
        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            initParticles();
        });
    </script>
</body>

</html>