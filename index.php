<?php
require_once "db.php";
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($loginInput === '' || $password === '') {
        $message = "Please fill both fields.";
    } else {
        // Prepare query
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? OR username = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("ss", $loginInput, $loginInput);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($user_id, $hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user_id;
                    header("Location: portifoliowebsite.php");
                    exit;
                } else {
                    $message = "Invalid username/email or password.";
                }
            } else {
                $message = "Invalid username/email or password.";
            }

            $stmt->close();
        } else {
            $message = "Database error: " . $conn->error;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SkillSyncPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
            color: #fff;
        }

        canvas#particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }


        /* animated gradient bg */
        .tbackground {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(120deg, #00111f, #003366, #00111f);
            background-size: 600% 600%;
            animation: gradientBG 20s ease infinite;
            z-index: -3;
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

        /* particles */
        #tsparticles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
        }

        /* glass login card */
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            color: #fff;
            padding: 40px 20px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.1), 0 0 80px rgba(0, 255, 255, 0.05);
            animation: floatUp 4s ease-in-out infinite;
            position: relative;
            z-index: 1;

        }

        @keyframes floatUp {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .login-card h2 {
            font-weight: 700;
            margin-bottom: 20px;
            font-family: 'Algerian', 'Times New Roman', serif;
            font-size: 2rem;
            background: linear-gradient(90deg, #00f7ff, #ff00ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;

            border: 2px solid transparent;
            background: rgba(255, 255, 255, 0.05);
            padding: 10px 25px;
            font-size: 1rem;
        }

        .form-control:hover {
            background: rgba(255, 255, 255, 0.05);
            color: #fff;

            border: 2px solid #00f7ff;
            box-shadow: 0 0 10px #00f7ff, 0 0 20px #ff00ff;
        }

        .label {
            background: linear-gradient(90deg, #ff00fbff, #f2ff00ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-neon {
            background: linear-gradient(90deg, #00f7ff, #ff00ff);
            color: #000;
            font-weight: 700;
            border: none;
            border-radius: 30px;
            transition: 0.3s;
            width: 100%;
            padding: 12px 0;
            font-size: 1rem;
            box-shadow: 0 0 15px #00ffff88;
        }

        .btn-neon:hover {
            box-shadow: 0 0 20px #ff00ff88, 0 0 40px #00ffff88;
            background: linear-gradient(90deg, #ff00ff, #00ffff88);
            transform: scale(1.05) rotate(-1deg);
        }

        a {
            color: #00f7ff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .alert {
            background: rgba(255, 0, 0, 0.2);
            color: #ffbbbb;
            border: none;
        }

        @media(max-width: 500px) {
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <canvas id="particles"></canvas>
    <div class="tbackground"></div>
    <div id="tsparticles"></div>

    <div class="login-card">
        <h2>Login</h2>

        <?php if ($message): ?>
            <div class="alert alert-danger">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <label>Email/Username</label>
                <input type="text" name="email" class="form-control" placeholder="Enter Your Username/Email" required>
            </div>
            <div class="mb-3 text-start">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter Your Password" required>
            </div>
            <button type="submit" class="btn btn-neon mt-3">Login</button>
            <p class="mt-3">Don't have an account? <a href="register.php">Register</a></p>
        </form>
    </div>

    <script>
        tsParticles.load("tsparticles", {
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        area: 800
                    }
                },
                color: {
                    value: ["#00f7ff", "#ff00ff", "#ffffff"]
                },
                shape: {
                    type: "circle"
                },
                opacity: {
                    value: 0.3
                },
                size: {
                    value: {
                        min: 1,
                        max: 3
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
                    speed: 1.2,
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