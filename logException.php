<?php

class LogException extends Exception
{
    protected $path = '';
    protected $yesterday;

    public function __construct($str, $key = "ERROR")
    {
        $this->yesterday = date("Y-m-") . (date("d") - 1);
        $this->logWr($str, $key);
    }

    protected function logWr($str, $key)
    {
        $fileName = $this->path . $key . ".log";
        $archiveName = $this->yesterday . "_" . $fileName;
        if ($this->archiveNeeded($archiveName))
            $this->archiveLog($fileName, $archiveName);
        if ($fp = $this->openLog($fileName)) {
            $outStr = date("d.m.y H:i") . ":\t" . $str . "\n";
            $this->writeLog($fp, $outStr);
            $this->closeLog($fp);
        }
    }

    protected function openLog($fileName, $param = "a+")
    {
        $fp = fopen($fileName, $param);
        if (!$fp) {
            echo "Log opening error" . $this->path . "<br>";
            return false;
        } else
            return $fp;

    }

    protected function closeLog($fp)
    {
        if ($fp)
            fclose($fp);
        else
            echo "Log closing error.";
    }

    protected function writeLog($fp, $str)
    {
        if (fwrite($fp, $str) === FALSE)
            echo "Log writing error";
    }

    protected function archiveLog($fileName, $archiveName)
    {
        $archive = new PclZip($archiveName);
        $archive->create($fileName);
        if ($fp = $this->openLog($fileName, "w")) {
            $this->writeLog($fp, '');
            $this->closeLog($fp);
        }
    }

    protected function archiveNeeded($archiveName)
    {
        if (file_exists($archiveName)) {
            return false;
        } else return true;
    }

}


?>