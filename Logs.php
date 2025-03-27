<?php
namespace CannalLogs;

class Log
{

    private $log_dir;

    private $log_name;

    public function __construct()
    {
        $this->log_dir = $_SERVER['DOCUMENT_ROOT'] . '/application/logs/';
        $this->log_name = date('Y-m-d_H:i:s') . '.log';
    }

    function write($level, $message)
    {
        if ($fp = @fopen($this->log_dir . $this->log_name, "a")) {
            $message = date('Y-m-d H:i:s') . ' [' . strtoupper($level) . '] ' . $message . PHP_EOL;
            fwrite($fp, $message);
            fclose($fp);
            return true;
        }
        return false;
    }

    function get_log_name()
    {
        return str_replace('.log', '', $this->log_name);
    }

    function set_log_name(string $log_name, bool $move_previous = false)
    {
        $log_name_tmp = explode('.', $log_name);
        if (count($log_name_tmp) == 1 || (count($log_name_tmp) > 1 && end($log_name_tmp) != "log")) {
            $log_name = $log_name . '.log';
        }
        unset($log_name_tmp);

        if ($this->log_name = $log_name) {
            return true;
        }

        $old_name = $this->log_name;
        if (file_exists($this->log_dir . $log_name)) {
            $this->log_name = $log_name;
        } else if (mkdir($this->log_dir . $log_name, 0755)) {
            $this->log_name = $log_name;
        }
        if ($this->log_name != $old_name && $move_previous) {
            $fp = fopen($this->log_dir . $this->log_name, "a");
            fwrite($fp, file_get_contents($this->log_dir . $old_name));
            fclose($fp);
            unlink($this->log_dir . $old_name);
            return true;
        } else if ($this->log_name != $old_name) {
            return true;
        }
        return false;
    }
}