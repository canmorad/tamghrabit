<?php
namespace App\Traits;

trait LoggableException
{
    protected function handle(\Throwable $e, $message = "Une erreur technique est survenue.")
    {
        $category = strtoupper(basename(str_replace('\\', '/', get_class($this))));
        $ref = $category . "-" . strtoupper(uniqid());

        
        $details = $e->getMessage();
        if ($e instanceof \PDOException && isset($e->errorInfo)) {
            $info = $e->errorInfo;
            $details .= " | SQL State: " . ($info[0] ?? 'N/A') . " | Custom Code: " . ($info[1] ?? 'N/A');
        }

        $logPath = dirname(__DIR__, 2) . '/logs/errors.log';
        $logEntry = sprintf(
            "[%s] [%s] %s | FILE: %s:%d\nStack Trace: %s\n%s\n",
            $ref,
            date('Y-m-d H:i:s'),
            $details,
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString(),
            str_repeat("-", 80)
        );

        file_put_contents($logPath, $logEntry, FILE_APPEND);

        throw new \Exception("$message (Ref: $ref)");
    }
}