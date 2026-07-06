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
                    ]
                );
            } else {
                $dbPath = $this->config['sqlite_path'] ?? __DIR__ . '/data.db';
                $this->pdo = new PDO('sqlite:' . $dbPath);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->pdo->exec('PRAGMA journal_mode=WAL;');
                $this->pdo->exec('PRAGMA foreign_keys=ON;');
            }
        } catch (Exception $e) {
            throw new Exception('数据库连接失败: ' . $e->getMessage());
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

        $this->pdo->beginTransaction();
        try {
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if (!empty($stmt)) {
                    $this->pdo->exec($stmt);
                }
            }
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
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
        foreach ($whereParams as $k => $v) {
            $params[$k] = $v;
        }
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $setParts),
            $where
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
