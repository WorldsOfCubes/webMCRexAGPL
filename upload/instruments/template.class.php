<?php
/*
=====================================
Автор идеи: Игорь Ткаченко (Alone)
=====================================
*/

class TemplateParser {	
private $lang;
	public function __construct() {
		global $MCR_LANG_TPL;
		$this->lang = $MCR_LANG_TPL;
	}
	
	/*=== Шаблонизатор ===*/
	public function parse($html_unparsed) {/*=== Проверка целостности языкового массива ===*/
		if(!$this->lang){
		die('Файл языкового пакета битый/не найден');//Выводим ошибку, если файл пуст или не найден
		exit;
		}
		
		/*=== Заменяем все найденые совпадения на нужные данные ===*/
		$html_parsed = str_replace(array_keys($this->lang), array_values($this->lang), $html_unparsed); //Производим замену
		return $html_parsed; //отдаем результат
	}
}
?>