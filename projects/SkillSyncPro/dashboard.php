<?php
session_start();
require_once "db.php";

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch logged-in user's name
$user_stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_stmt->bind_result($user_name);
$user_stmt->fetch();
$user_stmt->close();

// ✅ Fetch last uploaded resume text
$resume_stmt = $conn->prepare("SELECT extracted_text FROM resumes WHERE user_id = ? ORDER BY uploaded_at DESC LIMIT 1");
$resume_stmt->bind_param("i", $user_id);
$resume_stmt->execute();
$resume_stmt->bind_result($resume_text);
$resume_stmt->fetch();
$resume_stmt->close();

if (empty($resume_text)) {
    $resume_text = "";
}

// ✅ Turn resume text into array
$resume_words = [];
if (!empty($resume_text)) {
    $resume_words = array_map('strtolower', preg_split('/[\s,]+/', $resume_text));
}

// ✅ Fetch all active jobs
$jobs = [];
$result = $conn->query("SELECT id, title, company, location, required_skills, job_type, salary FROM jobs WHERE status='active'");
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
$result->close();

// ✅ Calculate matches and filter eligible jobs (>=50%)
$job_matches = [];
foreach ($jobs as $job) {
    $job_skills = [];
    if (!empty($job['required_skills'])) {
        $job_skills = array_map('strtolower', preg_split('/[\s,]+/', $job['required_skills']));
    }

    $matched_skills = [];
    if (!empty($resume_words) && !empty($job_skills)) {
        $matched_skills = array_intersect($resume_words, $job_skills);
    }

    $match_percentage = 0;
    if (count($job_skills) > 0) {
        $match_percentage = round((count($matched_skills) / count($job_skills)) * 100);
    }

    // ✅ Only include eligible jobs
    if ($match_percentage >= 50) {
        $job_matches[] = [
            'title' => $job['title'],
            'company' => $job['company'],
            'location' => $job['location'],
            'job_type' => $job['job_type'],
            'salary' => $job['salary'],
            'matched_skills' => $matched_skills,
            'match_percentage' => $match_percentage
        ];
    }
}

// ✅ Take first eligible job for circle display
$firstJob = !empty($job_matches) ? $job_matches[0] : null;

// subscription plan

$plan_name = isset($_GET['plan']) ? $_GET['plan'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : 0;
$duration_days = isset($_GET['duration']) ? $_GET['duration'] : 0;
$description = isset($_GET['desc']) ? $_GET['desc'] : '';

if ($plan_name != '') {
    // DB में insert करना
    $stmt = $conn->prepare("INSERT INTO subscriptions (user_id,plan_name,price,duration_days,description) VALUES (?,?,?,?,?)");
    $stmt->bind_param("isdss", $user_id, $plan_name, $price, $duration_days, $description);
    $stmt->execute();
    $message = ucfirst($plan_name);
} else {
    $message = "No plan selected!";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SkillSyncPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
    /* ---- Background & Particles ---- */
    body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        height: 100vh;
        width: 100%;
        display: flex;
        flex-direction: column;
        background: linear-gradient(120deg, #00111f, #003366, #00111f);
        background-size: 400% 400%;
        animation: gradientBG 20s ease infinite;
        color: var(--white);
        overflow-x: hidden;
        position: relative;
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

    #tsparticles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -2;
    }

    /* ---- Header ---- */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 50px;
        position: relative;
        z-index: 1;
    }

    .header .logo {
        font-size: 1.6rem;
        font-weight: 700;
        background: linear-gradient(90deg, #ffffff, #00f7ff, #ff00ff);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
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

    .header a {
        background: linear-gradient(90deg, #ff00fbff, #f2ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        border: solid #fff 1px;
        padding: 10px 20px;
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
    }

    .header a:hover {
        transform: scale(1.1);
    }

    /* ---- Main Content ---- */
    .main-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 20px 50px;
        width: 100%;
        position: relative;
        z-index: 1;
    }

    .left-section {
        flex: 1;
    }

    h1 {
        font-size: 2.5rem;
        margin-bottom: 20px;
        margin-left: 20%;
        font-family: 'Algerian', 'Times New Roman', serif;
        background: linear-gradient(90deg, #ff00ff, #00f7ff, #00ff1a);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
    }

    .subscription-plan {
        font-size: 0.9rem;

        margin-left: 30px;
        margin-top: 40px;

    }

    .skill-match {
        margin-top: 40px;
        margin-left: 30px;
    }

    .skill-match h2 {
        font-size: 1.2rem;
        font-weight: 600;
        background-color: #1aff00ff;
        color: #000;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .job-eligible h3 {
        font-size: 1.2rem;
        font-weight: 600;
        background: linear-gradient(90deg, #ff00fbff, #ffff00ff, #ff00ff);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
        color: #000;
        margin-top: 20px;
        margin-left: 30px;
    }



    .skills-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: flex-start;
        margin-top: 10px;
    }

    .skill-tag {
        background: linear-gradient(90deg, #1aff00ff, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-size: 300% 300%;
        background-color: rgba(0, 0, 0, 0.30);
        animation: gradientMove 5s ease infinite;
        padding: 8px 15px;
        border: solid 1px #fff;
        border-radius: 20px;
        font-size: 0.9rem;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.4s ease;
    }

    .skill-tag:hover {
        transform: scale(1.1);
        box-shadow: 0 0 15px #ff00ff88, 0 0 30px #00ffff88;
        background: linear-gradient(90deg, #ff00ff, #00ffff);
        -webkit-text-fill-color: white;
    }

    /* Buttons Container */
    .buttons-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-top: 50px;
        width: 100%;
        margin-left: 200px;
        align-items: center;
    }

    .action-button {
        text-align: center;
        padding: 20px;
        background-color: rgba(0, 0, 0, 0.30);
        border-radius: 10px;
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: solid #ffffff43 1px;
    }

    .action-button:hover {
        background-color: rgba(52, 51, 51, 0.1);
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 255, 255, 0.3);
    }

    .action-button h3 {
        margin-top: 10px;
        font-size: 1rem;
        font-weight: 600;
        background: linear-gradient(90deg, #ff00fbff, #f2ff00ff);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
    }

    .action-button i {
        font-size: 2rem;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #1aff00ff, #ff00fbff);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
    }

    /* Right Section Circle */
    .right-section {
        display: flex;
        flex-direction: column;
        margin-top: 60px;
        flex-shrink: 0;
        margin-right: 180px;
    }

    .circle-container {
        position: relative;
        width: 200px;
        height: 200px;
    }

    .progress-circle {
        width: 200px;
        height: 200px;
        transform-origin: 50% 50%;
        animation: spin 4s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(-90deg);
        }

        to {
            transform: rotate(270deg);
        }
    }

    .progress-circle .background {
        stroke: rgba(255, 255, 255, 0.2);
        stroke-width: 15;
        fill: none;
    }

    .progress-circle .foreground {
        stroke: url(#gradient);
        stroke-width: 15;
        fill: none;
        stroke-linecap: round;
        transform-origin: 50% 50%;
        transform: rotate(-90deg);
        transition: stroke-dashoffset 0.5s ease;
    }

    .circle-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(90deg, #ff00fbff, #f2ff00ff);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientMove 5s ease infinite;
    }

    .circle-label {
        margin-top: 15px;
        font-size: 1rem;
        text-align: center;
        color: #1aff00ff;
    }

    /* Footer */
    .footer {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .logout-btn-footer {
        background: linear-gradient(90deg, #00f7ff, #ff00ff);
        color: #000;
        border: none;
        border-radius: 30px;
        padding: 12px 60px;
        font-weight: bold;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .logout-btn-footer:hover {
        box-shadow: 0 0 20px #ff00ff88, 0 0 40px #00ffff88;
        background: linear-gradient(90deg, #ff00ff, #00ffff88);
        transform: scale(1.05) rotate(-1deg);
    }

    .version-text {
        font-size: 0.8rem;
        margin-top: 5px;
        color: #a0a0a0;
    }

    /* Responsive */
    @media (max-width:992px) {
        .main-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 20px;
        }

        .buttons-container {
            grid-template-columns: repeat(2, 1fr);
            margin-right: 0;
            margin-left: 0;
        }

        .right-section {
            margin-top: 40px;
            margin-right: 0;
        }

        .circle-container {
            width: 150px;
            height: 150px;
        }

        .circle-text {
            font-size: 2rem;
            margin-left: 20px;
            margin-top: 20px;
        }

        .circle-label {
            margin-top: 50px;
        }

    }

    @media (max-width:576px) {
        .buttons-container {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>
    <div class="tbackground"></div>
    <div id="tsparticles"></div>

    <header class="header">
        <div class="logo">SkillSyncPro</div>
        <a href="logout.php">Log Out</a>
    </header>

    <main class="main-content">
        <div class="left-section">
            <h1>Welcome ,<?= htmlspecialchars($user_name) ?></h1>

            <p style=" font-size: 1rem;color: #ffffff; opacity:0.65;   margin-left: 30px;margin-top: 40px;">
                <strong>Subscription Plan :
                </strong><?php echo $message;
                ?>
            </p>

            <div class="skill-match">
                <h2><strong>Skills For Resume Match :</strong></h2>
                <div class="skills-list">
                    <span class="skill-tag">HTML</span><span class="skill-tag">CSS</span>
                    <span class="skill-tag">JavaScript</span><span class="skill-tag">React-Js</span>
                    <span class="skill-tag">Node-Js</span><span class="skill-tag">PHP</span>
                </div>
                <div class="skills-list" style="margin-top: 10px;">
                    <span class="skill-tag">Java</span><span class="skill-tag">MySQL</span>
                    <span class="skill-tag">Python</span><span class="skill-tag">Github</span>
                    <span class="skill-tag">Android SDK</span> <span class="skill-tag">Kotlin </span>
                </div>
            </div>

            <?php if (!empty($job_matches)): ?>
            <?php foreach ($job_matches as $job): ?>
            <div class="job-eligible">
                <h3>💼 <strong><i>You Are Eligible For This Job : </i></strong>
                    <?= htmlspecialchars($job['title']); ?>
                </h3>
                <p style="color: #ffffff; opacity:0.65; margin-left:30px;">
                    <strong>Company : </strong> <?= htmlspecialchars($job['company']) ?> |
                    <strong>Location : </strong>
                    <?= htmlspecialchars($job['location']) ?> |
                    <strong>Type : </strong>
                    <?= htmlspecialchars($job['job_type'] ?? 'N/A') ?> |
                    <strong>Salary : </strong> <?= htmlspecialchars($job['salary'] ?? 'N/A') ?>
                </p>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="job-eligible">
                <h3><i>No eligible jobs found.</i></h3>
            </div>
            <?php endif; ?>

            <div class="buttons-container">
                <a href="upload-resume.php" class="action-button"><i class="fas fa-upload"></i>
                    <h3>Upload Resume</h3>
                </a>
                <a href="subscription.php" class="action-button"><i class="fas fa-bell"></i>
                    <h3>Subscription Plan</h3>
                </a>
                <a href="manage-job.php" class="action-button"><i class="fas fa-briefcase"></i>
                    <h3>Manage Jobs</h3>
                </a>
                <a href="profile.php" class="action-button"><i class="fas fa-user"></i>
                    <h3>Profile</h3>
                </a>
            </div>
        </div>

        <div class="right-section">
            <?php if ($firstJob): ?>
            <div class="circle-container">
                <svg class="progress-circle">
                    <defs>
                        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                            <stop offset="0%" style="stop-color:#ff00ff" />
                            <stop offset="50%" style="stop-color: #f2ff00ff" />
                            <stop offset="50%" style="stop-color:#00f7ff" />
                            <stop offset="100%" style="stop-color:#ff00ff" />
                        </linearGradient>
                    </defs>
                    <circle class="background" r="90" cx="100" cy="100" />
                    <circle class="foreground" r="90" cx="100" cy="100"
                        style="stroke-dasharray:565.48; stroke-dashoffset:<?= 565.48 - ($firstJob['match_percentage'] / 100) * 565.48 ?>;" />
                </svg>
                <div class="circle-text">
                    <?= $firstJob['match_percentage']; ?>%
                </div>
            </div>
            <div class="circle-label">
                <i style="color: #ffffff; opacity:0.65; "><strong>Matched Skills : </strong>
                    <?= implode(' | ', $firstJob['matched_skills']); ?><br>
                </i>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="footer">
        <a href="logout.php" class="logout-btn-footer">Logout</a>
    </footer>

    <script>
    const circle = document.querySelector('.foreground');
    const radius = circle.r.baseVal.value;
    const circumference = 2 * Math.PI * radius;
    circle.style.strokeDasharray = `${circumference} ${circumference}`;
    const percent = <?= $firstJob ? $firstJob['match_percentage'] : 0 ?>;
    const offset = circumference - (percent / 100) * circumference;
    circle.style.strokeDashoffset = offset;
    </script>

    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2/tsparticles.bundle.min.js"></script>
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
</body>

</html>