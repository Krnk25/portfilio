<?php
session_start();
include 'db.php'; // PDO connection

// Handle filter session save
if(isset($_POST['save_filters'])){
    $_SESSION['job_filters'] = $_POST['filters'];
    echo json_encode(['success'=>true]);
    exit;
}

// Current filters
$filters = $_SESSION['job_filters'] ?? [];

// Fetch jobs function
function fetchJobs($pdo, $filters=[]){
    $query = "SELECT * FROM jobs WHERE 1=1";
    $params = [];

    if(!empty($filters['location'])) { $query .= " AND location LIKE ?"; $params[] = "%".$filters['location']."%"; }
    if(!empty($filters['job_type'])) { $query .= " AND job_type LIKE ?"; $params[] = "%".$filters['job_type']."%"; }
    if(!empty($filters['status'])) { $query .= " AND status LIKE ?"; $params[] = "%".$filters['status']."%"; }
    if(!empty($filters['keyword'])){
        $query .= " AND (title LIKE ? OR company LIKE ? OR required_skills LIKE ?)";
        $params[] = "%".$filters['keyword']."%";
        $params[] = "%".$filters['keyword']."%";
        $params[] = "%".$filters['keyword']."%";
    }

    $query .= " ORDER BY sort_order ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $jobs = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $row['skills'] = explode(',', $row['required_skills']);
        $jobs[] = $row;
    }
    return $jobs;
}

// Handle AJAX actions
if(isset($_POST['action'])){
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $response = [];

    if($action === 'fetch'){
        $jobs = fetchJobs($pdo, $_POST['filters'] ?? []);
        $response = ['jobs' => $jobs];
    }

    if($action === 'add'){
        $stmt = $pdo->prepare("INSERT INTO jobs (title,company,location,job_type,status,salary,required_skills,description,sort_order) VALUES (?,?,?,?,?,?,?,?,?)");
        $max_sort = $pdo->query("SELECT COALESCE(MAX(sort_order),0)+1 as next_sort FROM jobs")->fetch(PDO::FETCH_ASSOC)['next_sort'];
        $stmt->execute([
            $_POST['title'],$_POST['company'],$_POST['location'],$_POST['job_type'],$_POST['status'],
            $_POST['salary'],$_POST['required_skills'],$_POST['description'],$max_sort
        ]);
        $response = ['success'=>true];
    }

    if($action === 'edit'){
        $stmt = $pdo->prepare("UPDATE jobs SET title=?, company=?, location=?, job_type=?, status=?, salary=?, required_skills=?, description=? WHERE id=?");
        $stmt->execute([
            $_POST['title'],$_POST['company'],$_POST['location'],$_POST['job_type'],$_POST['status'],
            $_POST['salary'],$_POST['required_skills'],$_POST['description'],$_POST['job_id']
        ]);
        $response = ['success'=>true];
    }

    if($action === 'delete'){
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id=?");
        $stmt->execute([$_POST['job_id']]);
        $response = ['success'=>true];
    }

    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 20px;
        min-height: 100vh;
        background: linear-gradient(270deg, #6a11cb, #2575fc, #6a11cb);
        background-size: 600% 600%;
        animation: gradientBG 15s ease infinite;
        color: #fff;
        transition: 0.3s;
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

    .dark-mode {
        background: #121212 !important;
        color: #fff;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 2.5rem;
    }

    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .filter-button,
    .theme-toggle,
    .add-button {
        background: #fff;
        color: #333;
        padding: 10px 15px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: .3s;
    }

    .filter-button:hover,
    .theme-toggle:hover,
    .add-button:hover {
        background: #2575fc;
        color: #fff;
    }

    .filter-drawer {
        position: fixed;
        top: 0;
        left: -320px;
        width: 320px;
        height: 100%;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        padding: 25px;
        transition: left .3s ease;
        overflow-y: auto;
        z-index: 9999;
        border-top-right-radius: 15px;
        border-bottom-right-radius: 15px;
    }

    .filter-drawer.open {
        left: 0;
    }

    .filter-drawer h3 {
        color: #fff;
        margin-bottom: 20px;
        font-size: 1.3rem;
        font-weight: 600;
    }

    .filter-drawer .filter-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .filter-drawer select,
    .filter-drawer input {
        padding: 10px;
        border-radius: 6px;
        border: none;
        font-size: 14px;
        outline: none;
        background: #fff;
        color: #333;
        font-weight: 500;
    }

    .apply-filter {
        padding: 10px;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        background: #fff;
        color: #333;
        cursor: pointer;
        transition: .3s;
    }

    .apply-filter:hover {
        background: #2575fc;
        color: #fff;
    }

    .job-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .job-card {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 15px;
        padding: 20px;
        transition: transform .3s, box-shadow .3s;
        cursor: grab;
        position: relative;
    }

    .job-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
    }

    .job-card h2 {
        font-size: 1.5rem;
        margin-bottom: 5px;
        color: #fff;
    }

    .job-card p {
        font-size: 14px;
        margin-bottom: 5px;
        color: #f1f1f1;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .status-active {
        background: #4caf50;
        color: #fff;
    }

    .status-closed {
        background: #f44336;
        color: #fff;
    }

    .skills {
        margin-top: 10px;
    }

    .skill-chip {
        display: inline-block;
        padding: 5px 10px;
        margin: 3px;
        background: #fff;
        color: #333;
        border-radius: 15px;
        font-size: 12px;
    }

    .job-card button {
        position: absolute;
        top: 10px;
        right: 10px;
        margin-left: 5px;
    }
    </style>
</head>

<body>
    <h1>Manage Jobs</h1>
    <div class="top-bar">
        <div class="d-flex gap-2 flex-wrap">
            <button class="filter-button" onclick="toggleDrawer()">☰ Filters</button>
            <button class="add-button" data-bs-toggle="modal" data-bs-target="#addJobModal">+ Add Job</button>
        </div>
        <button class="theme-toggle" onclick="toggleTheme()">🌙/☀️ Theme</button>
    </div>

    <div class="filter-drawer" id="filterDrawer">
        <h3>Filter Jobs</h3>
        <form class="filter-form" id="filterForm">
            <select name="location">
                <option value="">Select Location</option>
                <option value="Mumbai">Mumbai</option>
                <option value="Delhi">Delhi</option>
            </select>
            <select name="job_type">
                <option value="">Select Job Type</option>
                <option value="Full Time">Full Time</option>
                <option value="Part Time">Part Time</option>
            </select>
            <select name="status">
                <option value="">Select Status</option>
                <option value="Active">Active</option>
                <option value="Closed">Closed</option>
            </select>
            <button type="button" class="apply-filter">Apply Filters</button>
        </form>
    </div>

    <div id="jobContainer" class="job-container"></div>

    <!-- Add/Edit Job Modal -->
    <div class="modal fade" id="addJobModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="jobForm">
                        <input type="hidden" name="job_id">
                        <div class="mb-3"><label>Title</label><input type="text" name="title" class="form-control"
                                required></div>
                        <div class="mb-3"><label>Company</label><input type="text" name="company" class="form-control"
                                required></div>
                        <div class="mb-3"><label>Location</label><input type="text" name="location" class="form-control"
                                required></div>
                        <div class="mb-3"><label>Job Type</label><select name="job_type" class="form-control">
                                <option>Full Time</option>
                                <option>Part Time</option>
                            </select></div>
                        <div class="mb-3"><label>Status</label><select name="status" class="form-control">
                                <option>Active</option>
                                <option>Closed</option>
                            </select></div>
                        <div class="mb-3"><label>Salary</label><input type="text" name="salary" class="form-control"
                                required></div>
                        <div class="mb-3"><label>Skills (comma separated)</label><input type="text"
                                name="required_skills" class="form-control"></div>
                        <div class="mb-3"><label>Description</label><textarea name="description" class="form-control"
                                rows="3"></textarea></div>
                        <button type="submit" class="btn btn-primary">Save Job</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleDrawer() {
        document.getElementById("filterDrawer").classList.toggle("open");
    }

    function toggleTheme() {
        document.body.classList.toggle("dark-mode");
    }

    let currentFilters = {};

    function fetchJobs(filters = {}) {
        currentFilters = filters;
        $.post('manage-job.php', {
            action: 'fetch',
            filters: filters
        }, function(data) {
            let html = '';
            data.jobs.forEach(job => {
                html += `<div class="job-card" data-id="${job.id}">
            <span class="status-badge ${job.status.toLowerCase()==='active'?'status-active':'status-closed'}">${job.status}</span>
            <h2>${job.title}</h2>
            <p><strong>Company:</strong> ${job.company}</p>
            <p><strong>Location:</strong> ${job.location}</p>
            <p><strong>Job Type:</strong> ${job.job_type}</p>
            <p><strong>Salary:</strong> ${job.salary}</p>
            <div class="skills">${job.skills.map(s=>`<span class="skill-chip">${s}</span>`).join('')}</div>
            <button class="btn btn-sm btn-warning edit-btn">Edit</button>
            <button class="btn btn-sm btn-danger delete-btn">Delete</button>
            </div>`;
            });
            $('#jobContainer').html(html);
        }, 'json');
    }
    fetchJobs();

    $('.apply-filter').click(function() {
        let filters = {};
        filters.location = $('[name="location"]').val();
        filters.job_type = $('[name="job_type"]').val();
        filters.status = $('[name="status"]').val();
        $.post('manage-job.php', {
            save_filters: 1,
            filters: filters
        });
        fetchJobs(filters);
    });

    $('#jobForm').submit(function(e) {
        e.preventDefault();
        let action = $(this).find('[name="job_id"]').val() ? 'edit' : 'add';
        let data = $(this).serializeArray();
        data.push({
            name: 'action',
            value: action
        });
        $.post('manage-job.php', data, function(resp) {
            if (resp.success) {
                $('#addJobModal').modal('hide');
                $('#jobForm')[0].reset();
                fetchJobs(currentFilters);
            }
        }, 'json');
    });

    $(document).on('click', '.edit-btn', function() {
        let card = $(this).closest('.job-card');
        let id = card.data('id');
        let title = card.find('h2').text();
        let company = card.find('p').eq(0).text().replace('Company: ', '');
        let location = card.find('p').eq(1).text().replace('Location: ', '');
        let job_type = card.find('p').eq(2).text().replace('Job Type: ', '');
        let salary = card.find('p').eq(3).text().replace('Salary: ', '');
        let skills = card.find('.skills').text().trim();
        let form = $('#jobForm');
        form.find('[name="job_id"]').val(id);
        form.find('[name="title"]').val(title);
        form.find('[name="company"]').val(company);
        form.find('[name="location"]').val(location);
        form.find('[name="job_type"]').val(job_type);
        form.find('[name="salary"]').val(salary);
        form.find('[name="required_skills"]').val(skills);
        form.find('[name="status"]').val(card.find('.status-badge').text());
        form.find('[name="description"]').val('');
        $('#addJobModal').modal('show');
    });

    $(document).on('click', '.delete-btn', function() {
        if (confirm('Are you sure you want to delete this job?')) {
            let id = $(this).closest('.job-card').data('id');
            $.post('manage-job.php', {
                action: 'delete',
                job_id: id
            }, function(resp) {
                if (resp.success) fetchJobs(currentFilters);
            }, 'json');
        }
    });
    </script>
</body>

</html>