<?php
namespace CANNALLogs;

class Logs
{
    
    private string $baseDir = APPPATH . 'logs' . DIRECTORY_SEPARATOR;
    
    private string $logDir = '';
    
    private string $logName = '';
    
    private string $logExt = 'log';
    
    public function __construct(string $logDir = null, string $logName = null)
    {
        $logDir = $this->sanitizeDir($logDir);
        $logName = $this->sanitizeName($logName, true);
        if ($logDir && $this->checkLogDir($logDir)) {
            $this->logDir = $logDir . DIRECTORY_SEPARATOR;
        }
        if ($logName && $this->checkLogName($logName)) {
            $this->logName = $logName;
        } else {
            $this->logName = date('Y.m.d') . '.' . $this->logExt;
        }
    }
    
    public function getBaseDir(): string
    {
        return $this->baseDir;
    }
    
    public function setBaseDir(string $baseDir): self
    {
        $baseDir = $this->sanitizeDir($baseDir);
        $this->baseDir = $baseDir;
        return $this;
    }
    
    public function getLogDir(): string
    {
        return $this->logDir;
    }
    
    public function setLogDir(string $logDir): self
    {
        $logDir = $this->sanitizeDir($logDir);
        if ($logDir = $this->checkLogDir($logDir)) {
            $this->logDir = $logDir . DIRECTORY_SEPARATOR;
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
        $logName = $this->sanitizeName($logName, true);
        if ($this->checkLogName($logName)) {
            $this->logName = $logName;
            return $this;
        }
        return false;
    }
    
    private function checkLogDir(string $path): bool
    {
        if (realpath($this->baseDir . $path)) {
            return true;
        } else {
            $parts = explode(DIRECTORY_SEPARATOR, $path);
            $current = $this->baseDir;
            foreach ($parts as $part) {
                $current .= $part . DIRECTORY_SEPARATOR;
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
    
    private function checkLogName(string $filename): bool
    {
        if (realpath($this->baseDir . $this->logDir . $filename)) {
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
    
    private function sanitizeName(string $filename = null, bool $checkExtension = false): string|null
    {
        if (! $filename) {
            return null;
        }
        if ($checkExtension) {
            $ext = '.' . $this->logExt;
            if (substr($filename, strlen($filename) - strlen($ext)) != $ext) {
                $filename .= $ext;
            }
        }
        $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
        $file_name_str = pathinfo($filename, PATHINFO_FILENAME);
        
        // Replaces all spaces with hyphens.
        $file_name_str = str_replace(' ', '_', $file_name_str);
        // Removes special chars.
        $file_name_str = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $file_name_str);
        // Replaces multiple hyphens with single one.
        $file_name_str = preg_replace('/-+/', '_', $file_name_str);
        
        $clean_file_name = $file_name_str;
        if ($file_ext) {
            $clean_file_name .= '.' . $file_ext;
        }
        
        return $clean_file_name;
    }
    
    private function sanitizeDir(string $file_dir = null): string|null
    {
        if (! $file_dir) {
            return null;
        }
        foreach (explode(DIRECTORY_SEPARATOR, $file_dir) as $part) {
            $safe_dir[] = $this->sanitizeName($part);
        }
        return implode(DIRECTORY_SEPARATOR, $safe_dir);
    }
    
    public function write(string $level, string $message): bool
    {
        if ($fp = @fopen($this->baseDir . $this->logDir . $this->logName, "a+")) {
            $message = date('Y-m-d H:i:s') . ' [' . strtoupper($level) . '] ' . $message . PHP_EOL;
            fwrite($fp, $message);
            fclose($fp);
            return true;
        }
        return false;
    }
}
