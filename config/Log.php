<?php
namespace Config;

class Log
{
    // Path to your log file
    protected static $logFile = __DIR__.'../../storage/logs/app.log';

    /**
     * Write log message with a given level
     */
    protected static function write(string $level, $message)
    {
        // Ensure log directory and file exist
        // $logDir = dirname(static::$logFile);
        // if (!is_dir($logDir)) {
        //     mkdir($logDir, 0755, true);
        // }
        // if (!file_exists(static::$logFile)) {
        //     touch(static::$logFile);
        // }
        
        $time = date('Y-m-d h:i:s');
        // Convert array/object to string
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        
        $logMessage = "[$time][$level] $message" . PHP_EOL;
        // Append to log file
        file_put_contents(static::$logFile, $logMessage, FILE_APPEND);
    }

    // PSR-3 log levels
    public static function emergency($message) 
    { 
        static::write('EMERGENCY', $message); 
    }

    public static function alert($message)     
    { 
        static::write('ALERT', $message); 
    }

    public static function critical($message)  
    { 
        static::write('CRITICAL', $message); 
    }

    public static function error($message)     
    { 
        static::write('ERROR', $message); 
    }

    public static function warning($message)   
    { 
        static::write('WARNING', $message); 
    }

    public static function notice($message)    
    { 
        static::write('NOTICE', $message); 
    }

    public static function info($message)      
    { 
        static::write('INFO', $message); 
    }

    public static function debug($message)     
    { 
        static::write('DEBUG', $message); 
    }
    /**
     * Optional: Set custom log file
     */
    public static function setLogFile(string $filePath)
    {
        static::$logFile = $filePath;
    }
}
