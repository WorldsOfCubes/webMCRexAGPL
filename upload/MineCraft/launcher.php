<?php
    header('Content-Type: text/html; charset=cp1251');
	define('INCLUDE_CHECK',true);
	include("connect.php");
	include_once("loger.php");
        @$login       = $_POST['login'];
        @$postPass    = $_POST['password'];
        @$client      = $_POST['client'];
        @$action      = $_POST['action'];
	if(!file_exists($uploaddirs)) die ("Путь к скинам не является папкой! Укажите в настройках правильный путь.");
	if(!file_exists($uploaddirp)) die ("Путь к плащам не является папкой! Укажите в настройках правильный путь.");
	
	try {
	
	if (!preg_match("/^[a-zA-Z0-9_-]+$/", $login) || !preg_match("/^[a-zA-Z0-9_-]+$/", $postPass) || !preg_match("/^[a-zA-Z0-9_-]+$/", $action)) {
	
		exit("errorLogin"); 	
    }	
	
	if($crypt === 'hash_md5' || $crypt === 'hash_authme' || $crypt === 'hash_xauth' || $crypt === 'hash_cauth' || $crypt === 'hash_joomla' || $crypt === 'hash_joomla_new' || $crypt === 'hash_wordpress' || $crypt === 'hash_dle' || $crypt === 'hash_launcher' || $crypt === 'hash_drupal' || $crypt === 'hash_imagecms')
	{
		$stmt = $db->prepare("SELECT $db_columnUser,$db_columnPass,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$stmt->bindColumn($db_columnPass, $realPass);
		$stmt->bindColumn($db_columnUser, $realUser);
		$stmt->fetch();
	} else if ($crypt === 'hash_ipb' || $crypt === 'hash_vbulletin' || $crypt === 'hash_punbb')
	{
		
		$stmt = $db->prepare("SELECT $db_columnUser,$db_columnPass,$db_columnSalt,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$stmt->bindColumn($db_columnPass, $realPass);
		$stmt->bindColumn($db_columnSalt, $salt);
		$stmt->bindColumn($db_columnUser, $realUser);
		$stmt->fetch();
	} else if($crypt == 'hash_xenforo')
	{
		
		$stmt = $db->prepare("SELECT scheme_class, $db_table.$db_columnId,$db_table.$db_columnUser,$db_tableOther.$db_columnId,$db_tableOther.$db_columnPass FROM $db_table, $db_tableOther WHERE $db_table.$db_columnId = $db_tableOther.$db_columnId AND $db_table.$db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$stmt->bindColumn($db_columnPass, $salt);
		$stmt->bindColumn($db_columnUser, $realUser);
		$stmt->fetch();
		$stmt->execute();
		$stmt->bindColumn($db_columnPass, $realPass);
		$stmt->bindColumn('scheme_class', $scheme_class);
		$stmt->fetch();	
		$realPass = substr($realPass,22,64);
		if($scheme_class==='XenForo_Authentication_Core') {
			$salt = substr($salt,105,64);
		} else $salt = false;
	} else die("badhash");

	$checkPass = hash_name($crypt, $realPass, $postPass, @$salt);

	if($useantibrut)
	{	
		$ip  = getenv('REMOTE_ADDR');	
		$time = time();
		$bantime = $time+(10);
		$stmt = $db->prepare("Select sip,time From sip Where sip='$ip' And time>'$time'");
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$real = $row['sip'];
		if($ip == $real)
		{
			$stmt = $db->prepare("DELETE FROM sip WHERE time < '$time';");
			$stmt->execute();
			echo 'temp'; 
		   exit;
		}
		
		if ($login !== $realUser)
		{
			$stmt = $db->prepare("INSERT INTO sip (sip, time)VALUES ('$ip', '$bantime')");
			$stmt->execute();
			exit ('errorLogin');
		}
		if(!strcmp($realPass,$checkPass) == 0 || !$realPass) {
			$stmt = $db->prepare("INSERT INTO sip (sip, time)VALUES ('$ip', '$bantime')");
			$stmt->execute();
			exit("errorLogin");
		}

    } else {
		if ($login !== $realUser)
		{
			exit ('errorLogin');
		}
		if(!strcmp($realPass,$checkPass) == 0 || !$realPass) die("errorLogin");
    }
	
if($useban)
{
    $time = time();
    $tipe = '2';
	$stmt = $db->prepare("Select name From $banlist Where name= :login And type<'$tipe' And temptime>'$time'");
	$stmt->bindValue(':login', $login);
	$stmt->execute();
   if($stmt->fetchColumn() == 1)
	{
		$stmt = $db->prepare("Select name,temptime From $banlist Where name= :login And type<'$tipe' And temptime>'$time'");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		exit ('Временный бан до '.date('d.m.Yг. H:i', $row['temptime'])." по времени сервера");
    }
		$stmt = $db->prepare("Select name From $banlist Where name= :login And type<'$tipe' And temptime='0'");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
	if($stmt->fetchColumn() == 1)
    {
      exit ("Вечный бан");
    }
}
	if($action == 'getpersonal' && !$usePersonal) die("Использование ЛК выключено");
	if($action == 'uploadskin' && !$canUploadSkin) die("Функция недоступна");
	if($action == 'uploadcloak' && !$canUploadCloak) die("Функция недоступна");
	if($action == 'buyvip' && !$canBuyVip) die("Функция недоступна");
	if($action == 'buypremium' && !$canBuyPremium) die("Функция недоступна");
	if($action == 'buyunban' && !$canBuyUnban) die("Функция недоступна");
	if($action == 'exchange' && !$canExchangeMoney) die("Функция недоступна");
	if($action == 'activatekey' && !$canActivateVaucher) die("Функция недоступна");

	if($action == 'exchange' || $action == 'getpersonal')
	{
			$stmt = $db->prepare("SELECT username,balance FROM iConomy WHERE username= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$rowicon = $stmt->fetch(PDO::FETCH_ASSOC);
			$iconregistered = true;
		
		if(!$rowicon['balance'])
		{
			$stmt = $db->prepare("INSERT INTO `iConomy` (`username`, `balance`, `status`) VALUES (:login, '$initialIconMoney.00', '0');");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$iconregistered = false;
		}
	}
    
	if($action == 'auth')
	{
		if(!file_exists("clients/".$client."/bin/client.zip") || !file_exists("clients/".$client."/bin/minecraft.jar") ||
		   !file_exists("clients/".$client."/bin/libraries.jar")  || !file_exists("clients/".$client."/bin/Forge.jar")  ||
		   !file_exists("clients/".$client."/bin/extra.jar") || !file_exists("clients/".$client."/mods/")               || 
		   !file_exists("clients/".$client."/coremods/") || !file_exists("clients/".$client."/bin/assets.zip")) 
		   die("client $client");
		   
	    
	    $chars="0123456789abcdef";
        $max=32;
        $size=StrLen($chars)-1;
        $password=null;
        while($max--)
        $password.=$chars[rand(0,$size)];
	    $chars2="0123456789abcdef";
        $max2=32;
        $size2=StrLen($chars)-1;
        $password2=null;
        while($max2--)
        $password2.=$chars2[rand(0,$size2)];
		
		$sessid 		= "token:".$password.":".$password2;
		$md5zip			= md5_file("clients/".$client."/bin/client.zip");
		$md5czip        = strtoint(xorencode($md5zip, $protectionKey));
		$md52zip		= md5_file("clients/".$client."/bin/assets.zip");
		$md52czip       = strtoint(xorencode($md52zip, $protectionKey));
		$md5jar         = md5_file("clients/".$client."/bin/minecraft.jar");
		$md5cjar        = strtoint(xorencode($md5jar, $protectionKey));
		$md5lwjql		= md5_file("clients/".$client."/bin/libraries.jar");
		$md5clwjql      = strtoint(xorencode($md5lwjql, $protectionKey));
		$md5lwjql_util	= md5_file("clients/".$client."/bin/Forge.jar");
		$md5clwjql_util = strtoint(xorencode($md5lwjql_util, $protectionKey));
		$md5jinput		= md5_file("clients/".$client."/bin/extra.jar");
		$md5cjinput     = strtoint(xorencode($md5jinput, $protectionKey));
		
		$stmt = $db->prepare("UPDATE $db_table SET $db_columnSesId='$sessid' WHERE $db_columnUser = :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		
		echo "$md5czip<:>$md52czip<:>$md5cjar<:>$md5clwjql<:>$md5clwjql_util<:>$md5cjinput<:>$masterversion<br>".
		$realUser.'<:>'.strtoint(xorencode($sessid, $protectionKey)).'<br>';
		
		$colMods = 0; $files = scandir("clients/".$client."/mods");
		for($i=0; $i < sizeof($files); $i++) if(substr($files[$i], -4) == ".zip" || substr($files[$i], -4) == ".jar" || substr($files[$i], -8) == ".litemod")
		{
			$echo1 = $files[$i].":>".md5_file("clients/".$client."/mods/".$files[$i])."<:>"; $colMods++;
			echo str_replace(' ', '%20', $echo1);
		} if($colMods == 0);
		echo '::';
		$colCoreMods = 0; $coremods = scandir("clients/".$client."/coremods");
		for($i=0; $i < sizeof($coremods); $i++) if(substr($coremods[$i], -4) == ".zip" || substr($coremods[$i], -4) == ".jar")
		{
			$echo2 = $coremods[$i].":>".md5_file("clients/".$client."/coremods/".$coremods[$i])."<:>"; $colCoreMods++;
			echo str_replace(' ', '%20', $echo2);
		} if($colCoreMods == 0) echo "nomods";

	} else
  
	if($action == 'getpersonal')
	{
		@$realmoney = $row[$db_columnMoney];

		if($iconregistered)
		{	
			$stmt = $db->prepare("SELECT username,balance FROM iConomy WHERE username= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$iconmoney = $row['balance'];
		} else $iconmoney = "0.0";
		
		if($canBuyVip || $canBuyPremium)
		{
			
			$stmt = $db->prepare("SELECT name,permission,value FROM permissions WHERE name= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$datetoexpire = 0;
			if(!$stmt) $ugroup = 'User'; else
			{
				$group = $row['permission'];
				if($group == 'group-premium-until')
				{
					$ugroup = 'Premium';
					$datetoexpire = $row['value'];
				} else if($group == 'group-vip-until')
				{
					$ugroup = 'VIP';
					$datetoexpire = $row['value'];
				} else $ugroup = 'User';
			}
		} else
		{
			$datetoexpire = 0;
			$ugroup = 'User';
		}
	
		if($canUseJobs)
		{
			$stmt = $db->prepare("SELECT job FROM jobs WHERE username= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$sql = $stmt->fetch(PDO::FETCH_ASSOC);
			$query = $sql['job'];
			if($query == '') { $jobname = "Безработный"; $joblvl = 0; $jobexp = 0; } else
			{
				$stmt = $db->prepare("SELECT * FROM jobs WHERE username= :login");
				$stmt->bindValue(':login', $login);
				$stmt->execute();
				
				while($data = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					if ($data["job"] === 'Miner') $data["job"] = 'Шахтер';
					if ($data["job"] === 'Woodcooter') $data["job"] = 'Лесоруб';
					if ($data["job"] === 'Builder') $data["job"] = 'Строитель';
					if ($data["job"] === 'Digger') $data["job"] = 'Дигер';
					if ($data["job"] === 'Farmer') $data["job"] = 'Фермер';
					if ($data["job"] === 'Hunter') $data["job"] = 'Охотник';
					if ($data["job"] === 'Fisherman') $data["job"] = 'Рыбак';
					if ($data["job"] === 'Weaponsmith') $data["job"] = 'Оружейник';
					
					$jobname = $data['job'];
					$joblvl = $data["level"];
					$jobexp = $data["experience"];
				}
			}
		} else { $jobname = "nojob"; $joblvl = -1; $jobexp = -1; }
		
		$canUploadSkin 		= (int)$canUploadSkin;
		$canUploadCloak		= (int)$canUploadCloak;
		$canBuyVip	   		= (int)$canBuyVip;
		$canBuyPremium 		= (int)$canBuyPremium;
		$canBuyUnban   		= (int)$canBuyUnban;
		$canActivateVaucher = (int)$canActivateVaucher;
		$canExchangeMoney	= (int)$canExchangeMoney;
	
		if($canBuyUnban == 1)
		{
		    $ty = 2;
			$stmt = $db->prepare("SELECT name,type FROM $banlist WHERE name= :login and type<'$ty'");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$sql2 = $stmt->fetch(PDO::FETCH_ASSOC);
			$query2 = $sql2['name'];
			if(strcasecmp($query2, $login) == 0) $ugroup = "Banned";
		}
		
		echo "$canUploadSkin$canUploadCloak$canBuyVip$canBuyPremium$canBuyUnban$canActivateVaucher$canExchangeMoney<:>$iconmoney<:>$realmoney<:>$cloakPrice<:>$vipPrice<:>$premiumPrice<:>$unbanPrice<:>$exchangeRate<:>$ugroup<:>$datetoexpire<:>$jobname<:>$joblvl<:>$jobexp";
	} else
//============================================Функции ЛК====================================//

	if($action == 'activatekey')
	{
		@$key = $_POST['key'];
		$stmt = $db->prepare("SELECT * FROM `$db_tableMoneyKeys` WHERE `$db_columnKey` = '$key'");
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$amount = $row[$db_columnAmount];
		if($amount)
		{
			$stmt = $db->prepare("UPDATE `$db_table` SET $db_columnMoney = $db_columnMoney + $amount WHERE $db_columnUser= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$stmt = $db->prepare("DELETE FROM `$db_tableMoneyKeys` WHERE `$db_columnKey` = '$key'");
			$stmt->execute();	
			$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);	
			$money = $row[$db_columnMoney];
			echo "success:".$money;
		} else echo "keyerr";
	} else

	if($action == 'uploadskin')
	{
		if(!is_uploaded_file($_FILES['ufile']['tmp_name'])) die("nofile");
		$imageinfo = getimagesize($_FILES['ufile']['tmp_name']);
		if($imageinfo['mime'] != 'image/png' || $imageinfo["0"] != '64' || $imageinfo["1"] != '32') die("skinerr");
		$uploadfile = "".$uploaddirs."/".$login.".png";
		if(move_uploaded_file($_FILES['ufile']['tmp_name'], $uploadfile)) echo "success";
		else echo "fileerr";
	} else
	
	if($action == 'uploadcloak')
	{
		$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$query = $row[$db_columnMoney]; if($query < $cloakPrice) die("moneyno");
		if(!is_uploaded_file($_FILES['ufile']['tmp_name'])) die("nofile");
		$imageinfo = getimagesize($_FILES['ufile']['tmp_name']);
		$go = false;
		if(($imageinfo['mime'] != 'image/png' || $imageinfo["0"] == '64' || $imageinfo["1"] == '32')){
		$go = true;
		} else echo 'cloakerr';
		if($go) {
		$uploadfile = "".$uploaddirp."/".$login.".png";
		if(!move_uploaded_file($_FILES['ufile']['tmp_name'], $uploadfile)) die("fileerr");
		$stmt = $db->prepare("UPDATE $db_table SET $db_columnMoney = $db_columnMoney - $cloakPrice WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		echo "success:".$row[$db_columnMoney];
	}} else
	
	if($action == 'buyvip')
	{
		$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$query = $row[$db_columnMoney]; if($query < $vipPrice) die("moneyno");
	    $stmt = $db->prepare("SELECT name,permission FROM permissions WHERE name= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$group = $row['permission'];
		$pexdate = time() + 2678400;
		if($group == 'group-vip-until')
		{	
			$stmt = $db->prepare("UPDATE $db_table SET $db_columnMoney=$db_columnMoney-$vipPrice WHERE $db_columnUser= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$stmt = $db->prepare("UPDATE permissions SET value=value+2678400 WHERE name= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
		} else
		{
			$stmt = $db->prepare("INSERT INTO permissions (id, name, type, permission, world, value) VALUES (NULL, :login, '1', 'group-vip-until', ' ', '$pexdate')");
			$stmt->bindValue(':login', $login);
			$stmt->execute();	
			$stmt = $db->prepare("INSERT INTO permissions_inheritance (id, child, parent, type, world) VALUES (NULL, :login, 'vip', '1', NULL)");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$stmt = $db->prepare("UPDATE $db_table SET $db_columnMoney=$db_columnMoney-$vipPrice WHERE $db_columnUser= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
		}
			$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			echo "success:".$row[$db_columnMoney].":";
			$stmt = $db->prepare("SELECT name,permission,value FROM permissions WHERE name= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			echo $row['value'];
	} else
	
	if($action == 'buypremium')
	{
		$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$query = $row[$db_columnMoney]; if($query < $premiumPrice) die("moneyno");
		$stmt = $db->prepare("SELECT name,permission FROM permissions WHERE name= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$group = $row['permission'];
		$pexdate = time() + 2678400;
		if($group == 'group-premium-until')
		{
			$stmt = $db->prepare("UPDATE $db_table SET $db_columnMoney=$db_columnMoney-$premiumPrice WHERE $db_columnUser= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$stmt = $db->prepare("UPDATE permissions SET value=value+2678400 WHERE name= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
		} else
		{
			$stmt = $db->prepare("INSERT INTO permissions (id, name, type, permission, world, value) VALUES (NULL, :login, '1', 'group-premium-until', ' ', '$pexdate')");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$stmt = $db->prepare("INSERT INTO permissions_inheritance (id, child, parent, type, world) VALUES (NULL, :login, 'premium', '1', NULL)");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$stmt = $db->prepare("UPDATE $db_table SET $db_columnMoney=$db_columnMoney-$premiumPrice WHERE $db_columnUser= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
		}
			$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			echo "success:".$row[$db_columnMoney].":";
			$stmt = $db->prepare("SELECT name,permission,value FROM permissions WHERE name= :login");
			$stmt->bindValue(':login', $login);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			echo $row['value'];
	} else
	
	if($action == 'buyunban')
	{
		$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$sql1 = $stmt->fetch(PDO::FETCH_ASSOC);
		$query1 = $sql1[$db_columnMoney];
		$stmt = $db->prepare("SELECT name FROM $banlist WHERE name= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$sql2 = $stmt->fetch(PDO::FETCH_ASSOC);
		$query2 = $sql2['name'];
		if(strcasecmp($query2, $login) == 0)
		{
			if($query1 >= $unbanPrice)
			{
				if($canBuyVip || $canBuyPremium)
				{
					$stmt = $db->prepare("SELECT name,permission,value FROM permissions WHERE name= :login");
					$stmt->bindValue(':login', $login);
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
					$group = $row['permission'];
					if(!$stmt) $ugroup = 'User'; else
					{
						if($group == 'group-premium-until') $ugroup = 'Premium';
						else if($group == 'group-vip-until') $ugroup = 'VIP';
						else $ugroup = 'User';
					}
				} else $ugroup = 'User';
					$stmt = $db->prepare("DELETE FROM $banlist WHERE name= :login");
					$stmt->bindValue(':login', $login);
					$stmt->execute();
					$stmt = $db->prepare("UPDATE $db_table SET $db_columnMoney=$db_columnMoney-$unbanPrice WHERE $db_columnUser= :login");
					$stmt->bindValue(':login', $login);
					$stmt->execute();
					$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
					$stmt->bindValue(':login', $login);
					$stmt->execute();
					$row = $stmt->fetch(PDO::FETCH_ASSOC);
				echo "success:".$row[$db_columnMoney].":".$ugroup;
			} else die('moneyno');
		} else die("banno");
	} else

	if($action == 'exchange')
	{
		@$wantbuy = (int)$_POST['buy'];
		$gamemoneyadd = ($wantbuy * $exchangeRate);
		$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$query = $row[$db_columnMoney];
		if($wantbuy == '' || $wantbuy < 1) die("ecoerr");
		if(!$iconregistered) die("econo");
		if($query < $wantbuy) die("moneyno");
		$stmt = $db->prepare("UPDATE iConomy SET balance = balance + $gamemoneyadd WHERE username= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$stmt = $db->prepare("UPDATE $db_table SET $db_columnMoney = $db_columnMoney - $wantbuy WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$stmt = $db->prepare("SELECT $db_columnUser,$db_columnMoney FROM $db_table WHERE $db_columnUser= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$money = $row[$db_columnMoney];
		$stmt = $db->prepare("SELECT username,balance FROM iConomy WHERE username= :login");
		$stmt->bindValue(':login', $login);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$iconmoney = $row['balance'];
		echo "success:".$money.":".$iconmoney;
	} else echo "Запрос составлен неверно";
	
	} catch(PDOException $pe) {
		die("errorsql".$logger->WriteLine($log_date.$pe));  //вывод ошибок MySQL в m.log
	}
	//===================================== Вспомогательные функции ==================================//

	function xorencode($str, $key)
	{
		while(strlen($key) < strlen($str))
		{
			$key .= $key;
		}
		return $str ^ $key;
	}

	function strtoint($text)
	{
		$res = "";
		for ($i = 0; $i < strlen($text); $i++) $res .= ord($text{$i}) . "-";
		$res = substr($res, 0, -1);
		return $res;
	}

	function hash_name($ncrypt, $realPass, $postPass, $salt) {
		$cryptPass = false;
		
		if ($ncrypt === 'hash_xauth')
		{
				$saltPos = (strlen($postPass) >= strlen($realPass) ? strlen($realPass) : strlen($postPass));
				$salt = substr($realPass, $saltPos, 12);
				$hash = hash('whirlpool', $salt . $postPass);
				$cryptPass = substr($hash, 0, $saltPos) . $salt . substr($hash, $saltPos);
		}

		if ($ncrypt === 'hash_md5' or $ncrypt === 'hash_launcher')
		{
				$cryptPass = md5($postPass);
		}

		if ($ncrypt === 'hash_dle')
		{
				$cryptPass = md5(md5($postPass));
		}

		if ($ncrypt === 'hash_cauth')
		{
				if (strlen($realPass) < 32)
				{
						$cryptPass = md5($postPass);
						$rp = str_replace('0', '', $realPass);
						$cp = str_replace('0', '', $cryptPass);
						(strcasecmp($rp,$cp) == 0 ? $cryptPass = $realPass : $cryptPass = false);
				}
				else $cryptPass = md5($postPass);
		}

		if ($ncrypt === 'hash_authme')
		{
				$ar = preg_split("/\\$/",$realPass);
				$salt = $ar[2];
				$cryptPass = '$SHA$'.$salt.'$'.hash('sha256',hash('sha256',$postPass).$salt);
		}

		if ($ncrypt === 'hash_joomla')
		{
				$parts = explode( ':', $realPass);
				$salt = $parts[1];
				$cryptPass = md5($postPass . $salt) . ":" . $salt;
		}
				
		if ($ncrypt === 'hash_imagecms')
		{
		        $majorsalt = '';
				if ($salt != '') {
					$_password = $salt . $postPass;
				} else {
					$_password = $postPass;
				}
				
				$_pass = str_split($_password);
				
				foreach ($_pass as $_hashpass) {
					$majorsalt .= md5($_hashpass);
				}
				
				$cryptPass = crypt(md5($majorsalt), $realPass);
		}

		if ($ncrypt === 'hash_joomla_new' or $ncrypt === 'hash_wordpress' or $ncrypt === 'hash_xenforo')
		{
		
				if($ncrypt === 'hash_xenforo' and $salt!==false) {
					return $cryptPass = hash('sha256', hash('sha256', $postPass) . $salt);
				}
				
				$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				$cryptPass = '*0';
				if (substr($realPass, 0, 2) == $cryptPass)
					$cryptPass = '*1';

				$id = substr($realPass, 0, 3);
				# We use "$P$", phpBB3 uses "$H$" for the same thing
				if ($id != '$P$' && $id != '$H$')
					return $cryptPass = crypt($postPass, $realPass);

				$count_log2 = strpos($itoa64, $realPass[3]);
				if ($count_log2 < 7 || $count_log2 > 30)
					return $cryptPass = crypt($postPass, $realPass);

				$count = 1 << $count_log2;

				$salt = substr($realPass, 4, 8);
				if (strlen($salt) != 8)
					return $cryptPass = crypt($postPass, $realPass);

					$hash = md5($salt . $postPass, TRUE);
					do {
						$hash = md5($hash . $postPass, TRUE);
					} while (--$count);

				$cryptPass = substr($realPass, 0, 12);
				
				$encode64 = '';
				$i = 0;
				do {
					$value = ord($hash[$i++]);
					$encode64 .= $itoa64[$value & 0x3f];
					if ($i < 16)
						$value |= ord($hash[$i]) << 8;
					$encode64 .= $itoa64[($value >> 6) & 0x3f];
					if ($i++ >= 16)
						break;
					if ($i < 16)
						$value |= ord($hash[$i]) << 16;
					$encode64 .= $itoa64[($value >> 12) & 0x3f];
					if ($i++ >= 16)
						break;
					$encode64 .= $itoa64[($value >> 18) & 0x3f];
				} while ($i < 16);
				
				$cryptPass .= $encode64;

				if ($cryptPass[0] == '*')
					$cryptPass = crypt($postPass, $realPass);
		}
		
		if ($ncrypt === 'hash_ipb')
		{
				$cryptPass = md5(md5($salt).md5($postPass));
		}
		
		if ($ncrypt === 'hash_punbb')
		{
				$cryptPass = sha1($salt.sha1($postPass));
		}

		if ($ncrypt === 'hash_vbulletin')
		{
				$cryptPass = md5(md5($postPass) . $salt);
		}

		if ($ncrypt === 'hash_drupal')
		{
				$setting = substr($realPass, 0, 12);
				$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
				$count_log2 = strpos($itoa64, $setting[3]);
				$salt = substr($setting, 4, 8);
				$count = 1 << $count_log2;
				$input = hash('sha512', $salt . $postPass, TRUE);
				do $input = hash('sha512', $input . $postPass, TRUE);
				while (--$count);

				$count = strlen($input);
				$i = 0;
		  
				do
				{
						$value = ord($input[$i++]);
						$cryptPass .= $itoa64[$value & 0x3f];
						if ($i < $count) $value |= ord($input[$i]) << 8;
						$cryptPass .= $itoa64[($value >> 6) & 0x3f];
						if ($i++ >= $count) break;
						if ($i < $count) $value |= ord($input[$i]) << 16;
						$cryptPass .= $itoa64[($value >> 12) & 0x3f];
						if ($i++ >= $count) break;
						$cryptPass .= $itoa64[($value >> 18) & 0x3f];
				} while ($i < $count);
				$cryptPass =  $setting . $cryptPass;
				$cryptPass =  substr($cryptPass, 0, 55);
		}
		
		return $cryptPass;
	}

?>
