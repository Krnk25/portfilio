<?php
session_start();
require 'db.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Job
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_job'])) {
    $title = $_POST['title'] ?? '';
    $company = $_POST['company'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $required_skills = $_POST['required_skills'] ?? '';
    $job_type = $_POST['job_type'] ?? '';
    $status = $_POST['status'] ?? 'Active';
    $salary = $_POST['salary'] ?? '';
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    if ($title && $company && $location && $description && $required_skills && $job_type && $status && $salary) {
        $stmt = $conn->prepare("INSERT INTO jobs(title,company,location,description,required_skills,job_type,status,salary,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssssss", $title, $company, $location, $description, $required_skills, $job_type, $status, $salary, $created_at, $updated_at);
        $stmt->execute();
        $stmt->close();
    }
}

// Update Job
if (isset($_POST['update_job'])) {
    $id = $_POST['id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $company = $_POST['company'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $required_skills = $_POST['required_skills'] ?? '';
    $job_type = $_POST['job_type'] ?? '';
    $status = $_POST['status'] ?? 'Active';
    $salary = $_POST['salary'] ?? '';

    if ($id && $title && $company) {
        $sql = "UPDATE jobs SET title=?,company=?,location=?,description=?,required_skills=?,job_type=?,status=?,salary=?,updated_at=NOW() WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $title, $company, $location, $description, $required_skills, $job_type, $status, $salary, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete Job
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Search / Filter
$search = $_GET['search'] ?? '';
$searchParam = "%$search%";
$stmt = $conn->prepare("SELECT * FROM jobs WHERE title LIKE ? OR company LIKE ? OR location LIKE ? OR required_skills LIKE ? OR job_type LIKE ? OR status LIKE ?");
$stmt->bind_param("ssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
    <style>
    /* --- BASE --- */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
        transition: 0.3s;
    }

    body {
        background: #0a0a0a;
        color: #fff;
        overflow-x: hidden;
    }

    /* PARTICLE CANVAS */
    canvas#particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }

    /* CONTAINER */
    .container {
        width: 90%;
        max-width: 1200px;
        margin: 30px auto;
        position: relative;
        z-index: 1;
    }

    /* HEADER */
    h1 {
        text-align: center;
        font-size: 3rem;
        background: linear-gradient(90deg, #ff00ff, #00ffff, #ff0066);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
        margin-bottom: 35px;
    }

    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .header-buttons {
        display: flex;
        gap: 15px;
    }

    /* BUTTONS */
    button,
    .btn-neon {
        padding: 12px 25px;
        border: none;
        border-radius: 30px;
        text-decoration: none;
        cursor: pointer;
        font-weight: 600;
        min-width: 150px;
        height: 50px;
        text-transform: uppercase;
        background: linear-gradient(135deg, #00c6ff, #0072ff);
        color: #fff;
        box-shadow: 0 0 20px #00c6ff80, 0 0 40px #ff00ff50;
        position: relative;
        overflow: hidden;
    }

    button:hover,
    .btn-neon:hover {
        transform: scale(1.05);
        box-shadow: 0 0 30px #00ffffaa, 0 0 50px #ff00ffaa;
    }

    .dark-toggle {
        background: linear-gradient(135deg, #ff00ff, #00ffff);
        box-shadow: 0 0 20px #ff00ff80;
    }

    /* SEARCH */
    .search-box {
        width: 60%;
        margin: 0 auto 30px auto;
    }

    .search-box input {
        width: 100%;
        padding: 14px 20px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: #fff;
        font-size: 1rem;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.2);
        outline: none;
    }

    .search-box input:focus {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.02);
    }

    /* JOB LIST */
    .job-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    /* JOB CARDS */
    .job-box {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 25px;
        padding: 25px;
        position: relative;
        backdrop-filter: blur(15px);
        box-shadow: 0 4px 15px rgba(0, 255, 255, 0.1), 0 0 10px rgba(255, 0, 255, 0.1);
        transition: all 0.4s ease, transform 0.3s ease, box-shadow 0.3s ease;
    }

    .job-box:hover {
        transform: translateY(-10px) scale(1.03);
        box-shadow: 0 15px 40px rgba(0, 255, 255, 0.25), 0 0 30px rgba(255, 0, 255, 0.2);
    }

    .job-box h3 {
        margin-bottom: 12px;
        color: #ff00ff;
        text-shadow: 0 0 10px #ff00ff88;
        font-size: 1.7rem;
    }

    .job-box p {
        margin: 5px 0;
        font-size: 1rem;
        line-height: 1.4;
    }

    .job-box strong {
        color: #00ffff;
    }

    /* ACTIONS */
    .job-actions {
        position: absolute;
        top: 15px;
        right: 15px;
        display: flex;
        gap: 10px;
        opacity: 0;
        transition: 0.3s;
    }

    .job-box:hover .job-actions {
        opacity: 1;
    }

    .edit-btn {
        background: linear-gradient(135deg, #05ff6dff, #65ffa5ff);
        color: #fff;
        box-shadow: 0 0 15px #2ecc7180;
    }

    .delete-btn {
        background: linear-gradient(135deg, #ff1900ff, #ff7675);
        color: #000000ff;
        box-shadow: 0 0 15px #e74c3c80;
    }

    .edit-btn:hover,
    .delete-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 0 25px #2ecc71, 0 0 25px #ff4c4c;
    }

    /* MODALS */
    .modal {
        display: none;
        position: fixed;
        z-index: 10;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background: rgba(0, 0, 0, 0.75);
    }

    .modal-content {
        background: rgba(255, 255, 255, 0.05);
        margin: 5% auto;
        padding: 30px;
        border-radius: 25px;
        width: 90%;
        max-width: 500px;
        color: #fff;
        backdrop-filter: blur(20px);
        box-shadow: 0 8px 35px rgba(0, 255, 255, 0.25), 0 0 20px rgba(255, 0, 255, 0.15);
        position: relative;
        animation: fadeIn 0.5s ease;
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

    .modal-content input,
    .modal-content textarea,
    .modal-content select {
        width: 100%;
        margin-bottom: 18px;
        padding: 14px 20px;
        border-radius: 15px;
        border: none;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        font-size: 1rem;
        outline: none;
        transition: 0.3s;
    }

    .modal-content input:focus,
    .modal-content textarea:focus,
    .modal-content select:focus {
        background: rgba(255, 255, 255, 0.35);
        transform: scale(1.02);
        box-shadow: 0 0 15px #00ffff88, 0 0 20px #ff00ff88;
    }

    .modal-content button {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        cursor: pointer;
        font-weight: 600;
        letter-spacing: 1px;
        transition: 0.3s;
    }

    .modal-content button:hover {
        transform: scale(1.05);
        box-shadow: 0 0 25px #2ecc71, 0 0 35px #00ffff;
    }

    .modal-content .close {
        position: absolute;
        top: 12px;
        right: 18px;
        font-size: 26px;
        cursor: pointer;
        color: #ff00ff;
        text-shadow: 0 0 10px #ff00ff88;
    }

    /* DARK MODE */
    body.dark {
        background: #0b0b0b;
    }

    body.dark .job-box {
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 20px rgba(0, 255, 255, 0.25);
    }

    body.dark .modal-content {
        background: rgba(255, 255, 255, 0.08);
    }
    </style>

</head>

<body>
    <canvas id="particles"></canvas>

    <div class="container">
        <h1>Manage Jobs</h1>
        <div class="header-bar">
            <a href="dashboard.php" class="btn-neon">⬅ Back To Dashboard</a>
            <div class="header-buttons">
                <button class="dark-toggle" onclick="document.body.classList.toggle('dark')">🌙 Toggle Dark
                    Mode</button>
                <button class="add-job-btn" onclick="document.getElementById('addModal').style.display='block'">+ Add
                    Job</button>
            </div>
        </div>

        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search jobs..." value="<?= htmlspecialchars($search) ?>">
        </form>

        <div class="job-list">
            <?php while ($job = $result->fetch_assoc()): ?>
            <div class="job-box" id="job-<?= $job['id'] ?>" data-title="<?= htmlspecialchars($job['title']) ?>"
                data-company="<?= htmlspecialchars($job['company']) ?>"
                data-location="<?= htmlspecialchars($job['location']) ?>"
                data-salary="<?= htmlspecialchars($job['salary']) ?>"
                data-skills="<?= htmlspecialchars($job['required_skills']) ?>"
                data-type="<?= htmlspecialchars($job['job_type']) ?>"
                data-status="<?= htmlspecialchars($job['status']) ?>"
                data-description="<?= htmlspecialchars($job['description']) ?>">
                <h3><?= htmlspecialchars($job['title']) ?></h3>
                <p><strong>Company:</strong> <?= htmlspecialchars($job['company']) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                <p><strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?></p>
                <p><strong>Required Skills:</strong> <?= htmlspecialchars($job['required_skills']) ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($job['job_type']) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($job['description']) ?></p>
                <div class="job-actions">
                    <button type="button" class="edit-btn" onclick="openEditModal(<?= $job['id'] ?>)">Edit</button>
                    <a href="manage-job.php?delete=<?= $job['id'] ?>"
                        onclick="return confirm('Delete this job?');"><button type="button"
                            class="delete-btn">Delete</button></a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Add Job Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
            <h2>Add Job</h2>
            <form method="POST">
                <input type="text" name="title" placeholder="Job Title" required>
                <input type="text" name="company" placeholder="Company" required>
                <input type="text" name="location" placeholder="Location" required>
                <input type="text" name="salary" placeholder="Salary" required>
                <input type="text" name="required_skills" placeholder="Required Skills" required>
                <textarea name="description" placeholder="Job Description" required></textarea>
                <select name="job_type" required>
                    <option value="">Select Job Type</option>
                    <option value="Full-Time">Full-Time</option>
                    <option value="Part-Time">Part-Time</option>
                    <option value="Internship">Internship</option>
                </select>
                <select name="status" required>
                    <option value="">Select Status</option>
                    <option value="Active">Active</option>
                    <option value="Closed">Closed</option>
                </select>
                <button type="submit" name="add_job">Add Job</button>
            </form>
        </div>
    </div>

    <!-- Edit Job Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('editModal').style.display='none'">&times;</span>
            <h2>Edit Job</h2>
            <form method="POST">
                <input type="hidden" name="id" id="editJobId">
                <input type="text" name="title" id="editTitle" placeholder="Job Title" required>
                <input type="text" name="company" id="editCompany" placeholder="Company" required>
                <input type="text" name="location" id="editLocation" placeholder="Location" required>
                <textarea name="description" id="editDescription" placeholder="Job Description" required></textarea>
                <input type="text" name="required_skills" id="editSkills" placeholder="Required Skills" required>
                <select name="job_type" id="editType">
                    <option value="Full-Time">Full-Time</option>
                    <option value="Part-Time">Part-Time</option>
                    <option value="Internship">Internship</option>
                </select>
                <select name="status" id="editStatus">
                    <option value="Active">Active</option>
                    <option value="Closed">Closed</option>
                </select>
                <input type="text" name="salary" id="editSalary" placeholder="Salary" required>
                <button type="submit" name="update_job">Update Job</button>
            </form>
        </div>
    </div>

    <script>
    // Edit Modal
    function openEditModal(id) {
        const jobBox = document.getElementById('job-' + id);
        document.getElementById('editJobId').value = id;
        document.getElementById('editTitle').value = jobBox.dataset.title;
        document.getElementById('editCompany').value = jobBox.dataset.company;
        document.getElementById('editLocation').value = jobBox.dataset.location;
        document.getElementById('editSalary').value = jobBox.dataset.salary;
        document.getElementById('editSkills').value = jobBox.dataset.skills;
        document.getElementById('editType').value = jobBox.dataset.type;
        document.getElementById('editStatus').value = jobBox.dataset.status;
        document.getElementById('editDescription').value = jobBox.dataset.description;
        document.getElementById('editModal').style.display = 'block';
    }

    // Close modals on outside click
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }

    // PARTICLES
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