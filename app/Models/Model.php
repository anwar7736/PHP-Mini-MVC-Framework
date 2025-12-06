<?php
namespace App\Models;
use Config\App;
use Config\Database;

abstract class Model 
{
    public $db = "";
    protected $table;
    protected $select = "*";
    protected $join;
    protected $groupBy;
    protected $orderBy;
    protected $limit;
    protected $offset;
    protected $conditions;
    protected $bindings;
    public static $instance;
    public function __construct()
    {
        $this->db = getDBConnection();
        return $this->db;
    }

    public static function getModelInstance()
    {
        $object = self::$instance = self::$instance ?? new static;
        $object->table = $object->getTableName($object);
        return $object;
    }

    public static function destroyInstance() 
    {
        self::$instance = null;
    }

    protected function tableExists(string $table): bool
    {
        $stmt = $this->db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        return (bool) $stmt->fetchColumn();
    }

    public function getTableName($object): string
    {
        if (isset($object->table)) {
            return $object->table;
        }

        // Default table name based on class
        $className = (new \ReflectionClass($object))->getShortName();
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));

        // Try plural first
        $plural = $snake . 's';

        if ($this->tableExists($plural)) {
            return $plural;
        }

        // Fallback to singular
        if ($this->tableExists($snake)) {
            return $snake;
        }

        throw new \Exception("Table not found for model $className");
    }

    public static function where($column, $operator = null, $value = null, $separator = "AND")
    {
        $ob = self::$instance = self::$instance ?? new static;

        if (!$ob->conditions) {
            $ob->conditions = "WHERE";
        } else {
            $ob->conditions .= " $separator";
        }
        // dd($ob->conditions);

        if (is_array($column)) {
            $counter = 0;
            foreach ($column as $col => $val) {
                if ($counter == 0) {
                    $ob->conditions .= " $col = ?";
                } else {
                    $ob->conditions .= " AND $col = ?";
                }
                $ob->bindings[] = "$val";
                $counter++;
            }
        } else {
            if (isset($value)) 
            {
                $ob->conditions .= " $column $operator ?";
                $ob->bindings[] = $value;
            } else {
                if (in_array($operator, ["IS NULL", "IS NOT NULL"])) 
                {
                    $ob->conditions .= " $column $operator";
                } else {
                    $ob->conditions .= " $column = ?";
                    $ob->bindings[] = $operator;
                }
            }
        }
        // dd($ob->conditions);
        return $ob;
    }
    
    public static function orWhere($column, $operator = null, $value = null, $separator = "OR")
    {
        static::where($column, $operator, $value, $separator);
        return new static;
    }

    public static function whereNull($column)
    {
        static::where($column, "IS NULL", "");
        return new static;
    }

    public static function whereNotNull($column)
    {
        static::where($column, "IS NOT NULL", "");
        return new static;
    }

    public static function whereIn($column, $value)
    {
        static::where($column, " IN", "(".join(",",$value).")");
        return new static;
    }

    public static function whereNotIn($column, $value = [])
    {
        static::where($column, " NOT IN", "(".join(",",$value).")");
        return new static;
    }

    public static function whereBetween($column, $value = [])
    {
        static::where($column, " BETWEEN ", $value[0]." AND $value[1]");
        return new static;
    }

    public static function whereColumn($column1, $column2)
    {
        static::where($column1, $column2);
        return new static;
    }

    public static function orWhereColumn($column1, $column2)
    {
        static::where($column1, "<>", $column2);
        return new static;
    }

    public static function whereDate($column, $value)
    {
        static::where("DATE($column)", $value);
        return new static;
    }

    public static function orWhereDate($column, $value)
    {
        static::where("DATE($column)", $value, "", "OR");
        return new static;
    }

    public static function whereMonth($column, $value)
    {
        static::where("MONTH($column)", $value);
        return new static;
    }

    public static function orWhereMonth($column, $value)
    {
        static::where("MONTH($column)", $value, "", "OR");
        return new static;
    }

    public static function whereYear($column, $value)
    {
        static::where("YEAR($column)", $value);
        return new static;
    }

    public static function orWhereYear($column, $value)
    {
        static::where("YEAR($column)", "=", $value, "OR");
        return new static;
    }

    public static function whereExists()
    {
        $count = static::count();
        $status = false;
        if($count > 0)
        {
            $status = true;
        }
        return $status;
    }

    public static function count($column = "*")
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->table = $ob->getTableName($ob);
        $query = "SELECT COUNT($column) AS count_result FROM $ob->table $ob->conditions";
        $data = $ob->db->query($query, $ob->bindings)->find();
        $value = 0;
        if ($data && isset($data->count_result)) 
        {
            $value = (int) $data->count_result;
        }

        return  $value;
    }

    public static function sum($column)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->table = $ob->getTableName($ob);
        $query = "SELECT ROUND(SUM($column), 2) AS total FROM $ob->table $ob->conditions";
        $data = $ob->db->query($query, $ob->bindings)->find();
        $value = 0;
        if ($data && isset($data->total)) 
        {
            $value = $data->total;
        }
        
        return $value;
        
    }

    public static function avg($column)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->table = $ob->getTableName($ob);
        $query = "SELECT ROUND(AVG($column), 2) AS avg FROM $ob->table $ob->conditions";
        $data = $ob->db->query($query, $ob->bindings)->find();
        $value = 0;
        if ($data && isset($data->avg)) 
        {
            $value = $data->avg;
        }

        return $value;
    }

    public static function min($column)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->table = $ob->getTableName($ob);
        $query = "SELECT MIN($column) as min FROM $ob->table $ob->conditions";
        $data = $ob->db->query($query, $ob->bindings)->find();
        $value = 0;
        if ($data && isset($data->min)) 
        {
            $value = $data->min;
        }

        return $value;
    }

    public static function max($column)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->table = $ob->getTableName($ob);
        $query = "SELECT MAX($column) AS max FROM $ob->table $ob->conditions";
        $data = $ob->db->query($query, $ob->bindings)->find();
        $value = 0;
        if ($data && isset($data->max)) 
        {
            $value = $data->max;
        }

        return $value;
    }

    public static function groupBy(...$columns)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $columns = implode(", ", $columns);
        $ob->groupBy = "GROUP BY $columns";
        return $ob;
    }

    public static function having($column, $operator, $value)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->where($column, $operator, $value, "HAVING");
        return $ob;
    }

    public static function orderBy($column, $order_by = "ASC")
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->orderBy = "ORDER BY $column $order_by";
        return $ob;

    }

    public static function orderByAsc($column)
    {
        static::orderBy($column);
        return new static;

    }

    public static function orderByDesc($column)
    {
        static::orderBy($column, "DESC");
        return new static;

    }

    public static function latest($column = 'created_at')
    {
        static::orderBy($column);
        return new static;

    }

    public static function limit($value)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->limit = "LIMIT $value";
        return $ob;
    }

    public static function offset($value)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->offset = "OFFSET $value";
        return $ob;
    }

    public static function take($value)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->limit($value);
        return $ob;
    }

    public static function skip($value)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->offset($value);
        return $ob;
    }

    public static function select(...$columns)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->select = implode(", ", $columns);
        return $ob;
    }

    public static function join($table, $condition1, $operator, $condition2 = "", $joinText = "JOIN")
    {
        $ob = self::$instance = self::$instance ?? new static;
        $op = "=";
        $con2 = $operator;
        if($condition2)
        {
            $con2 = $condition2;
        }

        if($ob->join)
        {
            $joinText = " $joinText";
        }
        
        $ob->join .= "$joinText $table ON $condition1 $op $con2";
        return $ob;
    }

    public static function innerJoin($table, $condition1, $operator, $condition2 = "")
    {
        static::join($table, $condition1, $operator, $condition2, "INNER JOIN");
        return new static;
    }
    
    public static function leftJoin($table, $condition1, $operator, $condition2 = "")
    {
        static::join($table, $condition1, $operator, $condition2, "LEFT JOIN");
        return new static;
    }

    public static function rightJoin($table, $condition1, $operator, $condition2 = "")
    {
        static::join($table, $condition1, $operator, $condition2, "RIGHT JOIN");
        return new static;
    }

    public static function crossJoin($table, $condition1, $operator, $condition2 = "")
    {
        static::join($table, $condition1, $operator, $condition2, "CROSS JOIN");
        return new static;
    }

    public static function fullJoin($table, $condition1, $operator, $condition2 = "")
    {
        static::join($table, $condition1, $operator, $condition2, "FULL OUTER JOIN");
        return new static;
    }

    public static function get()
    {
        $ob = static::getModelInstance();
        $query = "SELECT $ob->select FROM $ob->table $ob->join $ob->conditions 
                  $ob->groupBy $ob->orderBy $ob->limit $ob->offset";
        return $ob->db->query($query, $ob->bindings)->get();
    }

    public static function all()
    {
       $ob = self::$instance = self::$instance ?? new static;
       $query = "SELECT * FROM $ob->table";
       return $ob->db->query($query)->get();
    }

    public static function toSql()
    {
        $ob = self::$instance = self::$instance ?? new static;
        $query = "SELECT $ob->select FROM $ob->table $ob->join $ob->conditions 
                  $ob->groupBy $ob->orderBy $ob->limit $ob->offset";
        return trim($query, implode(",", $ob->bindings));
    }

    public function first()
	{
        $ob = self::$instance = self::$instance ?? new static;
        $query = "SELECT $ob->select FROM $ob->table $ob->join $ob->conditions 
                  $ob->groupBy $ob->orderBy $ob->limit $ob->offset";
        return $ob->db->query($query, $ob->bindings)->find();
	}
	
	public static function find($id)
	{
        $ob = self::$instance = self::$instance ?? new static;
        if (!$ob->conditions) {
            $ob->conditions = "WHERE id = ?";
        }
        else $ob->conditions .= " AND id = ?";
        $ob->bindings[] = $id;
        $query = "SELECT $ob->select FROM $ob->table $ob->join  
                  $ob->conditions $ob->groupBy $ob->orderBy $ob->limit $ob->offset";
        return $ob->db->query($query, $ob->bindings)->find();
	}
	
	public static function findOrFail($id)
	{
        $ob = self::$instance = self::$instance ?? new static;
        if (!$ob->conditions) {
            $ob->conditions = "WHERE id = ?";
        }
        else $ob->conditions .= " AND id = ?";
        $ob->bindings[] = $id;
        $query = "SELECT $ob->select FROM $ob->table $ob->join  
                  $ob->conditions $ob->groupBy $ob->orderBy $ob->limit $ob->offset";
        return $ob->db->query($query, $ob->bindings)->findOrFail();
	}
	
    public static function create($data = [])
    {
        $ob = self::$instance = self::$instance ?? new static;

        $columns = [];
        $placeholders = [];
        $ob->bindings = [];

        foreach ($data as $key => $value) {
            $columns[] = $key;
            $placeholders[] = '?';
            $ob->bindings[] = trim($value);
        }

        $columnsSQL = '(' . implode(',', $columns) . ')';
        $valuesSQL  = 'VALUES(' . implode(',', $placeholders) . ')';

        $query = "INSERT INTO {$ob->table} {$columnsSQL} {$valuesSQL}";

        // Run the insert
        $ob->db->query($query, $ob->bindings);

        // Get inserted ID
        $id = $ob->db->lastInsertId();

        // Return the newly inserted row
        return $ob->find($id);
    }
	
	public static function update($data = [])
	{
        $ob = self::$instance = self::$instance ?? new static;
        $query1 = "";
        $bindings = [];
        foreach ($data as $key => $value) 
        {
            $value = trim($value);
            $query1 .= " $key = ?, ";
            $bindings[] = "$value";
        }
        $bindings = array_merge($bindings, $ob->bindings);
        $query1 = rtrim($query1, ", ");
        $query = "UPDATE $ob->table SET $query1 $ob->conditions";
		return $ob->db->query($query, $bindings);
	}

	public static function delete()
	{
        $ob = self::$instance = self::$instance ?? new static;
        $query = "DELETE FROM $ob->table $ob->conditions";
		return $ob->db->query($query, $ob->bindings);
	}
    
}
	

