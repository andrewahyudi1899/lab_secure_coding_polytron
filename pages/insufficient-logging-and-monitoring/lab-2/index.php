<?php
$page_title = "Insufficient Logging Lab 2 - Poor Monitoring";
require_once '../../../config/env.php';
require_once '../../../template/header.php';

$message = '';
$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    
    // VULNERABLE - Critical actions without proper monitoring
    // --- ISSUE ---
    // switch ($action) {
    //     case 'delete_user':
    //         $message = "User deleted successfully! (No audit log created)";
    //         break;
    //     case 'change_password':
    //         $message = "Password changed successfully! (No security log created)";
    //         break;
    //     case 'grant_admin':
    //         $message = "Admin privileges granted! (No privilege escalation log)";
    //         break;
    //     case 'access_sensitive':
    //         $message = "Sensitive data accessed! (No access log created)";
    //         break;
    //     default:
    //         $message = "Action completed without monitoring.";
    // }

    // --- PATCH ---
    switch ($action) {
        case 'delete_user':
            $message = "User deleted successfully!";

            error_log("User $username is deleted successfully");
            break;
        case 'change_password':
            $message = "Password changed successfully!";

            error_log("Password is changed successfully by user $username");
            break;
        case 'grant_admin':
            $message = "Admin privileges granted!";

            error_log("Admin privileges granted by user $username");
            break;
        case 'access_sensitive':
            $message = "Sensitive data accessed!";

            error_log("Sensitive data is accessed by user $username");
            break;
        default:
            $message = "Action completed without monitoring.";
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 px-0">
            <?php include '../../../template/nav.php'; ?>
        </div>
        
        <div class="col-md-9 col-lg-10 mt-60">
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="../">Insufficient Logging</a></li>
                                <li class="breadcrumb-item active">Lab 2</li>
                            </ol>
                        </nav>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h2">Lab 2: Poor Monitoring</h1>
                            <span class="lab-difficulty difficulty-medium">Medium</span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Administrative Actions</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($message): ?>
                                    <div class="alert alert-info" role="alert">
                                        <?php echo $message; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Target Username:</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="action" class="form-label">Administrative Action:</label>
                                        <select class="form-control" id="action" name="action" required>
                                            <option value="">Select action...</option>
                                            <option value="delete_user">Delete User</option>
                                            <option value="change_password">Change Password</option>
                                            <option value="grant_admin">Grant Admin Privileges</option>
                                            <option value="access_sensitive">Access Sensitive Data</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-danger">Execute Action</button>
                                </form>
                                
                                <div class="mt-4">
                                    <div class="alert alert-danger" role="alert">
                                        <h5>⚠️ Monitoring Deficiencies:</h5>
                                        <ul class="mb-0">
                                            <li>Critical actions not logged</li>
                                            <li>No real-time alerting</li>
                                            <li>No audit trail for privilege changes</li>
                                            <li>No data access monitoring</li>
                                            <li>No anomaly detection</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">💡 Lab Objectives</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li>✅ Perform unmonitored actions</li>
                                    <li>✅ Escalate privileges silently</li>
                                    <li>✅ Access data without detection</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">🛠️ Critical Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="actionsAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#unmonitored">
                                                Unmonitored Operations
                                            </button>
                                        </h2>
                                        <div id="unmonitored" class="accordion-collapse collapse" data-bs-parent="#actionsAccordion">
                                            <div class="accordion-body">
                                                All administrative actions execute without proper logging or monitoring
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../../template/footer.php'; ?>