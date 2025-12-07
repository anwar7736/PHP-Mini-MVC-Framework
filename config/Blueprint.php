<?php
namespace Config;

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/App.php';
CONST BASE_PATH = __DIR__.'/../';
require_once BASE_PATH.'vendor/autoload.php';
require_once BASE_PATH.'app/helpers/helpers.php';
use Dotenv\Dotenv;
use PDOException;

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

class Blueprint
{
    public $table = "";
    public $db = "";
    private $columns = [];
    private $alters = [];
    private $indexes = [];
    protected $foreignKeys = [];
    protected $lastForeignColumn = null;


    public function __construct($table)
    {
        $this->db = getDBConnection();
        $this->table = $table;
    }

    public function id()
    {
        $this->columns[] = "`id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
    }

    public function string($name, $length = 255)
    {
        $this->columns[] = "`$name` VARCHAR($length) NOT NULL";
        return $this;
    }

    public function tinyInteger($name)
    {
        $this->columns[] = "`$name` TINYINT NOT NULL";
        return $this;
    }

    public function integer($name)
    {
        $this->columns[] = "`$name` INT NOT NULL";
        return $this;
    }

    public function bigInteger($name)
    {
        $this->columns[] = "`$name` BIGINT NOT NULL";
        return $this;
    }

    public function unsignedBigInteger($name)
    {
        $this->columns[] = "`$name` UNSIGNED BIGINT NOT NULL";
        return $this;
    }

    public function boolean($name)
    {
        $this->columns[] = "`$name` TINYINT(1) NOT NULL";
        return $this;
    }

    public function float($name, $total = 8, $places = 2)
    {
        $this->columns[] = "`$name` FLOAT($total, $places) NOT NULL";
        return $this;
    }

    public function double($name, $total = 8, $places = 2)
    {
        $this->columns[] = "`$name` DOUBLE($total, $places) NOT NULL";
        return $this;
    }

    public function decimal($name, $total = 8, $places = 2)
    {
        $this->columns[] = "`$name` DECIMAL($total, $places) NOT NULL";
        return $this;
    }

    public function date($name)
    {
        $this->columns[] = "`$name` DATE NOT NULL";
        return $this;
    }

    public function dateTime($name)
    {
        $this->columns[] = "`$name` DATETIME NOT NULL";
        return $this;
    }

    public function text($name)
    {
        $this->columns[] = "`$name` TEXT NOT NULL";
        return $this;
    }   

    public function longText($name)
    {
        $this->columns[] = "`$name` LONGTEXT NOT NULL";
        return $this;
    }  

    public function fullText($name)
    {
        $this->columns[] = "`$name` FULLTEXT NOT NULL";
        return $this;
    }  

    public function binary($name)
    {
        $this->columns[] = "`$name` BLOB NOT NULL";
        return $this;
    }

    public function enum($name, array $values)
    {
        $enumValues = "'" . implode("','", $values) . "'";
        $this->columns[] = "`$name` ENUM($enumValues) NOT NULL";
        return $this;
    }

    public function json($name)
    {
        $this->columns[] = "`$name` JSON NOT NULL";
        return $this;
    }

    public function jsonb($name)
    {
        $this->columns[] = "`$name` JSON NOT NULL";
        return $this;
    }

    public function time($name)
    {
        $this->columns[] = "`$name` TIME NOT NULL";
        return $this;
    }

    public function year($name)
    {
        $this->columns[] = "`$name` YEAR NOT NULL";
        return $this;
    }

    public function index($column)
    {
        $this->indexes[] .= " INDEX (`{$column}`)";
        return $this;
    }
    
    public function after($column)
    {
        $lastIndex = array_key_last($this->columns);
        $this->columns[$lastIndex] .= " AFTER `$column`";
        return $this;
    }

    public function before($column)
    {
        $lastIndex = array_key_last($this->columns);
        $this->columns[$lastIndex] .= " BEFORE `$column`";
        return $this;
    }

    public function default($value)
    {
        $lastIndex = array_key_last($this->columns);
        if (is_string($value)) {
            $this->columns[$lastIndex] .= " DEFAULT '$value'";
        } else {
            $this->columns[$lastIndex] .= " DEFAULT $value";
        }
        return $this;
    }

    public function timestamp($name)
    {
        $this->columns[] = "`$name` TIMESTAMP NULL";
        return $this;
    }

    public function rememberToken()
    {
        $this->columns[] = "`remember_token` VARCHAR(100) NULL";
    }

    public function timestamps()
    {
        $this->columns[] = "`created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    }

    public function softDeletes()
    {
        $this->columns[] = "`deleted_at` TIMESTAMP NULL DEFAULT NULL";
    }

    public function renameColumn($from, $to)
    {
        $this->alters[] = "RENAME COLUMN $from TO $to";
        return $this;
    }

    public function dropColumn(array|string $columns)
    {
        if(!is_array($columns)){
            $columns = explode(", ", $columns);
        }

        foreach ($columns as $key => $column) {
          $this->alters[] = "DROP COLUMN $column";
        }
        
        return $this;
    }

    public function dropTimestamps()
    {
        $this->dropColumn(['created_at', 'updated_at']);
        return $this;
    }

    public function dropSoftDeletes()
    {
        $this->dropColumn(['deleted_at']);
        return $this;
    }

    public function comments($comments)
    {
        $lastIndex = array_key_last($this->columns);
        $this->columns[$lastIndex] .= " COMMENT '$comments'";
        return $this;
    }

    public function foreignId($name)
    {
        $this->columns[] = "`$name` BIGINT UNSIGNED";
        $this->lastForeignColumn = $name;  // store column for constrained()
        return $this;
    }

    public function constrained($table = null, $column = 'id')
    {
        $col = strtolower($this->lastForeignColumn);

        if (!$table) {
            $table = str_replace('_id', '', $col) . 's';
        }

        $this->foreignKeys[] =
            "FOREIGN KEY (`$col`) REFERENCES `$table`(`$column`)";
        
        return $this;
    }


    public function cascadeOnDelete($action = 'CASCADE')
    {
        $col = $this->lastForeignColumn;
        $lastIndex = array_key_last($this->foreignKeys);
        $this->foreignKeys[$lastIndex] .= " ON DELETE $action";
        return $this;
    }

    public function cascadeOnUpdate($action = 'CASCADE')
    {
        $col = $this->lastForeignColumn;
        $lastIndex = array_key_last($this->foreignKeys);
        $this->foreignKeys[$lastIndex] .= " ON UPDATE $action";
        return $this;
    }

    public function onDelete($action)
    {
        $this->cascadeOnDelete($action);
        return $this;
    }

    public function onUpdate($action)
    {
        $this->cascadeOnUpdate($action);
        return $this;
    }

    public function unique()
    {
        $lastIndex = array_key_last($this->columns);
        $this->columns[$lastIndex] .= " UNIQUE";
        return $this;
    }

    public function nullable()
    {
        $lastIndex = array_key_last($this->columns);
        $this->columns[$lastIndex] = str_replace("NOT NULL", "NULL", $this->columns[$lastIndex]);
        return $this;
    }

    public function execute($action = 'create')
    {
        if ($action === 'create') {
            $parts = $this->columns;

            if (!empty($this->foreignKeys)) {
                $parts = array_merge($parts, $this->foreignKeys);
            }

            $query = "CREATE TABLE IF NOT EXISTS `{$this->table}` (" 
                . implode(", ", $parts) 
                . ") ENGINE=InnoDB;";

        } elseif ($action === 'alter') {
            $alterParts =  array_merge($this->columns, $this->alters, $this->foreignKeys);

            $query = "ALTER TABLE `{$this->table}` " . implode(", ", $alterParts) . ";";

        } elseif ($action === 'drop') {
            try {
                $pdo = $this->db->connection;

                // Disable foreign key checks
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

                // Drop table
                $pdo->exec("DROP TABLE IF EXISTS `{$this->table}`");

                // Re-enable foreign key checks
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

                echo "Table {$this->table} dropped successfully.\n";
            } catch (PDOException $e) {
                echo "Error dropping table {$this->table}: " . $e->getMessage() . "\n";
            }
        }

        // echo($query);
       if(isset($query))  $this->db->query($query);
    }

}
