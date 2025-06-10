<?php
namespace CANNALLogs;

class Logs
{

    private string $baseDir = APPPATH . '/logs/';

    private string $logDir = '';

    private string $logName = '';

    private string $logExt = 'log';

    public function __construct(string $logDir = null, string $logName = null)
    {
        if ($logDir && $this->checkLogDir($logDir)) {
            $this->logDir = $logDir;
        }
        if ($logName) {
            if ($this->checkLogName($logName)) {
                $this->logName = $logName;
            } else {
                $this->logName = date('Y-m-d_H:i:s') . '.' . $this->logExt;
            }
        }
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function setBaseDir(string $baseDir): self
    {
        $this->baseDir = $baseDir;
        return $this;
    }

    public function getLogDir(): string
    {
        return $this->logDir;
    }

    public function setLogDir(string $logDir): self
    {
        if ($this->checkLogDir($logDir)) {
            $this->logDir = $logDir;
            return $this;
        }
        return false;
    }

    public function getLogName(): string
    {
        return $this->logName;
    }

    public function setLogName(string $logName): self
    {
        if ($this->checkLogName($logName)) {
            $this->logName = $logName;
            return $this;
        }
        return false;
    }

    public function checkLogDir(string $path): bool
    {
        if (is_dir($this->baseDir . $path)) {
            return true;
        } else {
            $parts = explode('/', $path);
            $current = $this->baseDir;
            foreach ($parts as $part) {
                $current .= '/' . $part;
                if (! empty($part) && ! is_dir($current)) {
                    if (! mkdir($current, 0755)) {
                        return false;
                    }
                }
            }
            if (is_dir($this->baseDir . $path)) {
                return true;
            }
        }
        return false;
    }

    function checkLogName(string $filename): bool
    {
        $ext = '.' . $this->logExt;
        if (substr($filename, sizeof($filename) - sizeof($ext)) != $ext) {
            $filename .= $ext;
        }
        if (is_file($this->baseDir . $this->logDir . $filename)) {
            return true;
        } else if (! $stream = fopen($this->baseDir . $this->logDir . $filename, 'a')) {
            return false;
        } else {
            fclose($stream);
        }

        if (is_file($this->baseDir . $this->logDir . $filename)) {
            return true;
        }

        return false;
    }

    function write(string $level, string $message): bool
    {
        if ($fp = @fopen($this->baseDir . $this->logDir . $this->logName . '.' . $this->logExt, "a")) {
            $message = date('Y-m-d H:i:s') . ' [' . strtoupper($level) . '] ' . $message . PHP_EOL;
            fwrite($fp, $message);
            fclose($fp);
            return true;
        }
        return false;
    }
}
