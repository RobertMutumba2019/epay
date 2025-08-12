<?php
include 'config.php';

// Check if user is logged in
if (!user_id()) {
    redirect('login.php');
}

// Admin check
$is_admin = ($_SESSION['role'] === 'admin');

$page = "APPROVAL MATRIX";
$action = $_GET['action'] ?? 'all';

// Access Control Emulation
function checkAccess() {
    global $is_admin;
    if (!$is_admin) {
        echo '<div class="modal show" style="display:block; background: rgba(0,0,0,0.7); z-index:9999;">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                      <h4 class="modal-title">Access Denied</h4>
                    </div>
                    <div class="modal-body text-center">
                      <i class="fa fa-ban fa-3x text-danger mb-3"></i><br>
                      <p>You don’t have permission to access this page.</p>
                    </div>
                    <div class="modal-footer">
                      <a href="approval_matrix.php" class="btn btn-primary">Go Back</a>
                    </div>
                  </div>
                </div>
              </div>';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Approval Matrix</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fc;
            color: #333;
            transition: background 0.3s, color 0.3s;
        }

        body.dark-mode {
            background: #121212;
            color: #e0e0e0;
        }

        .navbar-brand {
            font-weight: 600;
            color: #4e73df !important;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #4e73df, #224abe);
            padding-top: 20px;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            margin: 8px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(4px);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            background: #ffffff;
            color: #333;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        .card-header {
            background: #4e73df;
            color: white;
            font-weight: 600;
            border-bottom: none;
            padding: 15px 20px;
        }

        .btn-primary {
            background: #4e73df;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
        }

        .btn-primary:hover {
            background: #3a5bbf;
        }

        .table th {
            background: #f1f3f8;
            font-weight: 600;
            color: #333;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9ff !important;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn-outline-secondary.dark-mode-toggle {
            border-color: #adb5bd;
            color: #495057;
        }

        body.dark-mode .card,
        body.dark-mode .table {
            background: #1e1e2d;
            color: #e0e0e0;
        }

        body.dark-mode .table th {
            background: #2d2d40;
            color: #ffffff;
        }

        footer {
            margin-left: 250px;
            padding: 20px;
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 100%;
                position: relative;
                height: auto;
            }
            .content, footer {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Dark Mode Toggle Button -->
    <button class="btn btn-sm btn-outline-secondary dark-mode-toggle position-fixed top-0 end-0 m-3 z-3" onclick="toggleDarkMode()">
        <i class="fas fa-moon"></i> <span id="modeText">Dark Mode</span>
    </button>

    <!-- Sidebar -->
    <div class="sidebar text-white">
        <div class="text-center mb-4">
            <h4><i class="fas fa-tasks"></i> Approval System</h4>
            <small>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></small>
        </div>
        <hr class="mx-3">
        <div class="list-group list-group-flush">
            <a href="?action=add" class="nav-link <?php echo $action === 'add' ? 'active' : ''; ?>">
                <i class="fas fa-plus-circle"></i> Add Matrix
            </a>
            <a href="?action=all" class="nav-link <?php echo $action === 'all' ? 'active' : ''; ?>">
                <i class="fas fa-th-list"></i> All Matrices
            </a>
            <a href="?action=import" class="nav-link <?php echo $action === 'import' ? 'active' : ''; ?>">
                <i class="fas fa-file-import"></i> Import CSV
            </a>
            
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2 class="mb-4 text-primary">
            <i class="fas fa-cogs"></i> <?= htmlspecialchars($page) ?>
        </h2>

        <!-- Dynamic Content -->
        <?php
        if ($action == 'add') {
            include 'add_approval.php';
        } elseif ($action == 'edit') {
            include 'edit_approval.php';
        } elseif ($action == 'delete') {
            include 'delete_approval.php';
        } elseif ($action == 'import') {
            include 'import_approval.php';
        } else {
            include 'all_approval.php';
        }
        ?>
    </div>

    <footer>
        &copy; <?= date('Y') ?> Approval Matrix System | Designed with <i class="fas fa-heart text-danger"></i>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Dark Mode Toggle Script -->
    <script>
        function toggleDarkMode() {
            const body = document.body;
            const icon = document.querySelector('.dark-mode-toggle i');
            const modeText = document.getElementById('modeText');

            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                icon.className = 'fas fa-sun';
                modeText.textContent = 'Light Mode';
                localStorage.setItem('theme', 'dark');
            } else {
                icon.className = 'fas fa-moon';
                modeText.textContent = 'Dark Mode';
                localStorage.setItem('theme', 'light');
            }
        }

        // Load saved theme
        window.onload = function () {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                document.querySelector('.dark-mode-toggle i').className = 'fas fa-sun';
                document.getElementById('modeText').textContent = 'Light Mode';
            }
        };
    </script>

</body>
</html>