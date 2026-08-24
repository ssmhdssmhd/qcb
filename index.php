<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/AccelerationNodeManager.php';
require_once __DIR__ . '/src/UpdateManager.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'api':
        include __DIR__ . '/api.php';
        break;
    case 'admin':
    case '':
    default:
        header('Location: admin.php');
        exit;
}
