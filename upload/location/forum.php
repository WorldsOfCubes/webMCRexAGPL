<?php
error_reporting(E_ERROR);

$num_by_page = 20; //Число постов, выводимых на страницу

if (isset($_GET['do'])) $do = $_GET['do'];
elseif (isset($_POST['do'])) $do = $_POST['do'];
else $do = 'main';
$menu->SetItemActive('forum');
$path = 'forum/';

loadTool('ajax.php');

if (isset($_GET['page'])) $page = $_GET['page'];
elseif (isset($_POST['page'])) $page = $_POST['page'];
else $page = 1;
if ($page == 0) $page = 1;
$first = ((int) $page - 1) * $num_by_page;
$last  = (int) $page * $num_by_page;

//Форум
switch($do) {
    case 'main': //Главная дерриктория форума(/go/forum/)
    default:
        $forum_partition = $db->execute("SELECT * FROM forum_partition WHERE parent_id = '0'  ORDER BY priority DESC");


        //Вывод категорий
        while($fpat = $db->fetch_assoc($forum_partition)) {
            $parents[] = $fpat;
        }

        //Закрытие/открытие тем
        if(!empty($_GET['lock'])) {
            $id = intval($_GET['lock']);
            $db->execute("UPDATE forum_topics SET closed = 'Y' WHERE id = '$id'");
            header("Location: go/forum/view/topic/ ". $id ."/1");
            exit;
        }

        if(!empty($_GET['unlock'])) {
            $id = intval($_GET['unlock']);
            $db->execute("UPDATE forum_topics SET closed = 'N' WHERE id = '$id'");
            header("Location: go/forum/view/topic/ ". $id ."/1");
            exit;
        }

        //Закрепление/открепление тем
        if(!empty($_GET['top'])) {
            $id = intval($_GET['top']);
            $db->execute("UPDATE forum_topics SET top = 'Y' WHERE id = '$id'");
            header("Location: go/forum/view/topic/ ". $id ."/1");
            exit;
        }

        if(!empty($_GET['down'])) {
            $id = intval($_GET['down']);
            $db->execute("UPDATE forum_topics SET top = 'N' WHERE id = '$id'");
            header("Location: go/forum/view/topic/ ". $id ."/1");
            exit;
        }


        //Вывди списка форумов с подробностями
        foreach($parents as $key => &$value) {
            $forums = $db->execute("SELECT fp.*, (SELECT count(ft.id) FROM forum_topics ft WHERE ft.partition_id = fp.id) as topic_count,
                                    (SELECT count(fm.id) FROM forum_messages fm WHERE fm.partition_id = fp.id) as message_count,
                                    (SELECT acc.login FROM accounts acc, forum_messages fm WHERE fm.partition_id = fp.id AND acc.id = fm.author_id ORDER BY fm.date DESC LIMIT 1) as last_author,
                                    (SELECT ft.title FROM forum_topics ft, forum_messages fm WHERE fm.partition_id = fp.id AND ft.id = fm.topic_id ORDER BY fm.date DESC LIMIT 1) as last_name,
                                    (SELECT ft.id FROM forum_topics ft, forum_messages fm WHERE fm.partition_id = fp.id AND ft.id = fm.topic_id ORDER BY fm.date DESC LIMIT 1) as last_topic_id,
                                    (SELECT fm.date FROM forum_messages fm WHERE fm.partition_id = fp.id ORDER BY fm.date DESC LIMIT 1) as last_date
                                    FROM forum_partition fp
                                    WHERE fp.parent_id = '{$value['id']}' ORDER BY priority DESC ");
            while($forums_cont = $db->fetch_assoc($forums)) {
                $value['forums'][] = $forums_cont;
            }
        } unset($value);

        //Генерация шаблона
        ob_start();
        include View::Get('main.html', $path);
        $content_main = ob_get_clean();

        $page = lng('FORUM_LIST');
        return;
        break;
    case 'viewforum': //Просмотр форума(/go/forum/view/$1)
        $forum_id = intval($_GET['id']);

        //Информация о темах и форуме в котором мы находмся/пагнация/вывод тем
        $forum_topics = $db->execute("SELECT ft.*, acc.login as author_name, (SELECT MAX(fm.date) FROM forum_messages fm WHERE fm.topic_id = ft.id) as lastdate FROM forum_topics ft, accounts acc WHERE ft.partition_id = '$forum_id' AND ft.author_id = acc.id AND ft.top = 'N' ORDER BY lastdate DESC LIMIT $first, $num_by_page");
        $forum_topics_top = $db->execute("SELECT ft.*, acc.login as author_name, (SELECT MAX(fm.date) FROM forum_messages fm WHERE fm.topic_id = ft.id) as lastdate FROM forum_topics ft, accounts acc WHERE ft.partition_id = '$forum_id' AND ft.author_id = acc.id AND ft.top = 'Y' ORDER BY lastdate DESC LIMIT $first, $num_by_page");
        $forum_name = $db->execute("SELECT name FROM forum_partition WHERE id = '$forum_id'");

        while($fname = $db->fetch_assoc($forum_name)) {
            $name = $fname['name'];
        }

        if(!isset($name)) {
            header("Location: /go/forum");
            exit;
        }

        while($ftop = $db->fetch_assoc($forum_topics)) {
            $topics[] = $ftop;
        }

        while($ftop_top = $db->fetch_assoc($forum_topics_top)) {
            $topics_top[] = $ftop_top;
        }
        //Генерация шаблона
        ob_start();
        include View::Get('forum_topics.html', $path);
        $content_main = ob_get_clean();

        //Пагинатор(Постраничная навигация)
        $pagin_topics = $db->execute("SELECT COUNT(*) FROM `forum_topics` WHERE `partition_id` = '$forum_id'");
        $pagin_line = $db->fetch_array($pagin_topics);
        $view = new View("forum/paginator/");
        $content_main .= $view->arrowsGenerator('/go/forum/view/'.$forum_id."/", $page, $pagin_line[0], $num_by_page, "pagin");

        $page = lng('FORUM_CAT_VIEW');
        return;
        break;
    case 'viewtopic': //Просмотр темы(/go/forum/view/topic/$1)
        $topic_id = intval($_GET['id']);
        $message_add = '';
        $mess_id = intval($_POST['mess_id']);

        $delete = array();

        //Удаление комментов юзеров
        if(!empty($user) && $user->lvl() > 0 && $user->lvl() < 13) {
            if(!empty($_POST['mess_id']) && !empty($_POST['mess_auth'])) {
                $mess_id = intval($_POST['mess_id']);
                $auth_id = intval($_POST['mess_auth']);
                if($user->id() == $auth_id) {
                    $db->execute("DELETE FROM forum_messages WHERE id = '$mess_id' AND author_id = '$auth_id'");
                } else {
                    accss_deny();
                }
            }
        }


        //удаление топика и содержимого админом
        if (!empty($user) && $user->lvl() > 13) {

            if (isset($mess_id)) {
                $messages = $db->execute("SELECT * FROM forum_messages WHERE id = '$mess_id'");
                while ($msgs = $db->fetch_assoc($messages)) {
                    $message_list[] = $msgs;
                }
                foreach ($message_list as $msg) {
                    if ($msg['topmsg'] == 'Y') {
                        $delete['topmsg'][] = $msg['topic_id'];
                    } else $delete['default'][] = $mess_id;
                }

                if (count($delete['topmsg'])) {
                    $db->execute("DELETE FROM forum_messages WHERE topic_id IN (" . implode($delete['topmsg'], ',') . ")");
                    $db->execute("DELETE FROM forum_topics WHERE id IN (" . implode($delete['topmsg'], ',') . ")");
                }

                if (count($delete['default'])) {
                    $db->execute("DELETE FROM forum_messages WHERE id IN (" . implode($delete['default'], ',') . ")");
                }
            }
        }

        //Добавление сообщения
        if (!empty($user) && $user->lvl() > 0) {

            if(!empty($_POST['topic_id']) && !empty($_POST['message']) && $_POST['topic_id'] == $topic_id) {
                $message = $_POST['message'];
                $time = time();
                $message = TextBase::HTMLDestruct($message);
                if (CaptchaCheck(0, false)) {
                    $selectpart = $db->execute("SELECT partition_id FROM forum_topics WHERE id = '{$_POST['topic_id']}'");
                    $selectpart = $db->fetch_assoc($selectpart);
                    $db->execute("INSERT INTO `forum_messages`(`topic_id`, `author_id`, `message`, `date`, `partition_id`) VALUES ('$topic_id','" . $user->id() . "','" . $db->safe($message) . "','$time', '{$selectpart['partition_id']}')");
                } else {
                    $info = 'Неверный код проверки';
                }
            }
        }

        $topic_info = $db->execute("SELECT ft.*, fp.name as part_name, fp.id as part_id FROM forum_topics ft, forum_partition fp WHERE ft.id = '$topic_id' AND fp.id = ft.partition_id");
        $topic_info = $db->fetch_assoc($topic_info);

        $forum_topic = $db->execute("SELECT fm.*, acc.login as author_name FROM forum_messages fm, accounts acc WHERE fm.topic_id = '$topic_id' AND acc.id = fm.author_id ORDER BY fm.date ASC LIMIT $first, $num_by_page");

        while($ftopic = $db->fetch_assoc($forum_topic)) {
            $ftopic_msg[] = $ftopic;
        }

        $topmsg = $db->execute("SELECT fm.*, acc.login as author_name FROM forum_messages fm, accounts acc WHERE fm.topic_id = '$topic_id' AND fm.topmsg = 'Y' AND acc.id = fm.author_id");
        while($tpmsg = $db->fetch_assoc($topmsg)) {
            $tpmess[]= $tpmsg;
        }

        $lock = $db->execute("SELECT closed FROM forum_topics WHERE id = '$topic_id'");
        $lock = $db->fetch_assoc($lock);

        $toped = $db->execute("SELECT top FROM forum_topics WHERE id = '$topic_id'");
        $toped = $db->fetch_assoc($toped);



        if (!empty($user) && $user->lvl() > 0) {
            if( $lock['closed'] == 'N' ) {
                ob_start();
                include View::Get('forum_mess_add.html', $path);
                $message_add = ob_get_clean();
            }
        }

        if(!isset($ftopic_msg)) {
            header("Location: /go/forum");
            exit;
        }

        ob_start();
        include View::Get('forum_topic.html', $path);
        $content_main = ob_get_clean();

        $pagin_topics = $db->execute("SELECT COUNT(*) FROM `forum_messages` WHERE `topic_id` = '$topic_id'");
        $pagin_line = $db->fetch_array($pagin_topics);
        $view = new View("forum/paginator/");
        $content_main .= $view->arrowsGenerator('/go/forum/view/topic/'.$topic_id."/", $page, $pagin_line[0], $num_by_page, "pagin");

        $page = lng('FORUM_TOPIC_VIEW');
        return;
        break;
    case 'add': //добавление топиков
        if (!empty($user) && $user->lvl() > 0) {
            $forum_id = $_GET['id'];

            $topic_information = $db->execute("SELECT name FROM forum_partition WHERE id = '$forum_id'");
            while ($topic_inf = $db->fetch_assoc($topic_information)) {
                $topic_info = $topic_inf['name'];
            }

            if (!empty($_POST['message']) && !empty($_POST['topic_title'])) {
                $message = $_POST['message'];
                $message = TextBase::HTMLDestruct($message);
                $title = $_POST['topic_title'];
                $title = TextBase::HTMLDestruct($title);
                $time = time();
                if(CaptchaCheck(0,false)) {
                    if(!empty($_POST['top'])) {
                        $top = $_POST['top'];
                        $db->execute("INSERT INTO forum_topics(partition_id, author_id, title, date, top) VALUES ('$forum_id','" . $user->id() . "','" . $db->safe($title) . "','$time', '". $db->safe($top) ."')");
                    } else {

                        $db->execute("INSERT INTO forum_topics(partition_id, author_id, title, date) VALUES ('$forum_id','" . $user->id() . "','" . $db->safe($title) . "','$time')");
                    }
                    $forum_ids = mysql_insert_id();
                    $db->execute("INSERT INTO forum_messages(partition_id, topic_id, author_id, message, date, topmsg) VALUES ('$forum_id', '" . $db->insert_id() . "', '" . $user->id() . "','" . $db->safe($message) . "','$time', 'Y')");
                    header("Location: /go/forum/view/topic/" . $forum_ids . "/1/");
                    exit;
                } else {
                    $info = 'Неверный код проверки';
                }
            }
        } else {
            accss_deny();
        }

        ob_start();
        include View::Get('forum_topic_add.html', $path);
        $content_main = ob_get_clean();

        if(!isset($topic_info)) {
            ob_start();
            include View::Get('forum_topic_die.html', $path);
            $content_main = ob_get_clean();
        }

        $page = lng('FORUM_TOPIC_NEW');
        return;
        break;
    case 'mainadd': //Добавление категорий и форумов
        if(!empty($user) && $user->lvl() > 13) {
            if(!empty($_POST['cat_name'])) {
                $_POST['cat_name'] = TextBase::HTMLDestruct($_POST['cat_name']);
                $db->execute("INSERT INTO forum_partition(name) VALUES ('". $db->safe($_POST['cat_name']) ."')");
                header("Location: /go/forum/");
                exit;
            }

            $categorys = $db->execute("SELECT * FROM forum_partition WHERE parent_id = '0'");
            while($categor = $db->fetch_assoc($categorys)) {
                $categ[] = $categor;
            }

            $list_cat = '';
            foreach($categ as $cat) {
                $list_cat .= '<option value="'. $cat['id'] .'">'. $cat['name'] .'</option>';
            }

            if(!empty($_POST['name']) && !empty($_POST['category']) && !empty($_POST['description'])) {
                $db->execute("INSERT INTO forum_partition(parent_id, name, description) VALUES ('". $db->safe($_POST['category']) ."','". $db->safe($_POST['name']) ."', '". $db->safe($_POST['description']) ."' )");
                header("Location: /go/forum/");
                exit;
            }
        } else {
            accss_deny();
        }

        ob_start();
        include View::Get('forum_settings.html', $path);
        $content_main = ob_get_clean();

        $page = lng('FORUM_SETTINGS');
        return;
    break;
    case 'edit':
        if(!empty($user) && $user->lvl() > 13) {
            $msg_id = intval($_GET['id']);
            if( !empty($_POST['id']) && !empty($_POST['message']) ) {
                if( $_POST['id'] == $msg_id) {
                    if(CaptchaCheck(0,false)) {
                        $message = $_POST['message'];
                        $message = TextBase::HTMLDestruct($message);
                        $db->execute("UPDATE forum_messages SET message = '". $db->safe($message) ."' WHERE id = '$msg_id'");
                        $link = $db->execute("SELECT topic_id FROM forum_messages WHERE id = '$msg_id'");
                        $link = $db->fetch_assoc($link);
                        header("Location: /go/forum/view/topic/". $link['topic_id'] ."/1");
                        exit;
                    } else {
                        $info = 'Неверный код проверки';
                    }
                }
            }
            $message_db = $db->execute("SELECT message FROM forum_messages WHERE id = '$msg_id'");
            $message_db = $db->fetch_assoc($message_db);
            $message = $message_db['message'];


            ob_start();
            include View::Get('forum_mess_edit.html', $path);
            $content_main = ob_get_clean();
            $page = lng('MESSAGE_EDIT');
        } else {
            accss_deny();
        }
    break;
}