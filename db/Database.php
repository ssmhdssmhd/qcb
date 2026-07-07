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

        $transactionStarted = false;
        try {
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if (empty($stmt)) continue;
                
                if (preg_match('/CREATE TABLE\s+IF NOT EXISTS/i', $stmt)) {
                    try {
                        $this->pdo->exec($stmt);
                    } catch (Exception $e) {
                        if (!strpos($e->getMessage(), 'already exists')) {
                            throw $e;
                        }
                    }
                } else {
                    if (!$transactionStarted) {
                        $this->pdo->beginTransaction();
                        $transactionStarted = true;
                    }
                    $this->pdo->exec($stmt);
                }
            }
            if ($transactionStarted) {
                $this->pdo->commit();
            }
            return true;
        } catch (Exception $e) {
            if ($transactionStarted) {
                try {
                    $this->pdo->rollBack();
                } catch (Exception $rollbackEx) {
                }
            }
            throw $e;
        }
    }

    private function splitSqlStatements($sql) {
        $statements = [];
        $current = '';
        $inString = false;
        $stringChar = '';
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
                } elseif ($char === ';') {
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
