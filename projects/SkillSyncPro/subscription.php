<?php
session_start();

// (Optional) User login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Plans</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/particles.js"></script>
    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1600&q=80');

        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        color: #fff;
    }

    /* Particle background container */
    #particles-js {
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 0;
        top: 0;
        left: 0;
    }

    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 1;
    }

    .container {
        position: relative;
        z-index: 2;
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px;
        text-align: center;
    }

    h1 {
        font-size: 2.8rem;
        background: linear-gradient(90deg, #a2ff00ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    p.sub-text {
        font-size: 1rem;
        margin-bottom: 50px;
        color: #e0e0e0;
    }

    .plans {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    /* --- Plan Cards --- */
    .plan-card {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(18px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.25);
        transition: transform 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease;
        position: relative;
        overflow: hidden;
        align-items: center;
    }

    .plan-card::after {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at top left, rgba(255, 255, 255, 0.05), transparent 70%);
        transform: rotate(25deg);
        pointer-events: none;
    }

    .plan-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .plan-title {
        font-size: 1.8rem;
        margin-bottom: 10px;
        font-weight: 600;
        background: linear-gradient(90deg, #ff00ff, #a2ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .plan-price {
        font-size: 2.3rem;
        font-weight: bold;
        margin-bottom: 25px;
        color: #fff;
    }

    .plan-features {
        list-style: none;
        padding: 0;
        margin: 0 0 25px 0;
        color: #f1f1f1;
        font-size: 1rem;
    }

    .plan-features li {
        margin: 12px 0;
    }

    /* --- Neon Button --- */
    .btn-subscribe {
        display: inline-block;
        background: linear-gradient(135deg, #00c6ff, #0072ff);
        color: #fff;
        padding: 14px 28px;
        border-radius: 40px;
        font-weight: 600;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 0 15px rgba(0, 198, 255, 0.6), 0 0 30px rgba(0, 114, 255, 0.4);
        transition: all 0.3s ease;
    }

    .btn-subscribe::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: all 0.4s ease;
    }

    .btn-subscribe:hover::before {
        left: 100%;
    }

    .btn-subscribe:hover {
        background: linear-gradient(135deg, #0072ff, #00c6ff);
        transform: scale(1.05);
        box-shadow: 0 0 25px rgba(0, 198, 255, 0.8), 0 0 50px rgba(0, 114, 255, 0.6);
    }

    .btn-neon {
        background: transparent;
        font-weight: 700;
        border: 1px solid #fff;
        color: #fff;
        transition: 0.3s;
        text-decoration: none;
        padding: 15px 40px;
        margin-top: 30px;
        font-size: 1rem;
        box-shadow: 0 0 15px #00ffff88;
        display: inline-block;
    }

    .btn-neon:hover {
        background: #00ffff33;
    }

    /* Dark mode styles */
    body.dark .plan-card {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
    }

    /* Dark mode toggle button */
    .dark-toggle {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10;
        background: #000000ff;
        border: 1px solid #00ffffaa;
        color: #fff;
        padding: 10px 15px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 0.9rem;
        backdrop-filter: blur(5px);
        box-shadow: 0 0 10px #00ffff88;
    }

    @media(max-width:600px) {
        h1 {
            font-size: 2rem;
        }

        .plan-price {
            font-size: 1.8rem;
        }
    }
    </style>
</head>

<body>
    <!-- particles background -->
    <div id="particles-js"></div>

    <!-- dark mode toggle -->
    <button class="dark-toggle" onclick="toggleDarkMode()">🌙 Dark Mode</button>

    <div class="container">
        <h1>Choose Your Subscription Plan</h1>
        <p class="sub-text">Pick the plan that best suits your needs and start enjoying premium features!</p>

        <div class="plans">
            <!-- Basic Plan -->
            <div class="plan-card">
                <div class="plan-title">Basic Plan!</div>
                <div class="plan-price">₹199 / month</div>
                <ul class="plan-features">
                    <li>Access to basic features</li>
                    <li>Standard Support</li>
                    <li>Limited Downloads</li>
                </ul>
                <a href="dashboard.php?plan=basic" class="btn-subscribe">Subscribe</a>
            </div>

            <!-- Standard Plan -->
            <div class="plan-card">
                <div class="plan-title">Standard Plan!</div>
                <div class="plan-price">₹499 / month</div>
                <ul class="plan-features">
                    <li>All Basic features</li>
                    <li>Priority Support</li>
                    <li>Unlimited Downloads</li>
                </ul>
                <a href="dashboard.php?plan=standard" class="btn-subscribe">Subscribe</a>
            </div>

            <!-- Premium Plan -->
            <div class="plan-card">
                <div class="plan-title">Premium Plan!</div>
                <div class="plan-price">₹999 / month</div>
                <ul class="plan-features">
                    <li>All Standard features</li>
                    <li>24/7 Dedicated Support</li>
                    <li>Exclusive Content</li>
                </ul>
                <a href="dashboard.php?plan=premium" class="btn-subscribe">Subscribe</a>
            </div>
        </div>
        <a href="dashboard.php" class="btn-neon">⬅ Back To Dashboard</a>
    </div>

    <script>
    particlesJS("particles-js", {
        "particles": {
            "number": {
                "value": 90
            },
            "color": {
                "value": ["#00ffff", "#ff00ff", "#00ff00", "#ffff00"]
            },
            "shape": {
                "type": "circle"
            },
            "opacity": {
                "value": 0.6
            },
            "size": {
                "value": 3,
                "random": true
            },
            "line_linked": {
                "enable": true,
                "distance": 150,
                "color": "#ffffff",
                "opacity": 0.2,
                "width": 1
            },
            "move": {
                "enable": true,
                "speed": 2,
                "direction": "none",
                "out_mode": "out"
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {
                    "enable": true,
                    "mode": "repulse"
                },
                "onclick": {
                    "enable": true,
                    "mode": "push"
                }
            },
            "modes": {
                "repulse": {
                    "distance": 100
                },
                "push": {
                    "particles_nb": 4
                }
            }
        },
        "retina_detect": true
    });

    function toggleDarkMode() {
        document.body.classList.toggle('dark');
    }
    </script>
</body>

</html>