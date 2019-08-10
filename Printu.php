<?php
/**
 * Simple Logger
 * @author mpakfm <mpakfm@google.com>
 * @package \Mpakfm\Printu
 * @version 1.1.0
 */

namespace Mpakfm;

class Printu {

    /**
     * Path to log folder
     * @var string 
     */
    public static $logPath;

    /**
     * Set path to log folder
     * @param string $path
     * @return bool
     * @throws \Exception
     */
    public static function setPath(string $path=''): bool {
        if (!file_exists($path)) {
            if (!mkdir($path,0754,true)) {
                throw new \Exception ('Printu cannot create this folder: ' . $path . '. Sorry, check permissions on the file system.');
            }
        }
        static::$logPath = $path;
        return true;
    }

    /**
     * Make log to screen or file
     * return: 
     *      true - return string
     *      file - print into file: static::$logPath . '/{$file}'
     *      text|ajax - print in plain text
     *      false - print in html
     * @param mixed $obj
     * @param string $title
     * @param string $return 
     * @param string $file
     * @return mixed (string | boolean | void)
     */
    public static function log($obj, string $title='', $return=false, string $file=false) {
        if ($obj===true) $obj = 'TRUE (bool)';
        if ($obj===false) $obj = 'FALSE (bool)';
        if ($obj===NULL) $obj = 'NULL';
        $string = ($title == '' ? '': "$title: ") .print_r($obj, true)."\n";		
        if ($return===true) {
            return $string;
        } elseif ($return == 'file') {
            if (!$file) {
                $file = 'info.log';
            }
            $filePath = static::$logPath . DIRECTORY_SEPARATOR . $file;
            if (static::$logPath) {
                $res = file_put_contents($filePath, $string, FILE_APPEND);
                return $filePath;
            } else {
                throw new \Exception ('Printu cannot create this log file: ' . $filePath . '. You need check var $pathToLogFolder ("' . static::$logPath . '") in init line: \Mpakfm\Printu::setPath($pathToLogFolder).');
            }
        } elseif ($return=='text' || $return=='ajax') {
            echo $string;
        } else {
            echo '<div align="left" style="color: #000; text-align:left; background-color:#FFFAFA; border: 1px solid silver; margin: 10px 10px 10px 10px; padding: 10px 10px 10px 10px;">',$title == '' ? '': "<b>$title:&nbsp;</b>", nl2br(str_replace(array(' ','<','>'),array('&nbsp;','&lt;','&gt;'),print_r($obj,true))),'</div>';
        }
    }
}
