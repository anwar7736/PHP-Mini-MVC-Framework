<?php
namespace Config;
use App\Models\Model;

class DB extends Model
{
    protected $table = "";
    public function __construct()
    {
        parent::__construct();
    }

    public static function table(string $table)
    {
        $ob = self::$instance = self::$instance ?? new static;
        $ob->table = $table;
        return new static;
    }
    
    public static function statement(string $query)
    {
        $ob = self::$instance = self::$instance ?? new static;
        // dd($query);
		return $ob->db->query($query);
    }

    public static function selectRaw(string $query)
    {
        $ob = self::$instance = self::$instance ?? new static;
        // dd($query);
		return $ob->db->query($query)->get();
    }

    public static function insert(string $query)
    {
        $ob = self::$instance = self::$instance ?? new static;
        // dd($query);
		return $ob->db->raw($query);
    }
    
    public static function raw(string $expression)
    {
        return new RawExpression($expression);
    }
}