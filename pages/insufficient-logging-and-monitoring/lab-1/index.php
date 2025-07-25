<?php
$page_title = "Insufficient Logging Lab 1 - Missing Security Logs";
require_once '../../../config/env.php';
require_once '../../../template/header.php';

$message = '';
// $attempts = 0;
if (empty($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
} 

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';

    // if (getenv('HTTP_CLIENT_IP')) {
    //     $ipaddress = getenv('HTTP_CLIENT_IP');
    // } else if (getenv('HTTP_X_FORWARDED_FOR')) {
    //     $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    // } else if (getenv('HTTP_X_FORWARDED')) {
    //     $ipaddress = getenv('HTTP_X_FORWARDED');
    // } else if (getenv('HTTP_FORWARDED_FOR')) {
    //     $ipaddress = getenv('HTTP_FORWARDED_FOR');
    // } else if (getenv('HTTP_FORWARDED')) {
    //    $ipaddress = getenv('HTTP_FORWARDED');
    // } else if(getenv('REMOTE_ADDR')) {
    //     $ipaddress = getenv('REMOTE_ADDR');
    // } else {
    //     $ipaddress = 'UNKNOWN';
    // }

    $ipaddress = $_SERVER['REMOTE_ADDR'];

    return $ipaddress;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // $attempts++;
    $_SESSION['login_attempts']++;
    
    // VULNERABLE - No logging of failed attempts
    // --- ISSUE ---
    // if ($username === 'admin' && $password === 'admin123') {
    //     $message = "Login successful!";
    //     // No successful login logging
    // } else {
    //     $message = "Invalid credentials.";
    //     // No failed login logging
    //     // No brute force detection
    //     // No alerting system
    // }

    // --- PATCH ---
    $checkLogin = true;
    $maxLoginEachMinute = 5;
    if ($_SESSION['login_attempts'] == 1) {
        $_SESSION['first_attempt_time'] = microtime(true);
    } else {
        $currentTime = microtime(true);
        $cooldownDuration = number_format(($currentTime - $_SESSION['first_attempt_time']) * 1000, 2); // CONVERT TO SEC

        if ($cooldownDuration >= 60) {
            // RESET EACH MINUTE AFTER FIRST LOGIN ATTEMPT
            $_SESSION['first_attempt_time'] = microtime(true);
            $_SESSION['login_attempts'] = 1;
        } else {
            if ($_SESSION['login_attempts'] > $maxLoginEachMinute) {
                // ATTEMPT LOGIN MORE THAN MAX LIMIT LOGIN EACH MINUTE
                $checkLogin = false;
                $message = 'Login attempt exceed limit, please try again in a moment. ' . $cooldownDuration;

                // Brute force detection
                error_log("Brute force detected, IP : " . get_client_ip());
            }
        }
    }

    if ($checkLogin) {
        if ($username === 'admin' && $password === 'admin123') {
            $message = "Login successful!";
            
            // Successful login logging
            error_log("Successfully login for user : $username, with IP : " . get_client_ip());
        } else {
            $message = "Invalid credentials. Attempt " . $_SESSION['login_attempts'];

            // Failed login logging
            error_log("Failed login for user : $username, with IP : " . get_client_ip());

            // No alerting system
        }
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
                                <li class="breadcrumb-item active">Lab 1</li>
                            </ol>
                        </nav>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h2">Lab 1: Missing Security Logs</h1>
                            <span class="lab-difficulty difficulty-easy">Easy</span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Unmonitored Login System</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($message): ?>
                                    <div class="alert alert-info" role="alert">
                                        <?php echo $message; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="post">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username:</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password:</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </form>
                                
                                <div class="mt-4">
                                    <div class="alert alert-warning" role="alert">
                                        <h5>🚨 Security Issues Detected:</h5>
                                        <ul class="mb-0">
                                            <li>No failed login attempt logging</li>
                                            <li>No brute force detection</li>
                                            <li>No security event monitoring</li>
                                            <li>No audit trail generation</li>
                                            <li>No alerting mechanisms</li>
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
                                    <li>✅ Identify missing logs</li>
                                    <li>✅ Perform undetected attacks</li>
                                    <li>✅ Understand monitoring gaps</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">🛠️ Attack Scenarios</h5>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="scenariosAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#brute">
                                                Brute Force
                                            </button>
                                        </h2>
                                        <div id="brute" class="accordion-collapse collapse" data-bs-parent="#scenariosAccordion">
                                            <div class="accordion-body">
                                                Multiple failed attempts go undetected and unlogged
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#credentials">
                                                Test Credentials
                                            </button>
                                        </h2>
                                        <div id="credentials" class="accordion-collapse collapse" data-bs-parent="#scenariosAccordion">
                                            <div class="accordion-body">
                                                admin / admin123
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