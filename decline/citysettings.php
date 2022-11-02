<?php
/*
 * tax.php
 * выдает рабочее поле для установки оброка и налога
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
<HEAD><TITLE>DECLINE - Настройки замка</TITLE><LINK 
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

		if(isset($_GET['hour_turn'])) {		// смена перехода хода
	   	if(intval((time() - strtotime($row['hour_change']))/3600) < 24) {	// запретить менять переход хода чаще чем раз в сутки
				echo "<script type=\"text/javascript\">";
				echo "alert('Время перехода хода можно менять не чаще чем раз в сутки.');";
				echo "</script>";	    
	   	}
	   	else {
				$row['hour_turn'] = $_GET['hour_turn'];
	      	$query1 = "UPDATE `castles` SET `hour_turn`={$_GET['hour_turn']} , `hour_change`=now() WHERE `id`={$row['id']}";
    			$result1 = mysqli_query($link, $query1);
 				if (!$result1) {
					die('Ошибка запроса: '.mysqli_error());
				}  
	    	}
		}
		if(isset($_GET['tax'])) {		// установка таксы своего замка
	   	$row['tax'] = $_GET['tax'];
	   	if($row['tax'] < 0)
				$row['tax'] = 0;
	   	if($row['tax'] > 100)
				$row['tax'] = 100;
    	   $query1 = "UPDATE `castles` SET `tax`=".$row['tax']." WHERE `id`=".$row['id'];
    	   $result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}
		}	
		if(isset($_GET['protector_tax']) && isset($_GET['settax_id'])) {	// смена размеры оброка оброчнику
    	   $query1 = "SELECT * FROM `castles` WHERE `id`={$_GET['settax_id']}";     // колличество всех юнитов
	   	$result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}  

	    	if(mysqli_num_rows($result1) == 0) {
				echo "<script type=\"text/javascript\">";
				echo "alert('Этот замок не найден.');";
				echo "</script>";
	   	}
	    	else {	        
         	$row1 = mysqli_fetch_assoc($result1);
    	      if($row1['protector_id'] != $row['id']) {
	    	   	echo "<script type=\"text/javascript\">";
	    	   	echo "alert('Этот замок Вам оброк не платит.');";
		    		echo "</script>";
				}
				else {	
    		   	$protector_tax = $_GET['protector_tax'];
		    		if($protector_tax < 0)
						$protector_tax = 0;
		   		if($protector_tax > 10)
						$protector_tax = 10;
	    	   	$query1 = "UPDATE `castles` SET `protector_tax`='{$protector_tax}' WHERE `id`='{$_GET['settax_id']}'";
    		   	$result1 = mysqli_query($link, $query1);
					if (!$result1) {
						die('Ошибка запроса: '.mysqli_error());
					}
				}    
	    	}	
		}
		if(isset($_GET['refusetax'])) {		// отказ от оброка	
      	$query1 = "INSERT INTO `messages` VALUES(null,now(),0,'Decline',{$row['protector_id']},'{$row['protector_name']}','Замок {$row['castle_name']} отказался платить оброк.')";
    	   $result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}
	    	$row['protector_id'] = 0;
	    	$row['protector_name'] = "";
	    	$row['protector_tax'] = 0;
    	   $query1 = "UPDATE `castles` SET `protector_id`=0,`protector_name`=NULL,`protector_tax`=0 WHERE `id`=".$row['id'];
    	   $result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}
		}
	
		if(isset($_GET['applytax']) && isset($_GET['protector_id'])) {		// начать платить оброк
    	   $query1 = "SELECT * FROM `castles` WHERE `id`={$_GET['protector_id']}"; // проверяем замок    
	    	$result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}

	    	if(mysqli_num_rows($result1) != 0) {
            $row1 = mysqli_fetch_assoc($result1);
				$row['protector_id'] = $row1['id'];		// параметры выставляются чтобы дальше по коду отключить или включать поля ввода
				$row['protector_name'] = $row1['castle_name'];
				$row['protector_tax'] = 10;
    			$query2 = "UPDATE `castles` SET `protector_id`={$row1['id']},`protector_name`='{$row1['castle_name']}',`protector_tax`=10 WHERE `id`=".$row['id'];
    	      $result2 = mysqli_query($link, $query2);
				if (!$result2) {
					die('Ошибка запроса: '.mysqli_error());
				}
            $query3 = "INSERT INTO `messages` VALUES(null,now(),0,'Decline',{$row1['id']},'{$row1['castle_name']}','Замок {$row['castle_name']} начало платить оброк.')";
				$result3 = mysqli_query($link, $query3);
				if (!$result3) {
					die('Ошибка запроса: '.mysqli_error());
				}
    	 	}
	    	else {
				echo "<script type=\"text/javascript\">";
				echo "alert('Этот замок не найден.');";
				echo "</script>";
	   	}
      }
	
		echo "
	<TABLE align=center width=760>
	<TBODY>
	    <TR>
	    <TD align=middle><STRONG><FONT size=4>Установка налога</FONT></STRONG></TD>
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
	    </TD></TR></TBODY></TABLE>
	    <TABLE align=center border=1 cellPadding=0 cellSpacing=0 width=760>

	    <TR align=center bgColor=#dddd00 height=40><TD>Время перехода хода (час)</TD>
	    <FORM action=citysettings.php method=get>
	    <TD align=center width=200>
    	    <INPUT type=hidden name=id value=",$row['id'],">
	    <INPUT name=hour_turn style=\"HEIGHT: 25px; WIDTH: 50px\" type=number min=0 max=23 value=",$row['hour_turn'],">
	    <INPUT name=change_transition";
	    if(intval((time() - strtotime($row['hour_change']))/3600) < 24) {	// запретить менять переход хода чаще чем раз в сутки
	    	echo " disabled";    	    
	    }
	    echo " type=submit value=Изменить!>
	    </TD></FORM></TR>

	    <TR align=center bgColor=#dddd00 height=40><TD>Текущий налог (%)</TD>
	    <FORM action=citysettings.php method=get>
	    <TD align=center width=200>
    	    <INPUT type=hidden name=id value=",$row['id'],">
	    <INPUT name=tax style=\"HEIGHT: 25px; WIDTH: 50px\" type=number min=0 max=100 value=",$row['tax'],">
	    <INPUT name=changetax type=submit value=Изменить!>
	    </TD></FORM></TR>";
	    
    	$query1 = "SELECT * FROM `castles` WHERE `protector_id`={$row['id']}";     // все замки которые платят оброк
	   $result1 = mysqli_query($link, $query1);
		if (!$result1) {
			die('Ошибка запроса: '.mysqli_error());
		}
	   for($i = 0; $i < mysqli_num_rows($result1); $i++) {	// установка налога
         $row1 = mysqli_fetch_assoc($result1);                                          
	      echo "
		<TR align=center bgColor=#dddd00 height=40><TD>Вам платит дань замок ",$row1['castle_name']," установить налог (%)</TD>
    		<FORM action=citysettings.php method=get>
		<TD align=center width=200>
    		<INPUT type=hidden name=id value=",$row['id'],">
		<INPUT type=hidden name=settax_id value=",$row1['id'],">    		
		<INPUT name=protector_tax style=\"HEIGHT: 25px; WIDTH: 50px\" type=number min=0 max=10 value=",$row1['protector_tax'],">
		<INPUT name=changetax type=submit value=Изменить!>
		</TD></FORM></TR>";
	   }
	   if($row['protector_id'] != 0) {		// кому платит оброк
			echo "	
		<TR align=center bgColor=#dddd00 height=40><TD>Вы платите дань замку ",$row['protector_name']," ",$row['protector_tax'],"% от имеющегося в замке золота</TD>
    		<FORM action=citysettings.php method=get>
		<TD align=center width=200>
    		<INPUT type=hidden name=id value=",$row['id'],">
		<INPUT name=refusetax type=submit value=Отказаться!>
		</TD></FORM></TR>";
	   }
	   if($row['protector_id'] == 0) {	/// если вы уже платите оброк надо сначала отказаться
			// самому встать под оброк, рядом с замком должен стоять юнит
    		$query1 = "SELECT * FROM `units` WHERE `x`>='".($row['x']-1)."' AND `x`<='".($row['x']+1)."' AND `y`>='".($row['y']-1)."' AND `y`<='".($row['y']+1)."' AND `castle_id`!=".$row['id']." AND `castle_id`!=".$row['protector_id'];
        	$result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}
                                                    
			if(mysqli_num_rows($result1) > 0) {	// рядом с замком есть юниты
    		    echo "
		    <TR align=center bgColor=#dddd00 height=40>	    
		    <FORM action=citysettings.php method=get>
		    <INPUT type=hidden name=id value=",$row['id'],">	    
		    <TD>Начать платить оброк замку    		    
		    <SELECT name=protector_id width=100>";
		   	for($i = 0; $i < mysqli_num_rows($result1); $i++) {
	         	$row1 = mysqli_fetch_assoc($result1);                                          
					echo "<OPTION value=",$row1['castle_id'],">",$row1['castle_name'];
		    	}		    
		    	echo "
		    <OPTION SELECTED></SELECT>
		    <TD><INPUT name=applytax type=submit value=Платить!>
		    </TD></FORM></TR>";
	      }
	   }
	   echo "
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
