<?php

use Config\Log;

/**
 * Handle PHP errors (warnings, notices, deprecated, etc.)
 */
set_error_handler(function ($severity, $message, $file, $line) {
    $error = [
        'type'    => $severity,
        'message' => $message,
        'file'    => $file,
        'line'    => $line
    ];

    Log::error($error);
});

/**
 * Handle uncaught exceptions
 */
set_exception_handler(function (Throwable $e) {
    Log::critical($e);
});

/**
 * Handle fatal errors on shutdown
 */
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null) {
        Log::critical([
            'type'    => $error['type'],
            'message' => $error['message'],
            'file'    => $error['file'],
            'line'    => $error['line']
        ]);
    }
});
