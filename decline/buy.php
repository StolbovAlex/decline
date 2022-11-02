<?php
/*
 * bay.php
 * выдает рабочее поле для покупки юнитов
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
<HEAD><TITLE>DECLINE - Наем войска</TITLE>
<META content=no-cache http-equiv=pragma>
<META content="text/html; charset=utf-8" http-equiv=Content-Type>
<BODY bgColor=#dfdfb0>
<?php
   $army = array(
   array('name'=>'Тень',		'attack'=>0, 'defense'=>0, 'turn'=> 0, 'cost'=>0, 'rent' => 0),		//type 0
   array('name'=>'Пикеносец',		'attack'=>1, 'defense'=>1, 'turn'=> 3, 'cost'=>5, 'rent' => 0.5),	//type 1
   array('name'=>'Охраник',		'attack'=>1, 'defense'=>2, 'turn'=> 3, 'cost'=>10, 'rent' => 0.8),	//type 2
   array('name'=>'Лучник',		'attack'=>2, 'defense'=>1, 'turn'=> 3, 'cost'=>10, 'rent' => 0.8),	//type 3
   array('name'=>'Разведчик',		'attack'=>2, 'defense'=>1, 'turn'=> 6, 'cost'=>30, 'rent' => 2.0),	//type 4
   array('name'=>'Рыцарь',		'attack'=>4, 'defense'=>3, 'turn'=> 5, 'cost'=>80, 'rent' => 5.0),	//type 5
   array('name'=>'Тяжелый рыцарь',	'attack'=>3, 'defense'=>5, 'turn'=> 2, 'cost'=>60, 'rent' => 3.5),	//type 6
   array('name'=>'Паладин',		'attack'=>6, 'defense'=>5, 'turn'=> 4, 'cost'=>120, 'rent' => 8.0),	//type 7
   array('name'=>'Балиста',		'attack'=>4, 'defense'=>1, 'turn'=> 3, 'cost'=>40, 'rent' => 1.5),	//type 8
   array('name'=>'Маг',		'attack'=>2, 'defense'=>2, 'turn'=> 4, 'cost'=>80, 'rent' => 6.0));	//type 9

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
		if(isset($_GET['buy']) && isset($_GET['type']) && isset($_GET['count'])) {
			if(($_GET['type'] > 0) && ($_GET['type'] < 10)) {	// юниты пока только с 1 по 9
				$cost = $army[$_GET['type']]['cost'] * $_GET['count'];
				if(($row['gold'] >= $cost) && ($row['population'] > 10)) {
					$row['gold'] = $row['gold'] - $cost;
					$row['population'] = $row['population'] - 10;
					$castle_id = $row['id'];
					$castle_name = $row['castle_name'];
					$type = $_GET['type'];
					$cost = $army[$_GET['type']]['cost'];
					$rent = $army[$_GET['type']]['rent'];
					$turn = $army[$_GET['type']]['turn'];
					$attack = $army[$_GET['type']]['attack'];
					$defense = $army[$_GET['type']]['defense'];
					$x = $row['x'];
					$y = $row['y'];
					for($j = 0; $j < $_GET['count']; $j++) {
						$query1 = "insert into `units` values(null,".$castle_id.",'".$castle_name."',".$type.",0,".$cost.",".$rent.",100,".$turn.",".$attack.",".$defense.",".$x.",".$y.")";
						$result1 = mysqli_query($link, $query1);
						if (!$result1) {
							die('Ошибка запроса: '.mysqli_error());
						}
					}
					$query1 = "UPDATE `castles` SET `gold`='".$row['gold']."',`population`='".$row['population']."' WHERE `id`='".$row['id']."'";
					$result1 = mysqli_query($link, $query1);
					if (!$result1) {
							die('Ошибка запроса: '.mysqli_error());
					}
				}
				else {
					echo "<script type=\"text/javascript\">";
					echo "alert('Не достаточно рессурсов.');";
					echo "</script>";
				}
			}
			else {
				echo "<script type=\"text/javascript\">";
				echo "alert('Не известный тип юнита.');";
				echo "</script>";
			}
		}
		
		echo "
	    <TABLE align=center width=760>
	    <TBODY>
	    <TR>
	    <TD align=middle><STRONG><FONT size=4>Наем войска</FONT></STRONG></TD>
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
    	    <A href=/dembel.php?id=",$row['id'],">Демобилизация</A>&nbsp;&nbsp; 
    	    <A href=/world.php?id=",$row['id'],">Карта мира</A>&nbsp;&nbsp;
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
	    <TD>Вооружить?</TD></TR>";

		for($i = 1; $i < count($army); $i++) {		// 0 юнита не существует
	   	echo "
	    <TR align=middle bgColor=#ffff99 height=42>
	    <TD background=/pic/s",$i,".gif width=40>&nbsp;</TD>
	    <TD>",$army[$i]['name'],"</TD>
	    <TD>",$army[$i]['attack'],"</TD>
	    <TD>",$army[$i]['defense'],"</TD>
	    <TD>",$army[$i]['turn'],"</TD>
	    <TD>",$army[$i]['cost'],"</TD>
	    <TD>",$army[$i]['rent'],"</TD>
	    <FORM action=buy.php method=get>
	    <TD vAlign=center>
	    <INPUT type=hidden name=id value=",$_GET['id'],">
	    <INPUT type=hidden name=type value=",$i,">
	    <INPUT name=count style=\"HEIGHT: 25px; WIDTH: 30px\" type=number min=1 max=9 value=1>
	    <INPUT name=buy type=submit value=Нанять>
	    </TD></FORM></TR>\n";
		}
      echo "
    	    <TR>
	    <TD colSpan=9>Из всех войск, только маг имеет дополнительные возможности. 
	    Маги умеют лечить, и могут телепортировать между собой войска. Но будьте 
	    внимательны! При телепортации маг берет случайное Ваше войско стоящее 
    	    рядом с ним и телепортирует его на случайную клетку соседствующую с 
    	    выбранным вами мага! Телепортируемое войско теряет все свои ходы.<BR>Все 
    	    войска (кроме магов) могут стать Ангелом, набрав более 1499 опыта. Ангел 
    	    имеет следующие характеристики :<BR>Атака : 10, Защита : 10, Ходов : 10. 
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
