<?php
/*
 * world.php
 * выдает рабочее поле для замка
 */
	session_start();
	if(!isset($_SESSION['UID'])) {
		echo "<script type=\"text/javascript\">";
		echo "alert('Необходимо авторизоваться.');";
		echo "document.location.href='/index.php';";
		echo "</script>";
		exit;
	};
?>


<HTML><HEAD><TITLE>DECLINE - Сообщения</TITLE><LINK 
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
		$query = "SELECT * FROM `decline`.`castles` WHERE `id`='{$_GET['id']}' AND `owner_id`='{$_SESSION['UID']}'";
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
		
		if(isset($_GET['recv_name']) && isset($_GET['message'])) {
		    $query1 = "SELECT * FROM `decline`.`castles` WHERE `castle_name`='{$_GET['recv_name']}'";
		    $result1 = mysqli_query($line, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}

		    if(mysqli_num_rows($result1) == 0) {
				echo "<script type=\"text/javascript\">";
				echo "alert('Такого получателя не существует.');";
				echo "</script>";
		    }
		    else {
				$row1 = mysqli_fetch_assoc($result1);
				$query1 = "insert into `messages` values(null,now(),".$row['id'].",'".$row['castle_name']."',".$row1['id'].",'".$row1['castle_name']."','".$_GET['message']."')";
				$result1 = mysqli_query($link, $query1);
				if (!$result1) {
					die('Ошибка запроса: '.mysqli_error());
				}
		    }
		}
		echo "
		    <TABLE align=center width=760>
		      <TBODY>
		      <TR>
		        <TD align=middle><STRONG><FONT size=4>Сообщения замка</FONT></STRONG></TD>
		        <TD width=468></TD>
		        <TD align=middle><BR>Время на Сервере<BR><B>
		          <DIV id=clock></DIV>
			<SCRIPT language=javascript src=/clock.js></SCRIPT>
			<SCRIPT language=javascript><!--
	    		var hours = ",date("H"),"
    			var minutes = ",date("i"),"
			var seconds = ",date("s"),"
		        startclock();
			//--></SCRIPT>
		        </B></TD></TR></TBODY></TABLE><BR>
		        <CENTER><STRONG>Сообщения замка ",$row['castle_name']."</STRONG></CENTER>
		        <TABLE align=center border=1 width=760>
		          <TBODY>
			    <TR>
			<TD align=middle bgColor=#ffff99>
		    	  <A href=/city.php?id=",$_GET['id'],">Назад в замок</A>
		          <A href=/world.php?id=",$_GET['id'],">Карта мира</A>
		          <A href=/logout.php>Выход из игры</A> 
		          </TD></TR></TBODY></TABLE>";

		$query1 = "SELECT * FROM `messages` WHERE `sender_id`='{$_GET['id']}' OR `recv_id`='{$_GET['id']}' ORDER BY `date`";
		$result1 = mysqli_query($link, $query1);
		if (!$result1) {
			die('Ошибка запроса: '.mysqli_error());
		}

		echo "		
		    <TABLE align=center border=1 cellPadding=0 cellSpacing=0 width=760>
		      <TBODY>
		      <TR align=middle bgColor=#dddd00 height=40><td>Дата</td><td>Отправитель</td><td>Получатель</td><td>Сообщение</td></tr>";
		for($i = 0; $i < mysqli_num_rows($result1); $i++) {
			$row1 = mysqli_fetch_assoc($result1);
//			if($row1['sender_id'] == $_GET['id']) {         // где является отправителем
//				echo "<tr><td bgcolor=#eeff99>",$row1['date'],"</td><td>",$row1['recv_name'],"</td><td width=300 bgcolor=>",$row1['message'],"</td></tr>";
//			}
//			else {                                          // где является получателем
				if($row1['sender_id'] == 0)		// сообщения от системы
					echo "<tr><td bgcolor=#ccdd00>",$row1['date'],"</td><td>Decline","</td><td>",$row1['recv_name'],"</td><td width=300 bgcolor=>",$row1['message'],"</TD></TR>\n";
				else
					echo "<tr><td bgcolor=#88ee88>",$row1['date'],"</td><td>",$row1['sender_name'],"</td><td>",$row1['recv_name'],"</td><td width=300 bgcolor=>",$row1['message'],"</TD></TR>\n";
//			}
		}
		echo "
		    </TBODY></TABLE>";
		// новое сообщение
		echo "
		    <TABLE align=center border=1 cellPadding=0 cellSpacing=0 width=760>	
	    	    <TR align=middle bgColor=#ffff99>
        	    <FORM action=messages.php method=get>
        	    <INPUT type=hidden name=id value=",$_GET['id'],">
		    <TD width=650 align=left valign=top>Получатель:
		    <INPUT name=recv_name size=92 value=",$_GET['recv_name'],">
            	    <TEXTAREA name=message type=text cols=80 rows=10></TEXTAREA></TD>
        	    <TD><INPUT name=sendmessage type=submit value=Отправить></TD>
                    </FORM></TR>
		    </TBODY></TABLE>    
		    </TD></TR></TBODY></TABLE>
		    </BODY></HTML>";
		mysqli_close($link);
	}
	else {
		echo "<script type=\"text/javascript\">";
		echo "alert('Замок не выбран.');";
		echo "document.location.href='/login.php';";
		echo "</script>";
		exit;
	};
?>
