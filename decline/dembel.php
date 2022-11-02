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
	}
?>	
<HEAD><TITLE>DECLINE - Наем войска</TITLE><LINK 
<META content=no-cache http-equiv=pragma>
<META content="text/html; charset=utf-8" http-equiv=Content-Type>
<BODY bgColor=#dfdfb0>
<?php
	$army = array(
		array('type'=>1, 'name'=>'Пикеносец',     'attack'=>1, 'defense'=>1, 'turn'=> 3, 'cost'=>5, 'rent' => 0.5),
		array('type'=>2, 'name'=>'Охраник',       'attack'=>1, 'defense'=>2, 'turn'=> 3, 'cost'=>10, 'rent' => 0.8),
		array('type'=>3, 'name'=>'Лучник',        'attack'=>2, 'defense'=>1, 'turn'=> 3, 'cost'=>10, 'rent' => 0.8),
		array('type'=>4, 'name'=>'Разведчик',     'attack'=>2, 'defense'=>1, 'turn'=> 6, 'cost'=>30, 'rent' => 2.0),
		array('type'=>8, 'name'=>'Балиста',       'attack'=>4, 'defense'=>1, 'turn'=> 3, 'cost'=>40, 'rent' => 1.5),
		array('type'=>6, 'name'=>'Тяжелый рыцарь','attack'=>3, 'defense'=>5, 'turn'=> 2, 'cost'=>60, 'rent' => 3.5),
		array('type'=>9, 'name'=>'Маг',           'attack'=>2, 'defense'=>2, 'turn'=> 4, 'cost'=>80, 'rent' => 6.0),
		array('type'=>5, 'name'=>'Рыцарь',        'attack'=>4, 'defense'=>3, 'turn'=> 5, 'cost'=>80, 'rent' => 5.0),
		array('type'=>7, 'name'=>'Паладин',       'attack'=>6, 'defense'=>5, 'turn'=> 4, 'cost'=>120, 'rent' => 8.0));

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

	    	if(isset($_GET['dmb']) && isset($_GET['dmb_id'])) {
			$query1 = "SELECT * FROM `decline`.`units` WHERE `castle_id`='{$_GET['id']}' AND `id`='{$_GET['dmb_id']}'";
			$result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}

			if(mysqli_num_rows($result1) == 0) {
			    echo "<script type=\"text/javascript\">";
			    echo "alert('Юнит не обнаружен.');";
			    echo "</script>";
			    exit;
			}
			else {
		    		$query1 = "delete from `decline`.`units` WHERE `id`='{$_GET['dmb_id']}'";
		    		$result1 = mysqli_query($link, $query1);
				if (!$result1) {
					die('Ошибка запроса: '.mysqli_error());
				}
				
		    		$row['population'] = $row['population'] + 10;
		    		$query1 = "UPDATE `decline`.`castles` SET `population`='".$row['population']."' WHERE `id`='".$row['id']."'";
		    		$result1 = mysqli_query($link, $query1);
				if (!$result1) {
					die('Ошибка запроса: '.mysqli_error());
				}
			}
		}
	    	$query1 = "SELECT * FROM `decline`.`units` WHERE `castle_id`='{$_GET['id']}'";
	    	$result1 = mysqli_query($link, $query1);
		if (!$result1) {
			die('Ошибка запроса: '.mysqli_error());
		}

	    	echo "
	        <TABLE align=center width=760>
	        <TBODY>
		<TR>
		<TD align=middle><STRONG><FONT size=4>Демобилизация войск</FONT></STRONG></TD>
		<TD width=468></TD>
		<TD align=middle><BR>Время на Сервере<BR><B>
		<DIV id=clock></DIV>
		<SCRIPT language=javascript src=/clock.js></SCRIPT>
		<SCRIPT language=javascript><!--;
    		var hours = ",date("H"),";
		var minutes = ",date("i"),";
		var seconds = ",date("s"),";
		startclock();
		//--></SCRIPT>
		</B></TD></TR></TBODY></TABLE><BR>
		<CENTER><STRONG>У вас ",$row['population']," населения</STRONG></CENTER>
		<TABLE align=center border=1 width=760>
		<TBODY>
		<TR>
		<TD align=middle bgColor=#ffff99>
    		<A href=/city.php?id=",$_GET['id'],">Назад в замок</A>&nbsp;&nbsp;
		<A href=/world.php?id=",$_GET['id'],">Карта мира</A>&nbsp;&nbsp;
		    <A href=/logout.php>&nbsp;Выход из игры</A> 
		</TD></TR></TBODY></TABLE>
		<TABLE align=center border=1 cellPadding=0 cellSpacing=0 width=760>
		<TBODY>
		<TR align=middle bgColor=#dddd00 height=40>
		<TD colSpan=2>Название</TD>
		<TD>Атака</TD>
		<TD>Защита</TD>
		<TD>Кол-во<BR>ходов</TD>
		<TD>Цена</TD>
		<TD>Цена<BR>содержания</TD>
		<TD>X</TD>
		<TD>Y</TD>
		<TD>Распустить?</TD></TR>";

		for($i = 0; $i < mysqli_num_rows($result1); $i++) {
		    	$row1 = mysqli_fetch_assoc($result1);
		    	echo "<TR align=middle bgColor=#ffff99 height=42>\n";
		    	echo "<TD background=/pic/s",$row1['type'],".gif width=40>&nbsp;</TD>\n";
		    	for($j = 0; $j < count($army); $j++) {
		    		if($army[$j]['type'] == $row1['type']) {
			    		echo "<TD>",$army[$j]['name'],"</TD>\n";
			    		break;
				}
		 	}
		    	echo "
			<TD>",$row1['attack'],"</TD>
			<TD>",$row1['defense'],"</TD>
			<TD>",$row1['turns'],"</TD>
			<TD>",$row1['cost'],"</TD>
			<TD>",$row1['rent'],"</TD>
			<TD>",$row1['x'],"</TD>
			<TD>",$row1['y'],"</TD>
			<FORM action=dembel.php method=get>
			<TD vAlign=center>
			<INPUT type=hidden name=id value=",$_GET['id'],">
			<INPUT type=hidden name=dmb_id value=",$row1['id'],">
			<INPUT name=dmb type=submit value=Да!>
			</TD></FORM></TR>";
		}	
		echo "<TR>
<TD colSpan=10>Из всех войск, только маг имеет дополнительные возможности. 
Маги умеют лечить, и могут телепортировать между собой войска. Но будьте 
внимательны! При телепортации маг берет случайное Ваше войско стоящее 
рядом с ним и телепортирует его на случайную клетку соседствующую с 
выбранным вами мага! Телепортируемое войско теряет все свои ходы.<BR>Все 
войска (кроме магов) могут стать Ангелом, набрав более 1499 опыта. Ангел 
имеет следующие характеристики : Атака : 10, Защита : 10, Ходов : 10. 
Опыт при перевоплощении полностью теряется.</TD></TR></TBODY></TABLE>
</BODY></HTML>";
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
