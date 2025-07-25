<?php
$page_title = "Insecure Deserialization Lab 1 - PHP Object Injection";
require_once '../../../config/env.php';
require_once '../../../template/header.php';

$message = '';
$is_admin = false;

// VULNERABLE - Unsafe class for demonstration
class User
{
    public $username;
    public $role;

    public function __construct($username, $role = 'user')
    {
        $this->username = $username;
        $this->role = $role;
    }

    public function __wakeup()
    {
        // VULNERABLE - Magic method that could be exploited
        if ($this->role === 'admin') {
            global $message;
            $message = "Admin access granted through deserialization!";
        }
    }
}

// Magic method : 
// __construct()   : called on object creation
// __destruct()    : called when an object is destroyed
// __wakeup()      : called when an object is unserialize
// __sleep()       : called when an object is serialize
// __toString()    : called when the object is treated as a string
class FileDeleter {
    public $filename;

    public function __construct($filename = '')
    {
        $this->filename = $filename;
    }

    public function __wakeup() {
        if ($this->filename != '') {
            global $message;

            $status = unlink($this->filename);

            $message = 'Delete file from ' . $this->filename . ' with status ' . ($status ? 'SUCCESS' : 'FAILED');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_data = $_POST['user_data'] ?? '';

    if ($user_data) {
        try {
            // VULNERABLE - Unsafe deserialization
            $user = unserialize($user_data);
            if ($user instanceof User) {
                $message = "User object deserialized: " . htmlspecialchars($user->username) . " (Role: " . htmlspecialchars($user->role) . ")";
                if ($user->role == 'admin') {
                    $is_admin = true;
                }
            } else {
                $message = "Invalid user object.";
            }
        } catch (Exception $e) {
            $message = "Deserialization error: " . $e->getMessage();
        }
    }
}

// SCAN CURRENT DIRECTORY
// $files_and_directories = scandir('../');

// if ($files_and_directories !== false) {
//     $message .= 'Contents of the current directory:\n';
//     foreach ($files_and_directories as $item) {
//         $message .= $item . '\n';
//     }
// } else {
//     $message .= 'Failed to scan the current directory.\n';
// }

// Generate sample serialized data
$sample_user = new User('john', 'user');
$serialized_sample = serialize($sample_user);

// Generate dangerous serialized data
$malicious_object = new FileDeleter('../Sampe_Document_For_Insecure_Deserialization.docx');
$serialized_malicious_object = serialize($malicious_object);
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
                                <li class="breadcrumb-item"><a href="../">Insecure Deserialization</a></li>
                                <li class="breadcrumb-item active">Lab 1</li>
                            </ol>
                        </nav>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="h2">Lab 1: PHP Object Injection</h1>
                            <span class="lab-difficulty difficulty-medium">Medium</span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">User Data Processor</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($message): ?>
                                    <div class="alert alert-info" role="alert">
                                        <?php echo $message; ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post">
                                    <div class="mb-3">
                                        <label for="user_data" class="form-label">Serialized User Data:</label>
                                        <textarea class="form-control" id="user_data" name="user_data" rows="4" placeholder="Enter serialized user object..." required></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Process User Data</button>
                                </form>

                                <div class="mt-4">
                                    <h6>Sample Serialized User:</h6>
                                    <code><?php echo htmlspecialchars($serialized_sample); ?></code>
                                </div>

                                <div class="mt-4">
                                    <h6>Sample Malicious Serialized Object:</h6>
                                    <code><?php echo htmlspecialchars($serialized_malicious_object); ?></code>
                                </div>

                                <?php if ($is_admin): ?>
                                    <div class="card">
                                        <div class="card-header bg-danger text-white">
                                            <h5 class="mb-0">🔐 Admin Panel</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-success" role="alert">
                                                <h4 class="alert-heading">Privilege Escalation Successful!</h4>
                                                <p>You have successfully escalated privileges to admin level.</p>
                                            </div>

                                            <h6>Admin Functions:</h6>
                                            <ul>
                                                <li>View all user data</li>
                                                <li>Modify system settings</li>
                                                <li>Access sensitive information</li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
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
                                    <li>✅ Inject malicious objects</li>
                                    <li>✅ Escalate to admin role</li>
                                    <li>✅ Exploit magic methods</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">🛠️ Injection Payloads</h5>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="payloadsAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#admin">
                                                Admin Object
                                            </button>
                                        </h2>
                                        <div id="admin" class="accordion-collapse collapse" data-bs-parent="#payloadsAccordion">
                                            <div class="accordion-body">
                                                <code>O:4:"User":2:{s:8:"username";s:5:"admin";s:4:"role";s:5:"admin";}</code>
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