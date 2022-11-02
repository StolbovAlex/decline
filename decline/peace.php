<?php
/*
 * peace.php
 * выдает рабочее поле для установки мирных соглашений
 */
   session_start();
   if(!isset($_SESSION['UID'])) {
		echo "<script type=\"text/javascript\">";
		echo "alert('Необходимо авторизоваться.');";
		echo "document.location.href='/index.php';";
		echo "</script>";
		exit;
   }
?>
<HEAD><TITLE>DECLINE - Мирные соглашения</TITLE><LINK 
<META content=no-cache http-equiv=pragma>
<META content="text/html; charset=utf-8" http-equiv=Content-Type>
<BODY bgColor=#dfdfb0>
<?php
    // если выбран замок
   if(isset($_GET["id"])) {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		$link = mysqli_connect('mysql', 'decline', 'dfdbkjy5', 'decline');
		if (mysqli_connect_errno()) {
			die('Failed to connect to MySQL: '.mysqli_connect_error());
		}

		$query = "SELECT * FROM `castles` WHERE `id`='{$_GET['id']}' AND `owner_id`='{$_SESSION['UID']}'";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}

		if(mysqli_num_rows($result) == 0) {
			echo "<script type=\"text/javascript\">";
			echo "alert('Для доступа к запрошенным рессурсам необходимо авторизоваться.');";
			echo "document.location.href='/index.php';";
			echo "</script>";
			exit;
		}	
		$row = mysqli_fetch_assoc($result);

		// команды для метода GET
		if(isset($_GET['peace_name']) && isset($_GET['acceptpeace'])) {			// предложено мирное соглашение
    	$query1 = "SELECT * FROM `castles` WHERE `castle_name`='{$_GET['peace_name']}'";    
	   	$result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}

	   	if(mysqli_num_rows($result1) != 0) {	
	   		$row1 = mysqli_fetch_assoc($result1);	// данные замка кому предлагается мирное соглашение
	   		if ($row['id'] != $row1['id']) { // проверка на соглашение самому себе
    	   	$query2 = "SELECT * FROM `peace` WHERE (`castle_A`={$row['id']} AND `castle_B`={$row1['id']}) OR (`castle_A`={$row1['id']} AND `castle_B`={$row['id']})"; // повторное мирное соглашение    
	   			$result2 = mysqli_query($link, $query2);
					if (!$result2) {
						die('Ошибка запроса: '.mysqli_error());
					}

	   			if(mysqli_num_rows($result2) == 0) {	// нет мирных солашений
    					$query2 = "INSERT INTO `peace` VALUES(null, {$row['id']},'{$row['castle_name']}',{$row1['id']},'{$row1['castle_name']}',1,0,now())"; // true только тому кто запрашивает
      	   		$result2 = mysqli_query($link, $query2);
						if (!$result2) {
							die('Ошибка запроса: '.mysqli_error());
						}
		    			$query2 = "INSERT INTO `messages` VALUES(null,now(),{$row['id']},'{$row['castle_name']}',{$row1['id']},'{$row1['castle_name']}','Предложено мирное соглашение замку {$row['castle_name']}.')";
		    			$result2 = mysqli_query($link, $query2);
		    			if (!$result2) {
							die('Ошибка запроса: '.mysqli_error());
 						}	
    				}
  					else {
						echo "<script type=\"text/javascript\">";
						echo "alert('Предложение мира этому замку уже есть .');";
						echo "</script>";
					}
				}
  				else {
					echo "<script type=\"text/javascript\">";
					echo "alert('Предложение мира самому себе похвально.');";
					echo "</script>";
				}
			}	
	   	else {
				echo "<script type=\"text/javascript\">";
				echo "alert('Такой замок не найден.');";
				echo "</script>";
	   	}
   	}

		if(isset($_GET['peace_id']) && isset($_GET['acceptpeace'])) {			// подтверждаем мирное соглашение
    	   $query1 = "SELECT * FROM `peace` WHERE `id`={$_GET['peace_id']}";    
	   	$result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}

	   	if(mysqli_num_rows($result1) != 0) {
	   		$row1 = mysqli_fetch_assoc($result1);
    			$query2 = "UPDATE `peace` SET `apply_B`=1 WHERE `id`={$_GET['peace_id']}";
    	   	$result2 = mysqli_query($link, $query2);
				if (!$result2) {
					die('Ошибка запроса: '.mysqli_error());
				}				
		    	$query2 = "INSERT INTO `messages` VALUES(null,NOW(),'{$row1['castle_B']}','{$row1['castle_nameB']}','{$row1['castle_A']}','{$row1['castle_nameA']}','Подписано мирное соглашение с замком {$row1['castle_nameB']}.')";
		    	$result2 = mysqli_query($link, $query2);
		    	if (!$result2) {
					die('Ошибка запроса: '.mysqli_error());
 				}	
		    	$query2 = "INSERT INTO `messages` VALUES(null,NOW(),'{$row1['castle_A']}','{$row1['castle_nameA']}','{$row1['castle_B']}','{$row1['castle_nameB']}','Подписано мирное соглашение с замком {$row1['castle_nameA']}.')";
		    	$result2 = mysqli_query($link, $query2);
		    	if (!$result2) {
					die('Ошибка запроса: '.mysqli_error());
 				}
			}
		}	

		if(isset($_GET['peace_id']) && isset($_GET['rejectpeace'])) {			// отменяем мирное соглашение
    	   $query1 = "SELECT * FROM `peace` WHERE `id`={$_GET['peace_id']}";    
	   	$result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}

	   	if(mysqli_num_rows($result1) != 0) {
	   		$row1 = mysqli_fetch_assoc($result1);
    			$query2 = "DELETE FROM `peace` WHERE `id`='{$row1['id']}'";
    	   	$result2 = mysqli_query($link, $query2);
				if (!$result2) {
					die('Ошибка запроса: '.mysqli_error());
				}				
		    	$query2 = "INSERT INTO `messages` VALUES(null,NOW(),'{$row1['castle_B']}','{$row1['castle_nameB']}','{$row1['castle_A']}','{$row1['castle_nameA']}','Мирное соглашение с замком {$row1['castle_nameB']} разорвано.')";
		    	$result2 = mysqli_query($link, $query2);
		    	if (!$result2) {
					die('Ошибка запроса: '.mysqli_error());
 				 }	
		    	$query2 = "INSERT INTO `messages` VALUES(null,NOW(),'{$row1['castle_A']}','{$row1['castle_nameA']}','{$row1['castle_B']}','{$row1['castle_nameB']}','Мирное соглашение с замком {$row1['castle_nameA']} разорвано.')";
		    	$result2 = mysqli_query($link, $query2);
		    	if (!$result2) {
					die('Ошибка запроса: '.mysqli_error());
 				 }	
    		}
	   	else {
				echo "<script type=\"text/javascript\">";
				echo "alert('Соглашение о фире отсутствует.');";
				echo "</script>";
	   	}
   	}

		echo "
	<TABLE align=center width=760>
	<TBODY>
	    <TR>
	    <TD align=middle><STRONG><FONT size=4>Мирные соглашения</FONT></STRONG></TD>
	    <TD width=468></TD>
    	    <TD align=middle><BR>Время на Сервере<BR><B>
	    <DIV id=clock></DIV>
	    <SCRIPT language=javascript src=/clock.js></SCRIPT>
	    <SCRIPT language=javascript><!--
	    var hours = ",date('H'),";
	    var minutes = ",date('i'),";
	    var seconds = ",date('s'),";
	    startclock();
	    //--></SCRIPT>
	    </B></TD></TR></TBODY></TABLE><BR>
	    <CENTER><STRONG>У вас ",$row['gold']," золота ",$row['population']," населения</STRONG></CENTER>
	    <TABLE align=center border=1 width=760>
	    <TBODY>
	    <TR>
	    <TD align=middle bgColor=#ffff99>
	    <A href=/city.php?id=",$row['id'],">Назад в замок</A>&nbsp;&nbsp;
    	    <A href=/world.php?id=",$row['id'],">Карта мира</A>&nbsp;&nbsp;
	    <A href=/logout.php>&nbsp;Выход из игры</A> 
	    </TD></TR></TBODY></TABLE>";

		$query1 = "SELECT * FROM `peace` WHERE `castle_A`='{$_GET['id']}' OR `castle_B`='{$_GET['id']}'";	// castleA - если предлогал соглашение, castleB е сли принмал
		$result1 = mysqli_query($link, $query1);
		if (!$result1) {
			die('Ошибка запроса: '.mysqli_error());
		}
 	    
		echo "<TABLE align=center border=1 cellPadding=0 cellSpacing=0 width=760>";
		for($i = 0; $i < mysqli_num_rows($result1); $i++) {	// распечатка всех союзов
	   	$row1 = mysqli_fetch_assoc($result1);
			if ($row['id'] == $row1['castle_A']) { 
				if($row1['apply_A'] == 1) {								// Вы предложили подписать мирное соглашение
					if($row1['apply_B'] == 1) {							// и оно подписано
						echo "  
						<TR align=center bgColor=#dddd00 height=40><TD>У Вас мирное соглашение с ",$row1['castle_nameB'],"</TD> 
	    				<FORM action=peace.php method=get>
	    				<TD align=center width=200>
	    				<INPUT type=hidden name=peace_id value=",$row1['id'],">
	    				<INPUT type=hidden name=id value=",$row['id'],">
						<TD><INPUT name=rejectpeace type=submit value=Разорвать?></TD>
						</FORM></TR>";
					}
					else {													// ответная сторона его не подписала
						echo " 
						<TR align=center bgColor=#dddd00 height=40><TD>Вы предложили мирное соглашение ",$row1['castle_nameB'],"</TD> 
	    				<FORM action=peace.php method=get>
	    				<TD align=center width=200>
	    				<INPUT type=hidden name=peace_id value=",$row1['id'],">
	    				<INPUT type=hidden name=id value=",$row['id'],">
						<TD><INPUT name=rejectpeace type=submit value=Отказться?></TD>
						</FORM></TR>";
					}
				}
			}

			if ($row['id'] == $row1['castle_B']) {
				if($row1['apply_A'] == 1) {								// Вам предложили подписать мирное соглашение
					if($row1['apply_B'] == 1) {							// и вы его подписали
						echo "  
						<TR align=center bgColor=#dddd00 height=40><TD>У Вас мирное соглашение с ",$row1['castle_nameA'],"</TD> 
	    				<FORM action=peace.php method=get>
	    				<TD align=center width=200>
	    				<INPUT type=hidden name=peace_id value=",$row1['id'],">
	    				<INPUT type=hidden name=id value=",$row['id'],">
						<TD><INPUT name=rejectpeace type=submit value=Разорвать?></TD>
						</FORM></TR>";
					}
					else {													// вы пока думаете над ответом
						echo " 
						<TR align=center bgColor=#dddd00 height=40><TD>Вам предложили мирное соглашение ",$row1['castle_nameB'],"</TD> 
	    				<FORM action=peace.php method=get>
	    				<TD align=center width=200>
	    				<INPUT type=hidden name=peace_id value=",$row1['id'],">
	    				<INPUT type=hidden name=id value=",$row['id'],">
						<TD><INPUT name=acceptpeace type=submit value=Принять?></TD>
						</FORM></TR>";
					}
				}
			}
		}
		echo "
		<TR align=center bgColor=#dddd00 height=40><TD>Предложить Мирное соглашение </TD>
	   <FORM action=peace.php method=get>
	   <TD align=center width=200><INPUT name=peace_name size=32 value=''></TD>
	   <INPUT type=hidden name=id value=",$row['id'],">
		<TD><INPUT name=acceptpeace type=submit value=Отправить!></TD>
	   </FORM></TR>;
		</TABLE></BODY></HTML>";
		mysqli_close($link);
	}
	else {
		echo "<script type=\"text/javascript\">";
		echo "alert('Замок не выбран.');";
		echo "document.location.href='/login.php';";
		echo "</script>";
		exit;
	}
?>
