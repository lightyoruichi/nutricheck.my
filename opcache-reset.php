<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Opcache cleared']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'notice', 'message' => 'Opcache not enabled']);
} 