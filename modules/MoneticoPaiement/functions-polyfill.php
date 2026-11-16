<?php

if (!function_exists('dd')) {
    /**
     * @param mixed ...$args
     *
     * @return never
     */
    function dd(...$args): void
    {
        $callingFunction = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0] ?? null;
        if ($callingFunction !== null) {
            echo '<strong>Calling code: ' . $callingFunction['file'] . ':' . $callingFunction['line'] . '</strong> <br>';
        }

        echo '<pre>';
        var_dump(...$args);
        echo '</pre>';
        exit;
    }
}