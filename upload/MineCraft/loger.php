<?php
$logger = new Logger("./m.log");
$log_date = "[".date("d m Y H:i")."] ";

class Logger {
	var $file;
	var $error;

	function __construct($path) {
		$this->file = $path;
	}

	function WriteLine($text) {
		$fp = fopen($this->file, "a+");
		if ($fp) {
			fwrite($fp, $text."\n");
		} else {
			$this->error = "������ ������ � ���-����";
		}
		fclose($fp);
	}

	function Read() {
		if (file_exists($this->file)) {
			return file_get_contents($this->file);
		} else {
			$this->error = "���-���� �� ����������";
		}
	}

	function Clear() {
		$fp = fopen($this->file, "a+");
		if ($fp) {
			ftruncate($fp, 0);
		} else {
			$this->error = "������ ������ ���-�����";
		}
		fclose($fp);
	}
}

?>