<?php if (!defined('MCR')) exit;
$menu_items = array (
  0 => 
  array (
    'main' => 
    array (
      'name' => '<i class="glyphicon glyphicon-home"></i> Главная',
      'url' => '',
      'parent_id' => -1,
      'lvl' => -1,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'admin' => 
    array (
      'name' => '<i class="glyphicon glyphicon-wrench"></i> Администрирование',
      'url' => '',
      'parent_id' => -1,
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'add_news' => 
    array (
      'name' => 'Добавить новость',
      'url' => Rewrite::GetURL(array('go', 'news_add')),
      'parent_id' => 'admin',
      'lvl' => 1,
      'permission' => 'add_news',
      'active' => false,
      'inner_html' => '',
    ),
    'category_news' => 
    array (
      'name' => 'Категории новостей',
      'url' => Rewrite::GetURL(array('control', 'category')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'file_edit' => 
    array (
      'name' => 'Файлы',
      'url' => Rewrite::GetURL(array('control', 'filelist')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'control' => 
    array (
      'name' => 'Пользователи',
      'url' => Rewrite::GetURL(array('control', 'user')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'reqests' => 
    array (
      'name' => 'Заявки модераторов',
      'url' => Rewrite::GetURL(array('go', 'reqests')),
      'parent_id' => 'admin',
      'lvl' => 1,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'reg_edit' => 
    array (
      'name' => 'Регистрация',
      'url' => Rewrite::GetURL(array('control', 'ipbans')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'group_edit' => 
    array (
      'name' => 'Группы',
      'url' => Rewrite::GetURL(array('control', 'group')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'site_edit' => 
    array (
      'name' => 'Настройки сайта',
      'url' => Rewrite::GetURL(array('control', 'constants')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'donate_edit' => 
    array (
      'name' => 'Настройки доната',
      'url' => Rewrite::GetURL(array('control', 'donate')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'rcon' => 
    array (
      'name' => 'RCON',
      'url' => Rewrite::GetURL(array('control', 'rcon')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'game_edit' => 
    array (
      'name' => 'Настройки лончера',
      'url' => Rewrite::GetURL(array('control', 'update')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'serv_edit' => 
    array (
      'name' => 'Мониторинг серверов',
      'url' => Rewrite::GetURL(array('control', 'servers')),
      'parent_id' => 'admin',
      'lvl' => 15,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'guide' => 
    array (
      'name' => '<i class="glyphicon glyphicon-send"></i> Начать играть',
      'url' => Rewrite::GetURL(array('go', 'guide')),
      'parent_id' => -1,
      'lvl' => -1,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'rules' => 
    array (
      'name' => '<i class="glyphicon glyphicon-book"></i> Правила',
      'url' => Rewrite::GetURL(array('go', 'rules')),
      'parent_id' => -1,
      'lvl' => -1,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'users' => 
    array (
      'name' => '<i class="glyphicon glyphicon-user"></i> Игроки',
      'url' => Rewrite::GetURL(array('go', 'users')),
      'parent_id' => -1,
      'lvl' => -1,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
  ),
  1 => 
  array (
    'options' => 
    array (
      'name' => '<i class="glyphicon glyphicon-cog"></i> Настройки',
      'url' => Rewrite::GetURL(array('go', 'options')),
      'parent_id' => -1,
      'lvl' => 1,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
    'exit' => 
    array (
      'name' => '<i class="glyphicon glyphicon-log-out"></i> Выход',
      'url' => 'login.php?out=1',
      'parent_id' => -1,
      'lvl' => 1,
      'permission' => -1,
      'active' => false,
      'inner_html' => '',
    ),
  ),
);
