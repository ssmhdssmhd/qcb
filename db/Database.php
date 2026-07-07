<?php
/**
 * 数据库管理类
 * 支持 MySQL 和 SQLite，统一封装常用操作
 */

class Database {
    private static $instance = null;
    private $pdo;
    private $dbType;
    private $config;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct($config = null) {
        if ($config === null) {
            $config = $this->loadConfig();
        }
        $this->config = $config;
        $this->dbType = $config['type'] ?? 'sqlite';
        $this->connect();
    }

    private function loadConfig() {
        $configFile = __DIR__ . '/db_config.php';
        if (file_exists($configFile)) {
            return require $configFile;
        }
        return [
            'type' => 'sqlite',
            'sqlite_path' => __DIR__ . '/data.db',
            'mysql_host' => '127.0.0.1',
            'mysql_port' => 3306,
            'mysql_dbname' => 'm3u8_ad',
            'mysql_username' => 'root',
            'mysql_password' => '',
            'mysql_charset' => 'utf8mb4',
        ];
    }

    private function connect() {
        try {
            if ($this->dbType === 'mysql') {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $this->config['mysql_host'] ?? '127.0.0.1',
                    $this->config['mysql_port'] ?? 3306,
                    $this->config['mysql_dbname'] ?? 'm3u8_ad',
                    $this->config['mysql_charset'] ?? 'utf8mb4'
                );
                $this->pdo = new PDO(
                    $dsn,
                    $this->config['mysql_username'] ?? 'root',
                    $this->config['mysql_password'] ?? '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_TIMEOUT => 10,
                    ]
                );
            } else {
                $dbPath = $this->config['sqlite_path'] ?? __DIR__ . '/data.db';
                $dbDir = dirname($dbPath);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0755, true);
                }
                $this->pdo = new PDO('sqlite:' . $dbPath);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->pdo->exec('PRAGMA journal_mode=WAL;');
                $this->pdo->exec('PRAGMA foreign_keys=ON;');
            }
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            $friendlyMsg = $this->getFriendlyError($errorMsg, $this->dbType, $this->config);
            throw new Exception($friendlyMsg, 0, $e);
        }
    }

    private function getFriendlyError($errorMsg, $dbType, $config) {
        if ($dbType === 'mysql') {
            $host = $config['mysql_host'] ?? '127.0.0.1';
            $port = $config['mysql_port'] ?? 3306;
            $user = $config['mysql_username'] ?? 'root';
            $dbname = $config['mysql_dbname'] ?? 'm3u8_ad';

            if (strpos($errorMsg, 'Access denied') !== false) {
                return "MySQL 连接失败：用户名或密码错误。请检查用户名（{$user}）和密码是否正确。";
            }
            if (strpos($errorMsg, 'Connection refused') !== false || strpos($errorMsg, 'HY000') !== false && strpos($errorMsg, '2002') !== false) {
                return "MySQL 连接失败：无法连接到服务器 {$host}:{$port}。请检查 MySQL 服务是否启动，以及主机和端口是否正确。";
            }
            if (strpos($errorMsg, 'Unknown database') !== false) {
                return "MySQL 连接失败：数据库「{$dbname}」不存在。请先创建数据库或检查数据库名是否正确。";
            }
            if (strpos($errorMsg, 'No such file or directory') !== false || strpos($errorMsg, 'could not find driver') !== false) {
                return "MySQL 连接失败：PHP 未安装 MySQL 扩展（pdo_mysql）。请在 php.ini 中启用 extension=pdo_mysql。";
            }
            if (strpos($errorMsg, 'getaddrinfo failed') !== false || strpos($errorMsg, 'php_network_getaddresses') !== false) {
                return "MySQL 连接失败：主机名「{$host}」无法解析。请检查主机地址是否正确。";
            }
            if (strpos($errorMsg, 'timeout') !== false || strpos($errorMsg, 'Timeout') !== false) {
                return "MySQL 连接超时：服务器 {$host}:{$port} 响应超时。请检查网络连接和防火墙设置。";
            }
            return "MySQL 连接失败：{$errorMsg}";
        } else {
            $path = $config['sqlite_path'] ?? '';
            if (strpos($errorMsg, 'could not find driver') !== false) {
                return "SQLite 连接失败：PHP 未安装 SQLite 扩展（pdo_sqlite）。请在 php.ini 中启用 extension=pdo_sqlite。";
            }
            if (strpos($errorMsg, 'permission denied') !== false || strpos($errorMsg, 'Permission denied') !== false) {
                return "SQLite 连接失败：数据库文件「{$path}」无读写权限。请检查目录和文件权限。";
            }
            if (strpos($errorMsg, 'No such file') !== false || strpos($errorMsg, 'unable to open') !== false) {
                return "SQLite 连接失败：数据库文件路径不存在或无法创建。请检查路径「{$path}」是否正确。";
            }
            return "SQLite 连接失败：{$errorMsg}";
        }
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function getDbType() {
        return $this->dbType;
    }

    public function initTables() {
        $sqlFile = __DIR__ . '/schema_' . $this->dbType . '.sql';
        if (!file_exists($sqlFile)) {
            $sqlFile = __DIR__ . '/schema.sql';
        }
        if (!file_exists($sqlFile)) {
            throw new Exception('数据库 schema 文件不存在: ' . $sqlFile);
        }

        $sql = file_get_contents($sqlFile);
        $statements = $this->splitSqlStatements($sql);

        $createTableStmts = [];
        $createIndexStmts = [];
        $otherStmts = [];

        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if (empty($stmt)) continue;

            if (preg_match('/CREATE TABLE\s+IF NOT EXISTS/i', $stmt)) {
                $createTableStmts[] = $stmt;
            } elseif (preg_match('/CREATE INDEX\s+IF NOT EXISTS/i', $stmt)) {
                $createIndexStmts[] = $stmt;
            } elseif (!preg_match('/^--/', $stmt) && !preg_match('/^\/\*/', $stmt)) {
                $otherStmts[] = $stmt;
            }
        }

        try {
            foreach ($createTableStmts as $stmt) {
                try {
                    $this->pdo->exec($stmt);
                } catch (Exception $e) {
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        error_log('[DB] 创建表失败: ' . $e->getMessage());
                    }
                }
            }

            $this->migrateTables();
            $this->migrateColumns();

            foreach ($createIndexStmts as $stmt) {
                try {
                    $this->pdo->exec($stmt);
                } catch (Exception $e) {
                    error_log('[DB] 创建索引失败（忽略）: ' . $e->getMessage());
                }
            }

            $transactionStarted = false;
            foreach ($otherStmts as $stmt) {
                try {
                    if (!$transactionStarted) {
                        $this->pdo->beginTransaction();
                        $transactionStarted = true;
                    }
                    $this->pdo->exec($stmt);
                } catch (Exception $e) {
                    error_log('[DB] 执行初始化语句失败（忽略）: ' . $e->getMessage());
                }
            }
            if ($transactionStarted) {
                try {
                    $this->pdo->commit();
                } catch (Exception $e) {
                    try { $this->pdo->rollBack(); } catch (Exception $re) {}
                }
            }

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function migrateTables() {
        $expectedTables = [
            'sys_config',
            'domain_rules',
            'resource_sites',
            'official_sites',
            'proxies',
            'official_platforms',
            'auto_learn_logs',
            'player_config',
            'm3u8_analysis_cache',
            'ad_signatures',
            'official_replace_cache',
            'domain_analysis_stats',
        ];

        foreach ($expectedTables as $table) {
            if (!$this->tableExists($table)) {
                error_log('[DB Migration] 创建缺失的表: ' . $table);
                $this->createTableByName($table);
            }
        }
    }

    private function createTableByName($table) {
        $schemaFile = __DIR__ . '/schema_' . $this->dbType . '.sql';
        if (!file_exists($schemaFile)) {
            $schemaFile = __DIR__ . '/schema.sql';
        }
        if (!file_exists($schemaFile)) return;

        $sql = file_get_contents($schemaFile);
        $pattern = '/CREATE TABLE\s+IF NOT EXISTS\s+[`"]?' . preg_quote($table, '/') . '[`"]?\s*\((?:[^()]|(?R))*\)/s';
        if (preg_match($pattern, $sql, $matches)) {
            try {
                $this->pdo->exec($matches[0]);
                error_log('[DB Migration] 表创建成功: ' . $table);
            } catch (Exception $e) {
                error_log('[DB Migration] 创建表失败 ' . $table . ': ' . $e->getMessage());
            }
        }
    }

    private function migrateColumns() {
        $expectedColumns = $this->getExpectedColumns();

        foreach ($expectedColumns as $table => $columns) {
            if (!$this->tableExists($table)) continue;

            $existingColumns = $this->getTableColumns($table);
            $existingNames = array_map('strtolower', array_keys($existingColumns));

            foreach ($columns as $colName => $colDef) {
                if (!in_array(strtolower($colName), $existingNames)) {
                    error_log('[DB Migration] 添加缺失的列: ' . $table . '.' . $colName);
                    $this->addColumn($table, $colName, $colDef);
                }
            }
        }
    }

    private function getExpectedColumns() {
        $isMysql = $this->dbType === 'mysql';
        $tinyint = $isMysql ? 'TINYINT(1)' : 'INTEGER';
        $decimal = $isMysql ? 'DECIMAL(10,3)' : 'REAL';
        $decimal52 = $isMysql ? 'DECIMAL(5,2)' : 'REAL';
        $text = 'TEXT';
        $int = 'INT';
        $varchar191 = $isMysql ? 'VARCHAR(191)' : 'TEXT';
        $varchar1000 = $isMysql ? 'VARCHAR(1000)' : 'TEXT';
        $varchar64 = $isMysql ? 'VARCHAR(64)' : 'TEXT';
        $timestamp = $isMysql ? 'TIMESTAMP NULL DEFAULT NULL' : 'DATETIME';
        $timestampDef = $isMysql ? 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP' : 'DATETIME DEFAULT CURRENT_TIMESTAMP';

        return [
            'm3u8_analysis_cache' => [
                'url_hash' => $varchar64 . ' NOT NULL',
                'url' => $varchar1000 . ' NOT NULL',
                'domain' => $varchar191 . " DEFAULT ''",
                'media_url' => $varchar1000 . " DEFAULT ''",
                'total_segments' => $int . ' DEFAULT 0',
                'ad_segments' => $int . ' DEFAULT 0',
                'kept_segments' => $int . ' DEFAULT 0',
                'original_duration' => $decimal . ' DEFAULT 0',
                'filtered_duration' => $decimal . ' DEFAULT 0',
                'saved_duration' => $decimal . ' DEFAULT 0',
                'ad_percentage' => $decimal52 . ' DEFAULT 0',
                'duration_rules' => $text,
                'discontinuity_rules' => $text,
                'sequence_jump_rules' => $text,
                'filename_patterns' => $text,
                'ad_signatures' => $text,
                'stats' => $text,
                'fast_mode' => $tinyint . ' DEFAULT 0',
                'safeguard_triggered' => $tinyint . ' DEFAULT 0',
                'expires_at' => $timestamp,
            ],
            'domain_rules' => [
                'marker_detection' => $text,
                'confidence' => $text,
                'enable_marker_detection' => $tinyint . ' DEFAULT 0',
                'history_stats' => $text,
            ],
            'ad_signatures' => [
                'domain' => $varchar191 . ' NOT NULL',
                'signature_type' => ($isMysql ? 'VARCHAR(50)' : 'TEXT') . ' NOT NULL',
                'signature_value' => ($isMysql ? 'VARCHAR(500)' : 'TEXT') . ' NOT NULL',
                'weight' => $int . ' DEFAULT 30',
                'hit_count' => $int . ' DEFAULT 1',
                'confidence' => $int . ' DEFAULT 50',
                'first_seen' => $timestamp,
                'last_seen' => $timestamp,
                'status' => $tinyint . ' DEFAULT 1',
            ],
            'official_replace_cache' => [
                'original_url_hash' => $varchar64 . ' NOT NULL',
                'original_url' => $varchar1000 . ' NOT NULL',
                'platform' => ($isMysql ? 'VARCHAR(100)' : 'TEXT') . " DEFAULT ''",
                'video_title' => ($isMysql ? 'VARCHAR(500)' : 'TEXT') . " DEFAULT ''",
                'base_title' => ($isMysql ? 'VARCHAR(500)' : 'TEXT') . " DEFAULT ''",
                'season_num' => $int . ' DEFAULT NULL',
                'episode_num' => $int . ' DEFAULT NULL',
                'm3u8_url' => $varchar1000 . " DEFAULT ''",
                'match_score' => $decimal52 . ' DEFAULT 0',
                'site' => ($isMysql ? 'VARCHAR(100)' : 'TEXT') . " DEFAULT ''",
                'video_info' => $text,
                'status' => $tinyint . ' DEFAULT 1',
                'expires_at' => $timestamp,
            ],
            'domain_analysis_stats' => [
                'domain' => $varchar191 . ' NOT NULL',
                'analyze_count' => $int . ' DEFAULT 0',
                'learn_count' => $int . ' DEFAULT 0',
                'total_segments_analyzed' => $int . ' DEFAULT 0',
                'total_ads_detected' => $int . ' DEFAULT 0',
                'avg_ad_percentage' => $decimal52 . ' DEFAULT 0',
                'last_analyze_time' => $timestamp,
                'last_learn_time' => $timestamp,
            ],
        ];
    }

    private function getTableColumns($table) {
        $columns = [];
        if ($this->dbType === 'mysql') {
            $dbname = $this->config['mysql_dbname'] ?? 'm3u8_ad';
            $stmt = $this->pdo->prepare(
                'SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, IS_NULLABLE
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?'
            );
            $stmt->execute([$dbname, $table]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columns[$row['COLUMN_NAME']] = $row;
            }
        } else {
            $stmt = $this->pdo->query('PRAGMA table_info(' . $table . ')');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $columns[$row['name']] = $row;
            }
        }
        return $columns;
    }

    private function addColumn($table, $columnName, $columnDef) {
        try {
            $sql = sprintf('ALTER TABLE %s ADD COLUMN %s %s', $table, $columnName, $columnDef);
            $this->pdo->exec($sql);
            return true;
        } catch (Exception $e) {
            error_log('[DB Migration] 添加列失败: ' . $table . '.' . $columnName . ' - ' . $e->getMessage());
            return false;
        }
    }

    private function splitSqlStatements($sql) {
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
        $parenDepth = 0;
        $length = strlen($sql);

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            if ($inString) {
                $current .= $char;
                if ($char === $stringChar && ($i === 0 || $sql[$i - 1] !== '\\')) {
                    $inString = false;
                }
            } else {
                if ($char === '"' || $char === "'") {
                    $inString = true;
                    $stringChar = $char;
                    $current .= $char;
                } elseif ($char === '(') {
                    $parenDepth++;
                    $current .= $char;
                } elseif ($char === ')') {
                    $parenDepth--;
                    $current .= $char;
                } elseif ($char === ';' && $parenDepth === 0) {
                    $statements[] = $current;
                    $current = '';
                } else {
                    $current .= $char;
                }
            }
        }

        if (trim($current) !== '') {
            $statements[] = $current;
        }

        return $statements;
    }

    public function tableExists($tableName) {
        if ($this->dbType === 'mysql') {
            $dbname = $this->config['mysql_dbname'] ?? 'm3u8_ad';
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?'
            );
            $stmt->execute([$dbname, $tableName]);
        } else {
            $stmt = $this->pdo->prepare(
                'SELECT COUNT(*) FROM sqlite_master WHERE type = "table" AND name = ?'
            );
            $stmt->execute([$tableName]);
        }
        return $stmt->fetchColumn() > 0;
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function queryOne($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function execute($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($f) { return ':' . $f; }, $fields);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        $params = [];
        foreach ($data as $k => $v) {
            $params[':' . $k] = $v;
        }
        $this->execute($sql, $params);
        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = []) {
        $setParts = [];
        $params = [];
        foreach ($data as $k => $v) {
            $setParts[] = $k . ' = :set_' . $k;
            $params[':set_' . $k] = $v;
        }
        // 将 WHERE 中的 ? 替换为命名参数，避免混合命名和位置参数
        $whereParamIndex = 0;
        $whereReplaced = preg_replace_callback('/\?/', function() use (&$whereParamIndex) {
            return ':where_' . ($whereParamIndex++);
        }, $where);
        foreach ($whereParams as $k => $v) {
            if (is_int($k)) {
                $params[':where_' . $k] = $v;
            } else {
                $params[$k] = $v;
            }
        }
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $setParts),
            $whereReplaced
        );
        return $this->execute($sql, $params);
    }

    public function delete($table, $where, $params = []) {
        $sql = sprintf('DELETE FROM %s WHERE %s', $table, $where);
        return $this->execute($sql, $params);
    }

    public function count($table, $where = '1=1', $params = []) {
        $sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $table, $where);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }
}
