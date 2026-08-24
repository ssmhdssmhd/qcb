<?php

define('APP_NAME', 'QCB 在线更新系统');
define('APP_VERSION', '1.2.0');
define('DATA_DIR', __DIR__ . '/data');
define('NODES_FILE', DATA_DIR . '/nodes.json');
define('UPDATE_CACHE_FILE', DATA_DIR . '/update_cache.json');
define('SPEED_TEST_TIMEOUT', 10);
define('DEFAULT_GITHUB_REPO', 'ssmhdssmhd/qcb');
define('DEFAULT_GITHUB_BRANCH', 'KF2');
define('SPEED_TEST_ROUNDS', 3);
define('MIN_RESPONSE_THRESHOLD', 5000);

date_default_timezone_set('Asia/Shanghai');
