<?php
namespace CannalLogs;

class Logs
{

    private string $log_dir = '';

    private string $log_name = '';

    public function __construct($log_dir = null)
    {
        $this->log_dir = $_SERVER['DOCUMENT_ROOT'] . '/application/logs/';
        if ($log_dir && $this->check_path($log_dir)) {
            return true;
        } else {
            $this->log_name = date('Y-m-d_H:i:s') . '.log';
            return true;
        }
    }

    function check_path(string $path): bool
    {
        if (is_file($path)) {
            return true;
        }

        $expl_dir = explode('/', $path);
        $file_name = array_pop($expl_dir);
        $log_name_tmp = explode('.', $file_name);
        if (count($log_name_tmp) == 1 || (count($log_name_tmp) > 1 && end($log_name_tmp) != "log")) {
            $file_name .= '.log';
        }
        unset($log_name_tmp);

        $point_dir = $this->log_dir;
        if (count($expl_dir) > 1 && ! is_dir(implode('/', $expl_dir))) {
            foreach ($expl_dir as $subdir) {
                if (! empty($subdir) && ! file_exists($point_dir . '/' . $subdir)) {
                    if (! mkdir($point_dir . '/' . $subdir, 0755)) {
                        return false;
                    }
                }
                $point_dir .= '/' . $subdir;
            }
        }

        if (fopen($point_dir . '/' . $file_name, "a")) {
            $this->log_name = str_replace($this->log_dir, '', $point_dir . '/' . $file_name);
            return true;
        }
        return false;
    }

    function write(string $level, string $message): bool
    {
        if ($fp = @fopen($this->log_dir . $this->log_name, "a")) {
            $message = date('Y-m-d H:i:s') . ' [' . strtoupper($level) . '] ' . $message . PHP_EOL;
            fwrite($fp, $message);
            fclose($fp);
            return true;
        }
        return false;
    }

    function get_log_name(): string
    {
        return str_replace('.log', '', $this->log_name);
    }

    function set_log_name(string $log_name, bool $move_previous = false): bool
    {
        if ($this->log_name = $log_name) {
            return true;
        }

        return $this->check_path($log_name);
    }

    function set_log_dir(string $dir): bool
    {
        $this->log_dir .= '/' . $dir;
        return true;
    }

    function get_log_dir(): string
    {
        return $this->log_dir;
    }
}
