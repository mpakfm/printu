<?php
/**
 * Simple Logger
 * @author mpakfm <mpakfm@google.com>
 * @package \Mpakfm\Printu
 * @version 1.2
 */

namespace Mpakfm;

class Printu {

    /**
     * Path to the log folder
     * @var string
     */
    public static $logPath;

    /** @var array */
    public static $confNotifier = [
        'debug'   => null,
        'info'    => null,
        'warning' => null,
        'alert'   => null,
    ];

    /** @var string */
    public static $fromNotifier = 'PrintuLogger';
    /** @var string */
    public static $subjNotifier = 'LOGGER';

    /** @var array */
    public static $confChannel;

    /** @var array */
    public static $availableChannel = ['email'];

    /**
     * Default type of the log response
     * @var string
     */
    private static $defaultResponse = 'file';

    /** @var array */
    public static $responseTypes = [
        'var',
        'file',
        'text',
        'html',
    ];

    /** @var string */
    protected $level;

    /** @var bool */
    protected $isDating;

    /** @var mixed */
    private $obj;

    /** @var string */
    private $title;

    /** @var string */
    private $response;

    /** @var \DateTime */
    private $dt;

    /** @var string */
    private $dtFormat = 'd.m H:i:s';

    /** @var bool */
    private $isShowed = false;

    /** @var string */
    public $file;

    public function __construct($obj) {
        if ($obj === true) {
            $obj = 'TRUE (bool)';
        }
        if ($obj === false) {
            $obj = 'FALSE (bool)';
        }
        if (is_null($obj)) {
            $obj = 'NULL';
        }
        if (is_string($obj) && $obj == '') {
            $obj = 'EMPTY LINE (string)';
        }
        $this->obj      = $obj;
        $this->isDating = false;
    }

    public function __destruct() {
        if ($this->isShowed) {
            return;
        }
        $this->show();
    }

    public function title(string $title) {
        $this->title = $title;
        return $this;
    }

    public function dt(\DateTime $dt = null) {
        if ($dt) {
            $this->dt = $dt;
        } else {
            $this->dt = new \DateTimeImmutable();
        }
        return $this;
    }

    public function dating() {
        $this->isDating = true;
        return $this;
    }

    public function timeFormat(string $format) {
        $this->dtFormat = $format;
        return $this;
    }

    /**
     * Types of response:
     *      var     return the variable
     *      file    write in file
     *      text    write in STD_OUTPUT like text
     *      html    write in STD_OUTPUT like html
     */
    public function response(string $response) {
        if (!in_array($response, static::$responseTypes)) {
            throw new \Exception('Unknown response type: ' . $response . '. Try one of this: ' . implode(', ', static::$responseTypes));
        }
        $this->response = $response;
        return $this;
    }

    public function file(string $file) {
        $this->file = $file;
        if (strpos($this->file, '.') === false) {
            $this->file .= '.log';
        }
        return $this;
    }

    /**
     * @deprecated deprecated since version 2.0.0
     */
    public function error(string $title) {
        $this->title($title);
        $this->dt();
        $this->file('error');
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function show() {
        if ($this->isShowed) {
            return;
        }
        $this->isShowed = true;
        $string = '';
        if ($this->dt) {
            $string .= $this->dt->format($this->dtFormat)."\t";
        }
        if ($this->title) {
            $string .= ($this->title == '' ? '': "{$this->title}: ");
        }

        if (!$this->response) {
            $this->response = static::$defaultResponse;
        }

        $this->notice($string);

        switch ($this->response) {
            case"var":
                $string .= print_r($this->obj, true)."\n";
                return $string;
                break;
            case"file":
                $string .= print_r($this->obj, true)."\n";
                if (!$this->file) {
                    $this->file = 'info.log';
                }
                if ($this->isDating) {
                    $dt         = new \DateTime();
                    $this->file = $dt->format('d-m-Y-') . $this->file;
                }
                $filePath = static::$logPath . DIRECTORY_SEPARATOR . $this->file;
                if (static::$logPath) {
                    file_put_contents($filePath, $string, FILE_APPEND);
                    return $filePath;
                } else {
                    throw new \Exception ('Printu cannot create this log file: ' . $filePath . '. You need check var $pathToLogFolder ("' . static::$logPath . '") in init line: \Mpakfm\Printu::setPath($pathToLogFolder).');
                }
                break;
            case"text":
                $string .= print_r($this->obj, true)."\n";
                echo $string;
                break;
            case"html":
                if ($this->title != '') {
                    $string = str_replace($this->title . ':', "<b>{$this->title}:&nbsp;</b>", $string);
                }
                $string .= nl2br(str_replace([' ','<','>'], ['&nbsp;','&lt;','&gt;'], print_r($this->obj,true)));
                $string = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $string);
                echo '<div style="color: #000; text-align:left; background-color:#FFFAFA; border: 1px solid silver; margin: 10px 10px 10px 10px; padding: 10px 10px 10px 10px;">',$string ,'</div>';
                break;
        }
    }

    public function notice(string $string) {
        if (!$this->level || !self::$confNotifier[$this->level]) {
            return;
        }
        $channel = self::$confNotifier[$this->level];
        if (!self::$confChannel[$channel]) {
            return;
        }
        $method = "{$channel}Channel";
        $this->$method($string);
    }

    public function emailChannel(string $string) {
        if (empty(self::$confChannel['email'])) {
            return;
        }
        foreach (self::$confChannel['email'] as $email) {
            $headers = 'From: ' . self::$fromNotifier . "\r\n" . 'X-Mailer: PHP/' . phpversion();
            mail($email, self::$subjNotifier . " {$this->level} {$string}", print_r($this->obj, true), $headers);
        }
    }

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
     * Set Notifier configuration
     */
    public static function setConfNotifier(array $conf): bool {
        foreach (self::$confNotifier as $key => $value) {
            if (array_key_exists($key, $conf) && in_array($conf[$key], self::$availableChannel)) {
                self::$confNotifier[$key] = $conf[$key];
            }
        }
        return true;
    }

    /**
     * Set Channels configuration
     *  Keys:
     *      email: ['some@email.com', 'another@email.com', ]
     */
    public static function setConfChannel(array $conf): bool {
        foreach ($conf as $key => $value) {
            if (in_array($key, self::$availableChannel)) {
                self::$confChannel[$key] = $conf[$key];
            }
        }
        return true;
    }

    public static function setDefaultResponse(string $response): bool {
        if (!in_array($response, static::$responseTypes)) {
            throw new \Exception('Unknown response type: ' . $response . '. Try one of this: ' . implode(', ', static::$responseTypes));
        }
        static::$defaultResponse = $response;
        return true;
    }

    /**
     * Logger - Debug method
     * @param mixed $obj
     */
    public static function debug($obj): Printu {
        $log = new self($obj);
        $log->level = 'debug';
        return $log->response('file')->file('debug');
    }

    /**
     * Logger - Info method
     * @param mixed $obj
     */
    public static function info($obj): Printu {
        $log = new self($obj);
        $log->level = 'info';
        return $log->dt()->response('file')->file('info');
    }

    /**
     * Logger - Warning method
     * @param mixed $obj
     */
    public static function warning($obj): Printu {
        $log = new self($obj);
        $log->level = 'warning';
        return $log->dt()->response('file')->file('warning');
    }

    /**
     * Logger - Alert method
     * @param mixed $obj
     */
    public static function alert($obj): Printu {
        $log = new self($obj);
        $log->level = 'alert';
        return $log->dt()->response('file')->file('alert');
    }

    /**
     * Logger base method
     * @param mixed $obj
     */
    public static function obj($obj): Printu {
        return new self($obj);
    }

    /**
     * Make log to screen or file
     * return:
     *      true - return string
     *      file - print into file: static::$logPath . '/{$file}'
     *      text|ajax - print in plain text
     *      false - print in html
     * @deprecated deprecated since version 1.2.2
     * @param mixed $obj
     * @param string $title
     * @param string $return
     * @param string $file
     * @throws \Exception
     * @return mixed (string | boolean | void)
     */
    public static function log($obj, string $title='', $return=null, string $file=null) {
        if ($obj === true) {
            $obj = 'TRUE (bool)';
        }
        if ($obj === false) {
            $obj = 'FALSE (bool)';
        }
        if (is_null($obj)) {
            $obj = 'NULL';
        }
        if (is_string($obj) && $obj == '') {
            $obj = 'EMPTY LINE (string)';
        }
        $string = ($title == '' ? '': "$title: ") .print_r($obj, true)."\n";
        if ($return === true) {
            return $string;
        } elseif ($return == 'file') {
            if (!$file) {
                $file = 'info.log';
            }
            $filePath = static::$logPath . DIRECTORY_SEPARATOR . $file;
            if (static::$logPath) {
                file_put_contents($filePath, $string, FILE_APPEND);
                return $filePath;
            } else {
                throw new \Exception ('Printu cannot create this log file: ' . $filePath . '. You need check var $pathToLogFolder ("' . static::$logPath . '") in init line: \Mpakfm\Printu::setPath($pathToLogFolder).');
            }
        } elseif ($return == 'text' || $return == 'ajax') {
            echo $string;
        } else {
            echo '<div style="color: #000; text-align:left; background-color:#FFFAFA; border: 1px solid silver; margin: 10px 10px 10px 10px; padding: 10px 10px 10px 10px;">',$title == '' ? '': "<b>{$title}:&nbsp;</b>", nl2br(str_replace([' ','<','>'], ['&nbsp;','&lt;','&gt;'], print_r($obj,true))),'</div>';
        }
        return;
    }
}
