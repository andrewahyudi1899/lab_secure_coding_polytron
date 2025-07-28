<?php
    if (isset($_GET['cmd'])) {
        $command = $_GET['cmd'] ?? '';

        echo "Run command $command";
    } else {
        echo "No command is executed";
    }
?>