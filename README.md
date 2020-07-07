# printu
# Simple Logger

**version 1.2.3**

* bug-fix in method show()
* add method error() for default set datetime and file error.log

**version 1.2.2**

Response types:
* var - return as a string variable
* file - print in the log file as plain text, **set as default now**
* text - print in STDOUT as plain text
* html - print in STDOUT as html

Default response type is "file"

You can change default response type by this method:

```php
public static function setDefaultResponse(string $response): bool {}
```

Set path to save log files:

```php
public static function setPath(string $path=''): bool {}
```

For example:

some index.php 
```php
Printu::setPath(__DIR__.'/var/log');
Printu::setDefaultResponse('html');
```

some some.php
```php
Printu::obj($_POST)->title('POST');
```

**version 1.2**

New samples:
```php
Printu::setPath(__DIR__ . '/var/log');

Printu::obj('-----')->response('file')->show();

$dt = new \DateTime();
$dt->sub(new \DateInterval('P1MT10H'));
Printu::obj('test')->title('IndexController::index')->dt($dt)->response('file')->show();

Printu::obj('test H:i:s in log file info.log')->dt()->timeFormat('H:i:s')->response('file')->show();

Printu::obj('test in log file error.log')->response('file')->file('error')->show();

Printu::obj('test echo')->show();
```

tail -f var/log/info.log:
```log
-----
01.06 13:50:13  IndexController::index: test
23:31:08        test H:i:s in log file info.log
```
tail -f var/log/error.log:
```log
-----
test in log file error.log
```

Old version samples:
```php
Printu::setPath(__DIR__ . '/var/log');

$dt = new \DateTime();
$dt->sub(new \DateInterval('P1MT10H'));
Printu::log('-------', '', 'file');
Printu::log('test', $dt->format('d.m H:i:s')."\t".'IndexController::index', 'file');
Printu::log('test in log file info.log', 'IndexController::index', 'file');
Printu::log('test in log file error.log', 'IndexController::index', 'file', 'error.log');
```
tail -f var/log/info.log:
```log
-------
01.06 13:58:20  IndexController::index: test
IndexController::index: test in log file info.log
```
tail -f var/log/error.log:
```log
IndexController::index: test in log file error.log
```
