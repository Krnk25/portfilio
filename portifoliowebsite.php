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




// Photo path
$photoPath = $user['photo'] ? 'uploads/' . htmlspecialchars($user['photo']) : 'default-avatar.png';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixed Header Example</title>
    <link rel="stylesheet " type="" href="portifolio.css">
    <script src="https://unpkg.com/typed.js@2.0.15/dist/typed.umd.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <header>
        <div class="logo">
            🌐
            <span class="logospan">CodeCraft</span>
        </div>
        <div class="hamburger" onclick="toggleMenu()">☰</div>
        <nav class="navbar" id="nav-menu">
            <ul>
                <li class="active">
                    <a href="#home">Home</a>
                </li>
                <li>
                    <a href="#about">About</a>
                </li>
                <li>
                    <a href="#services">Services</a>
                </li>
                <li>
                    <a href="#skills">Skills</a>
                </li>
                <li>
                    <a href="#project">Projects</a>
                </li>
                <li>
                    <a href="#contact">Contact</a>
                </li>
                <li>
                    <a href="logout.php" class="btn-logout">Logout</a>
                </li>
            </ul>
        </nav>
    </header>
    <section class="home" id="home">
        <div class="home-content">
            <h2>Hello, it's Me</h2>
            <h1>Kabade Karan</h1>
            <h2>
                And I'm a
                <span class="text"></span>
            </h2>
            <p>I'm a passionate Full Stack Developer who loves crafting clean, responsive, and visually engaging web
                applications using HTML, CSS, JS, PHP & MySQL.</p>
            <p>My goal is to turn ideas into digital reality — blending creativity with functionality to make impactful
                web solutions.</p>
            <div class="home-sci">
                <a href="https://www.instagram.com/krn_k_25/">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://web.whatsapp.com/">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="https://www.twitter.com">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.youtube.com">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="https://www.linkedin.com/in/karan-kabade-6a46b1259/">
                    <i class="fab fa-linkedin"></i>
                </a>
            </div>
            <a href="#about" class="btn-box">More About Me</a>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <script>
        function toggleMenu() {
            document.getElementById("nav-menu").classList.toggle("active");
        }
        document.addEventListener("DOMContentLoaded", function () {
            var typed = new Typed(".text", {
                strings: ["SOFTWARE DEVELOPER", "WEB DEVELOPER", "FRONTEND DEVELOPER", "BACKEND DEVELOPER"],
                typeSpeed: 100,
                backSpeed: 60,
                loop: true
            });
        });
    </script>
    <section class="about" id="about">
        <div class="about-container">
            <div class="about-img">
                <img src="<?php echo $photoPath; ?>" class="avatar" alt="Profile Photo">
                <label for="photo-upload" class="upload-icon">&#128247;</label>
                <form action="upload-photo.php" method="POST" enctype="multipart/form-data">
                    <input type="file" name="photo" id="photo-upload" onchange="this.form.submit()" hidden>
                </form>
            </div>
            <div class="about-content">
                <h2>About Me</h2>
                <li>Bsc CS (Bachelor of Science in Computer)</li>
                <li>Saw.Swarkar Mahavidyalya Beed , 2023 – 2026 (Pursuing, 3rd Year)</li>
                <li>Dr.Babasaheb Ambedkar Maratwada University Chattrapati Sambhaji Nagar.</li>

                <br>
                <h2>My Project</h2>
                <h3>
                    📌 Project Title:
                    <span>SkillSyncPro – Smart Resume & Job Matching System </span>
                </h3>
                <br>
                <h3>📝 Project Summary:</h3>
                <li>SkillSyncPro is an advanced web-based platform that connects job seekers and employers using
                    AI-powered skill analysis.
                    It analyzes users’ resumes, identifies key skills, and matches them with relevant job openings.</li>
              
                <li>SkillSyncPro is an AI-powered web platform that analyzes resumes, extracts key skills, and matches
                    users with the best job opportunities. Built using HTML, CSS, JavaScript, PHP, and MySQL, it
                    features smart dashboards for both users and admins.</li>
                <br>

                <h5>🔑 Key Features:</h5>

            
                <li><strong>Smart Resume Analysis: </strong>Automatically extracts and evaluates skills from uploaded
                    resumes</li>
             

                <li><strong>Skill Matching Algorithm:</strong> Suggests best-fit jobs based on candidate skills and
                    experience.</li>
               

                <li><strong>User Dashboard:</strong> Personalized panel showing matches, applied jobs, and
                    recommendations.</li>

                
                <li><strong>Admin Dashboard:</strong> Manage users, job postings, and skill data efficiently.</li>
                

                <li><strong>Modern UI:</strong> Clean, animated, and mobile-friendly frontend using HTML, CSS, JS, and
                 

                <li><strong>🔐 Authentication System:</strong> Secure login/signup (PHP + MySQL)</li>

                <li><strong>Backend:</strong> Powered by PHP + MySQL, ensuring fast and secure data handling.</li>
              

                <li>🔧 Mini Projects Section: Real-time demos</li>
                
                <a href="#services" class="about-btn">Explore My Work</a>


            </div>
        </div>
    </section>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById("profile-pic").src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    <section class="skills-section" id="skills">
        <h2 class="skills-title">
            My
            <span>Skills</span>
        </h2>
        <div class="skill-containers">
            <!-- Technical Skills -->
            <div class="container1">
                <h1 class="heading1">Technical Skills</h1>
                <div class="Technical-bars">
                    <div class="bar">
                        <div class="info">
                            <span>HTML - 90%</span>
                        </div>
                        <div class="progress-line html">
                            <span></span>
                        </div>
                    </div>
                    <div class="bar">
                        <div class="info">
                            <span>CSS - 87%</span>
                        </div>
                        <div class="progress-line css">
                            <span></span>
                        </div>
                    </div>
                    <div class="bar">
                        <div class="info">
                            <span>JavaScript - 70%</span>
                        </div>
                        <div class="progress-line javascript">
                            <span></span>
                        </div>
                    </div>
                    <div class="bar">
                        <div class="info">
                            <span>PHP - 60%</span>
                        </div>
                        <div class="progress-line php">
                            <span></span>
                        </div>
                    </div>
                    <div class="bar">
                        <div class="info">
                            <span>MySQL - 85%</span>
                        </div>
                        <div class="progress-line mysql">
                            <span></span>
                        </div>
                    </div>
                    <div class="bar">
                        <div class="info">
                            <span>Python - 70%</span>
                        </div>
                        <div class="progress-line python">
                            <span></span>
                        </div>
                    </div>
                    <div class="bar">
                        <div class="info">
                            <span>Java - 80%</span>
                        </div>
                        <div class="progress-line java">
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Professional Skills -->
            <div class="container2">
                <h1 class="heading">Professional Skills</h1>
                <div class="radial-bars">
                    <div class="radial-bar">
                        <svg viewBox="0 0 200 200">
                            <circle class="progress-bar" cx="100" cy="100" r="80"></circle>
                            <circle class="path" cx="100" cy="100" r="80" style="--dash-offset: 50;"></circle>
                        </svg>
                        <div class="percentage">90%</div>
                        <div class="textt">Creativity</div>
                    </div>
                    <div class="radial-bar">
                        <svg viewBox="0 0 200 200">
                            <circle class="progress-bar" cx="100" cy="100" r="80"></circle>
                            <circle class="path" cx="100" cy="100" r="80" style="--dash-offset: 125;"></circle>
                        </svg>
                        <div class="percentage">75%</div>
                        <div class="textt">Problem Solving</div>
                    </div>
                    <div class="radial-bar">
                        <svg viewBox="0 0 200 200">
                            <circle class="progress-bar" cx="100" cy="100" r="80"></circle>
                            <circle class="path" cx="100" cy="100" r="80" style="--dash-offset: 100;"></circle>
                        </svg>
                        <div class="percentage">80%</div>
                        <div class="textt">Communication</div>
                    </div>
                    <div class="radial-bar">
                        <svg viewBox="0 0 200 200">
                            <circle class="progress-bar" cx="100" cy="100" r="80"></circle>
                            <circle class="path" cx="100" cy="100" r="80" style="--dash-offset: 75;"></circle>
                        </svg>
                        <div class="percentage">85%</div>
                        <div class="textt">Teamwork</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="services" id="services">
        <h1>My Services</h1>
        <h2>What I Can Do For You</h2>
        <div class="service-grid">
            <div class="service-card">
                <i class="fas fa-code"></i>
                <h3>Web Development</h3>
                <p>I build responsive, modern, and user-friendly websites using the latest technologies like HTML, CSS,
                    JS, and frameworks.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-paint-brush"></i>
                <h3>UI/UX Design</h3>
                <p>Creative and interactive UI/UX designs that make your website/app more engaging and user-focused.</p>
            </div>
            <div class="service-card">
                <i class="fas fa-database"></i>
                <h3>Backend Development</h3>
                <p>Powerful backend systems using PHP, MySQL, and APIs to ensure secure and efficient performance.</p>
            </div>
        </div>
    </section>
    <section>
        <div class="project" id="project">
            <div class="main-text">
                <h2>
                    Latest
                    <span>Projects</span>
                </h2>
            </div>
            <div class="portfolio-content">
                <!-- Project 1 -->
                <div class="row">
                    <img src="images/webimage.jpg" alt="ShopX Project">
                    <div class="layer">
                        <h5>ShopX – A Modern E-Commerce Platform</h5>
                        <p>
                            A feature-rich, responsive e-commerce web app inspired by Amazon & Flipkart.
                            ShopX combines modern design, speed, and usability for a next-gen shopping experience.
                        </p>
                        <a href="projects/shopXproject/project1.html">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
                <!-- Project 2 -->
                <div class="row">
                    <img src="images/image1.jpg" alt="Virtual Marketplace">
                    <div class="layer">
                        <h5>Virtual Marketplace</h5>
                        <p>
                            An online shopping platform like Flipkart where users can browse, add to cart, and order.
                            Built using HTML, CSS, JavaScript & PHP with responsive design.
                        </p>
                        <a href="projects/virtual_marketplace/index.html">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
                <!-- Project 3 -->
                <div class="row">
                    <img src="images/image2.webp" alt="SkillSyncPro">
                    <div class="layer">
                        <h5>SkillSyncPro</h5>
                        <p>
                            An AI-powered job matching platform that uses smart resume analysis to connect
                            candidates with ideal job opportunities efficiently and intelligently.
                        </p>
                        <a href="projects/SkillSyncPro/index.php">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="contact" id="contact">
        <div class="contact-text">
            <h2>
                Contact
                <span>Me</span>
            </h2>
            <h4>Let's Work Together</h4>
            <p>Have a project in mind or want to collaborate? Let’s build something amazing together!</p>
            <ul class="contact-list">
                <li>
                    <i class="fa-solid fa-envelope"></i>
                    karankabade7@gmail.com
                </li>
                <li>
                    <i class="fa-solid fa-phone"></i>
                    +91 8265044456
                </li>
                <li>
                    <i class="fa-solid fa-location-dot"></i>
                    Beed, Maharashtra
                </li>
            </ul>
            <div class="home-sci">
                <a href="https://www.instagram.com" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://wa.me/1234567890" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="https://www.twitter.com" target="_blank">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.youtube.com" target="_blank">
                    <i class="fab fa-youtube"></i>
                </a>
                <a href="https://www.linkedin.com/in/karan-kabade-6a46b1259" target="_blank">
                    <i class="fab fa-linkedin"></i>
                </a>
            </div>
        </div>
        <div class="contact-form">
            <form action="">
                <input type="text" placeholder="Enter Your Name" required>
                <input type="email" placeholder="Enter Your Email" required>
                <input type="text" placeholder="Enter Your Subject">
                <textarea placeholder="Enter Your Message"></textarea>
                <input type="submit" value="Send Message" class="send">
            </form>
        </div>
    </section>
    <div class="last-text">
        <p>Developed by Karan Kabade © 2025</p>
        <a href="#home" class="top">
            <i class="fas fa-arrow-up"></i>
        </a>
    </div>
    <script src="script.js"></script>
</body>

</html>