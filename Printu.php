<?php
namespace Mpakfm;
/**
 * Simple Logger
 * @author mpakfm <mpakfm@google.com>
 * @package \Mpakfm\Printu
 * @version 1.0.1
 */
class Printu {
	/**
	 * Make log to screen or file
	 * return: 
	 *		true - return string
	 *		file - print into file /mpakfm/log/($file)
	 *		text|ajax - print in plain text
	 *		false - print in html
	 * @param mixed $obj
	 * @param string $title
	 * @param string $return 
	 * @param string $file
	 * @return string
	 */
	public static function log($obj, $title='', $return=false, $file=false) {
		if ($obj===true) $obj = 'TRUE (bool)';
		if ($obj===false) $obj = 'FALSE (bool)';
		if ($obj===NULL) $obj = 'NULL';
		$string = ($title == '' ? '': "$title: ") .print_r($obj, true)."\n";
		
		if ($return===true)
			return $string;
		elseif ($return == 'file') {
			if (!isset($_SERVER['DOCUMENT_ROOT'])) return false;
			$path = $_SERVER['DOCUMENT_ROOT'].'/mpakfm/log/';
			if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/mpakfm/')) {
				mkdir($_SERVER['DOCUMENT_ROOT'].'/mpakfm',0744,true);
			}
			if (!file_exists($_SERVER['DOCUMENT_ROOT'].'/mpakfm/log')) {
				mkdir($_SERVER['DOCUMENT_ROOT'].'/mpakfm/log',0744,true);
			}
			if (file_exists($path)) {
				if (!$file) $file = 'info.log';
				$res = file_put_contents($path.$file, $string, FILE_APPEND);
				return $path.$file;
			}
		}
		elseif ($return=='text' || $return=='ajax')
			echo $string;
		else
			echo '<div align="left" style="color: #000; text-align:left; background-color:#FFFAFA; border: 1px solid silver; margin: 10px 10px 10px 10px; padding: 10px 10px 10px 10px;">',$title == '' ? '': "<b>$title:&nbsp;</b>", nl2br(str_replace(array(' ','<','>'),array('&nbsp;','&lt;','&gt;'),print_r($obj,true))),'</div>';
	}
}

