<?php

require_once __DIR__ . '/vendor/autoload.php';

\Sentry\init([
    'dsn' => 'https://b01cc6b7b57ccc9273c385fdb0d17fb7@o4509604031299584.ingest.de.sentry.io/4510137777979472',
    'send_default_pii' => true,
    'traces_sample_rate' => 1.0,
    'enable_logs' => true,
]);

// Capture all PHP errors automatically
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
    // Only capture if error reporting is enabled for this error type
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // Capture the error as an exception
    \Sentry\captureLastError();

    // Return false to continue with PHP's internal error handler
    return false;
});

// Capture uncaught exceptions
set_exception_handler(function (\Throwable $exception) {
    \Sentry\captureException($exception);

    // Re-throw the exception so PHP can handle it normally
    throw $exception;
});
