<?php
	include '../data/rules.php';
	session_start();
?>
<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>Мои замки.</title>
</head>
<body topmargin=5 leftmargin=2 bgcolor="#dfdfb0"><TABLE width="760" align="center">
<?php
/*
 * login.php
 * принимает от клиента авторизационные параметры и устанавливает сессионные переменные, при положительной авторизации выдает стартовую страницу 
 */
 
	if (!isset($_SESSION['UID'])) {
		if (!isset($_REQUEST['myusername']) || !isset($_REQUEST['mypassword']) || ($_REQUEST['myusername'] == "") || ($_REQUEST['mypassword'] == "") ) {
			echo "<script type=\"text/javascript\">";
			echo "alert('Недостаточно данных для авторизации.');";
			echo "document.location.href='/index.php';";
			echo "</script>";
			exit;
		}

		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$link = mysqli_connect('mysql', 'decline', 'dfdbkjy5', 'decline');
		if (mysqli_connect_errno()) {
			die('Failed to connect to MySQL: '.mysqli_connect_error());
		}

		$login = mysqli_real_escape_string($link, $_REQUEST['myusername']);
		$password = mysqli_real_escape_string($link, $_REQUEST['mypassword']);

		$query = "SELECT `id` FROM `gamers` WHERE `username`='{$login}' AND `password`='{$password}' LIMIT 1";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}

		if(mysqli_num_rows($result) == 0) {
			mysqli_close($link);
			echo "<script type=\"text/javascript\">";
			echo "alert('Пользователь не найден, повторите вход.');";
			echo "document.location.href='/index.php';";
			echo "</script>";
			exit;
		}

		$row = mysqli_fetch_assoc($result);
		$_SESSION['UID']=$row['id'];
		$_SESSION['myusername']=$_REQUEST['myusername'];
		$_SESSION['mypassword']=$_REQUEST['mypassword'];
		mysqli_close($link);
	}

	$link = mysqli_connect('mysql', 'decline', 'dfdbkjy5','decline');
	if (mysqli_connect_errno()) {
		die('Failed to connect to MySQL: '.mysqli_connect_error());
	}

	// если передавался параментр создания замка
    if (isset($_POST["newcastlename"]) || ($_POST["newcastlename"] != NULL)) {
		$query = "SELECT * FROM `castles` WHERE `castle_name`='{$_POST['newcastlename']}'";
		$result = mysqli_query($link, $query);
	    if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}

		if (mysqli_num_rows($result) != 0) { //  такой замок уже есть
			mysqli_close($link);
			echo "<script type=\"text/javascript\">";
			echo "alert('Такой замок уже есть или недопустимое имя.');";
			echo "document.location.href='/login.php';";
			echo "</script>";
			exit;
		}
		else {	// алгоритм поиска свободного места на карте.	    
			get_map(0,0);	// инициализируем width и height	    
	    	$query = "SELECT MAX(x) FROM `castles`";	
	    	$result = mysqli_query($link, $query);
	    	if (!$result) {
				die('Ошибка запроса: '.mysqli_error());
			}
			$row = mysqli_fetch_row($result);
			if ($row[0] != NULL) {
		    	$max_x = $row[0] + 5;
		    	if ($max_x >= $width) {	// но максимальное значение не может быть за пределами поля
					$max_x = $width - 5;
				}
			}
			else {
		    	$max_x = 10;	// если база пустая то клетка 5-10
			}
	    
			//echo "MAXX=".$max_x." ";		    	
	    		    
	    	$query = "SELECT MAX(y) FROM `castles`";
	    	$result = mysqli_query($link, $query);
	    	if (!$result) {
				die('Ошибка запроса: '.mysqli_error());
			}
    		$row = mysqli_fetch_row($result);
			if ($row[0] != NULL) {
		    	$max_y = $row[0] + 5;
		    	if ($max_y >= $height) {
					$max_y = $height - 5;		    
		    	}
			}
			else {
		    	$max_y = 10;
		    }

			//echo "MAXY=".$max_y." ";		    	
		    
			for($i = 0; $i < 50; $i++) {    // дается 50 попыток
		    	$set_x = rand(5, $max_x);    
		    	$set_y = rand(5, $max_y);

				//echo "SETX=".$set_x." ";
				//echo "SETY=".$set_y." ";

		    	if (get_map($set_x, $set_y) > 2) {	 // замок ставим только на суше
					$query = "SELECT * FROM `decline`.`castles` WHERE x BETWEEN ".($set_x-3)." AND ".($set_x+3)." AND y BETWEEN ".($set_y-3)." AND ".($set_y+3);
					$result = mysqli_query($link, $query);
   	    			if (!$result) {
						die('Ошибка запроса: '.mysqli_error());
					}

		    		if (mysqli_num_rows($result) == 0) {	// в окрестностях +3 -3 нет замков
			    		$query = "INSERT INTO `castles` VALUES(null,".$_SESSION['UID'].",'".$_POST['newcastlename']."','Люди',".$set_x.",".$set_y.",100,50.00,1.0,10,0,'',0,0,null,12,now())";
			 			$result = mysqli_query($link, $query);
			    		if (!$result) {
							die('Ошибка запроса: '.mysqli_error());
						}
                		$query = "SELECT * FROM `castles` WHERE `castle_name`='".$_POST['newcastlename']."'";
			    		$result = mysqli_query($line, $query);
   	    				if (!$result) {
							die('Ошибка запроса: '.mysqli_error());
						}

			    		if (mysqli_num_rows($result) > 0) {		// добавить пикеносца
    						$row = mysqli_fetch_assoc($result);
		            		$query = "insert into `decline`.`units` values(null,".$row['id'].",'".$row['castle_name']."',1,0,5.00,0.5,100,3,1,1,".$set_x.",".$set_y.")";
    						$result = mysqli_query($link, $query);
   	    					if (!$result) {
								die('Ошибка запроса: '.mysqli_error());
							}
							$query = "insert into `decline`.`units` values(null,".$row['id'].",'".$row['castle_name']."',2,0,10.00,0.8,100,3,1,2,".$set_x.",".$set_y.")";
    						$result = mysqli_query($link, $query);
   	    					if (!$result) {
								die('Ошибка запроса: '.mysqli_error());
							}

							$query = "insert into `decline`.`messages` values(null,NOW(),0,'Decline',".$row['id'].",'".$row['castle_name']."','Приветствую, вас молодой хозяин! Теперь это ваш замок, ваш город. У вас в подчинении 100 человек и 2 войска. Вам дается подъемные 50 золота . Вокруг много опасностей, но так же и много возможностей. Правьте мудро, и когда нибудь вы станете сильнейшим!')";
    						$result = mysqli_query($query);
   	    					if (!$result) {
								die('Ошибка запроса: '.mysqli_error());
							}
		    	    	}
    			    	break;
					}
//					else {
//			    		echo "Рядом замок.";
//					}    
		    	}
//		    	else {
//					echo "Попали на воду.";
//		    	}
			}
			if($i >= 50) {
		    	mysqli_close($link);		
		    	echo "<script type=\"text/javascript\">";
		    	echo "alert('Нет свободного места на карте. Попробуйте позже.');";
		    	echo "document.location.href='/login.php';";
		    	echo "</script>";
		    	exit;		
			}		
	    }
	}

	$query = "SELECT * FROM `decline`.`castles` WHERE `owner_id`='{$_SESSION['UID']}'";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}
	echo 	"Список замков пользователя ",$_SESSION['myusername'],"<br>
		<TABLE align=center border=1 cellPadding=0 cellSpacing=0 width=760>
		<TBODY>
		<TR align=middle bgColor=#dddd00 height=40>
		<TD>ID</TD><TD>Название</TD><TD>X</TD><TD>Y</TD><TD>Население</TD></TR>";
		
	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		$row = mysqli_fetch_assoc($result);
		echo "<TR><TD>",$row['id'],"</TD><TD><a href=/world.php?id=".$row['id'].">".$row['castle_name']."</a></TD><TD>",$row['x'],"</TD><TD>",$row['y'],"</TD><TD>",$row['population'],"</TD></TR>";
	}
	if(mysqli_num_rows($result) < 5) {	// колличество замков пока ограничено 5
	    echo "<TR><form name=newcastle method=post action=login.php><TD></TD>
	          <TD width=500><input size=64 name=newcastlename type=text id=newcastlename><input type=submit name=Submit value=Создать></TD>
	          <TD></TD><TD></TD><TD></TD>
 	          </TR>";	
	}
	mysqli_close($link);
?>
</TABLE>
</body>
</html>
