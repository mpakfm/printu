# printu
# Simple Logger

**version 2.0.0**

* add log levels: [debug, info, warning, alert]
* add notifications (email)

Configuration:
```php
Printu::setConfNotifier([
    'warning' => 'email',
    'alert'   => 'email',
]);
Printu::setConfChannel([
    'email' => ['some@email.com', 'another@email.com']
]);
Printu::$fromNotifier = 'no-reply@server';
Printu::$subjNotifier = 'Some project';
```
Usage:
```php
$var = ['asd', 'fgh'];
Printu::debug($var);
Printu::info($var)->title('INFO $var');
Printu::warning($var)->title('WARNING $var');
Printu::alert($var)->title('ALERT $var'); 
```
Log files:

debug.log
```text
Array
(
    [0] => asd
    [1] => fgh
)
```
info.log
```text
24.05 18:54:35	INFO $var: Array
(
    [0] => asd
    [1] => fgh
)
```
warning.log
```text
24.05 18:54:35	WARNING $var: Array
(
    [0] => asd
    [1] => fgh
)
```
alert.log
```text
24.05 18:55:35	ALERT $var: Array
(
    [0] => asd
    [1] => fgh
)
```
email subject:
```text
{Printu::$subjNotifier} {level} {title}:
```
email example:
```text
From hp440 Tue May 24 19:13:42 2022
Return-Path: <hp440>
Received: (from hp440@localhost)
	by hp440 (8.15.2/8.15.2/Submit) id 24OGDgaT3370909;
	Tue, 24 May 2022 19:13:42 +0300
Date: Tue, 24 May 2022 19:13:42 +0300
Message-Id: <202205241613.24OGDgaT3370909@hp440>
To: some@email.com
Subject: Some project warning WARNING $var
From: no-reply@server
X-Mailer: PHP/7.4.29

24.05 19:12:42	WARNING $var: Array
(
    [0] => asd
    [1] => fgh
)

```

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
