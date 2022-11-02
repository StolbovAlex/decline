<?php
/*
 * world.php
 * выдает рабочее поле для замка
 */
    session_start();
    if (!isset($_SESSION['UID'])) {
		echo "<script type=\"text/javascript\">";
		echo "alert('Необходимо авторизоваться.');";
		echo "document.location.href='/index.php';";
		echo "</script>";
		exit;
    }
?>
<head><TITLE>DECLINE - Замок</TITLE>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Замок</title>
</head>
<body topmargin=5 leftmargin=2 bgcolor="#dfdfb0"><TABLE width="760" align="center">
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
		$timelife = intval((time() - strtotime($row['date_creation'])) / 86400); // время жизни в днях 	
		echo "
	    <table width=760 align=center><tr><td align=center><strong><font size=4>Замок :<br>",$row['castle_name'],"<br>x",$row['x']," y",$row['y'],"</font></strong></td>
	    <td width=468></td><td align=center><br>Время на Сервере<br><b><div id=clock></div>
	    <script language=javascript src=clock.js></script>
	    <script language=javascript><!--;
	    var hours = ",date("H"),";
	    var minutes = ",date("i"),";
	    var seconds = ",date("s"),";
	    if(typeof allloaded!='undefined') startclock();
	    else showclock = 1; //--></script></b></td></tr></table><br>
	    <table align=center width=760 border=0><tr><td align=center valign=top width=150>
	    <table align=left bgcolor=#ffff99 width=150><TR><TD bgcolor=#dddd00 align=center><strong>Меню</strong>    	        
	    <tr><td align=center>
	    <a href=world.php?id=",$_GET['id'],">Карта</a><br>
	    <a href=buy.php?id=",$_GET['id'],">Наем войска</a><br>
	    <a href=dembel.php?id=",$_GET['id'],">Демобилизация</a><br>
	    <a href=peace.php?id=",$_GET['id'],">Мирные соглашения</a><br>
	    <a href=iclan.php?id=",$_GET['id'],">Клан</a>
	    </td></tr>
	    <tr><td bgcolor=#dddd00 align=center><strong>Настройка</strong></td></tr>
	    <tr><td align=center><a href=citysettings.php?id=",$_GET['id'],">Настройки замка</a>
	    <tr><td bgcolor=#dddd00 align=center><STRONG>Коммуникация</STRONG></td></tr>
	    <tr><td align=center>
	    <a href=messages.php?id=",$_GET['id'],">Cообщения</a><br>
	    </td></tr>
	    <tr><td bgcolor=#dddd00 align=center><strong>Выход из игры</strong></td></tr>
	    <tr><td align=center><br>
	    <a href=/logout.php>[ Выход ]</a><br>
	    </td></tr>
	    </table></td>
	    <td valign=top width=300>
	    <table width=300 bgcolor=#ffff99>
	    <tr><td bgcolor=#dddd00 align=center><strong>Сообщения за три дня</strong></td></tr>\n";

		$query1 = "SELECT * FROM `messages` WHERE `date` >= NOW() - INTERVAL 3 DAY AND (`sender_id`='{$_GET['id']}' OR `recv_id`='{$_GET['id']}')";
		$result1 = mysqli_query($link, $query1);
		if (!$result1) {
			die('Ошибка запроса: '.mysqli_error());
		}		
		for($i = 0; $i < mysqli_num_rows($result1); $i++) {
			$row1 = mysqli_fetch_assoc($result1);
			if($row1['sender_id'] == $_GET['id']) { 	// отправитель
				echo "<tr><td bgcolor=#eeff99>",$row1['date'],"&nbsp;Для ",$row1['recv_name'],"</td></tr><tr><td width=300>",$row1['message'],"</TD></TR>";
			}
			else {						// получатель
				if($row1['sender_id'] == 0)
					echo "<tr><td bgcolor=#ccdd00>",$row1['date'],"&nbsp;Внимание","</td></tr><tr><td width=300 bgcolor=>",$row1['message'],"</TD></TR>";
				else
					echo "<tr><td bgcolor=#88ee88>",$row1['date'],"&nbsp;От ",$row1['sender_name'],"</td></tr><tr><td width=300 bgcolor=>",$row1['message'],"</TD></TR>";
			}
		}
		// вычисление параметров	
    	$query2 = "SELECT * FROM `units` WHERE `castle_id`={$row['id']}";	// колличество всех юнитов
		$result2 = mysqli_query($link, $query2);
		if (!$result2) {
			die('Ошибка запроса: '.mysqli_error());
		}		
		$rent = 0;
		$allunits = mysqli_num_rows($result2);		// так же использыется для расчета уровня жизни
		$cityunits = 0;
		for($i = 0; $i < $allunits; $i++) {
	   	 $row2 = mysqli_fetch_assoc($result2);	
	   	 $rent = $rent + $row2['rent'];	    
		}
		$timelife = intval((time() - strtotime($row['date_creation'])) / 86400); // время жизни в днях     
	
   	$query1 = "SELECT * FROM `castles` WHERE `protector_id`={$row['id']}";     // 
	   $result1 = mysqli_query($link, $query1);
		if (!$result1) {
			die('Ошибка запроса: '.mysqli_error());
		}
		$sumtax = 0.0;			// суммарный доход с оброчников.
	   for($i = 0; $i < mysqli_num_rows($result1); $i++) {	// установка налога
         $row1 = mysqli_fetch_assoc($result1);                           
         $curcitytax = round($row1['gold']*$row1['protector_tax']/100,2);
			$sumtax += $curcitytax;
		}
		$volume = (1.00 - $row['population']/30000)*100;								// объем произведеной продукции - с ростом начеления растет колличество бездельников, производительность падает (50 - 100)
		$consumption = (1.00 - ($row['population']+$allunits * 10)/30000)*50;	// объем потребления - с ростом начеления растет колличество бездельников, производительность падает (49 - 99)	
		$income =  round($volume*$row['tax']/100/8,2);									// приход золота с начеления.
		$food = round(($volume * (1-$row['tax']/100) - $consumption)/8,2);		// оставшееся кол-во продуктов оставшееся от потребления начелением и армией за три часа
		$pay = round($row['gold']*$row['protector_tax']/100,2);						// сколько платим оброка 
		$growth = floor(($row['food']+$food)/2);											// прирост населения, на восспроизводство требоется удвоенное кол-во пищи
		$output = round($income - $pay - $rent + $sumtax,2);							// итого
		
		echo "
	    </table></td><td width=290 valign=top>
	    <table align=center bgcolor=#ffff99 width=290>
	    <tr><td align=center bgcolor=#dddd00 colspan=2>Владелец : <b>",$row['castle_name'],"</b></td></tr>
	    <tr><td align=center bgcolor=#FFFFCC colspan=2>Возраст замка (дней) : <b>",$timelife,"</b></td></tr>
	    <tr><td align=center colspan=2>Убито : <b>",$row['destroyed'],"</b></TD></TR>    
            <tr><td align=center bgcolor=#FFFFCC colspan=2>Возраст замка: <b>",$timelife,"</b></td></tr>
	    <tr><td height=10 bgcolor=#dfdfb0 colspan=2></TD></TR>
	    <tr><td colSpan=2 bgcolor=#dddd00 align=center><STRONG>На сегодняшний день у Вас в замке</STRONG></TD>
	    <tr align=right bgcolor=#FFFFCC><td width=210>Золота</td><td width=80>",$row['gold'],"</td></tr>
	    <tr align=right bgcolor=#FFFFCC><td>Население (человек)</td><td>",$row['population'],"</td></tr>
	    <tr align=right bgcolor=#FFFFCC><td>Пища (т.)</TD><TD>",$row['food'],"</TD></TR>
	    <tr align=right bgcolor=#FFFFCC><td>Налог (%)</TD><TD>",$row['tax'],"</TD></TR>
	    <tr bgcolor=#dddd00 align=center><td colspan=2><b>Баланс за последние три часа</b></td></tr>
	    <tr align=right bgcolor=#FFFFCC><td>Приход пищи на склад :</td><td><font color=Green>",$food,"</font></td></tr>	    
	    <tr align=right bgcolor=#FFFFCC><td>Прирост населения :</td><td><font color=Green>",$growth,"</font></td></tr>	    
	    <tr align=right bgcolor=#FFFFCC><td>Доход от населения :</td><td><font color=Green>",$income,"</font></td></tr>
	    <tr bgcolor=#dddd00 align=center><td colspan=2><b>Баланс за сутки при текущем налоге</b></td></tr>
	    <tr align=right bgcolor=#FFFFCC><td>Доход от населения :</td><td><font color=Green>",$income * 8,"</font></td></tr>	    
	    <TR align=right bgcolor=#FFFFCC><TD>Оброк :</TD><TD><font color=Red>",$pay,"</font></TD></TR>	    
	    <TR align=right bgcolor=#FFFFCC><TD>Содержание войска :</TD><TD><font color=Red>",$rent,"</font></TD></TR>	    
	    <TR align=right bgcolor=#FFFFCC><TD>Итого :</TD><TD><FONT color=Green>",round($income*8-$pay-$rent+$sumtax,2),"</FONT></TD></TR>	    
	    <TR><TD bgcolor=#dddd00 colspan=4 align=center><STRONG>Войска</STRONG></TD></TR>
	    <TR bgcolor=#FFFFCC align=right>
	     <TD>Содержание :</TD>
    	     <TD><font color=Red>",$rent,"</font></TD></TR>    	     
	    <TR bgcolor=#FFFFCC align=right>
	     <TD>Количество :</TD>
	     <TD><font color=Red>",$allunits,"</font></TD></TR>
	     
	    <TR bgcolor=#DDDD00 align=center><td colspan=2><b>Войска в замке</td></tr>";

    	$query2 = "SELECT * FROM `units` WHERE `castle_id`={$row['id']} AND `x`={$row['x']} AND `y`={$row['y']}";	// колличество всех юнитов
		$result2 = mysqli_query($link, $query2);
		if (!$result2) {
			die('Ошибка запроса: '.mysqli_error());
		}		

		echo "<TABLE cellspacing=0 cellpadding=1 border=0 width=290 bgcolor=#FFFFCC><TR height=40>";
		for($i = 0; $i < mysqli_num_rows($result2); $i++) {
	   	$row2 = mysqli_fetch_assoc($result2);		
    	   echo "<TD width=40><a href=/world.php?id=".$row['id']."&id_sold=".$row2['id']."><img border=1 height=40 width=40 src=/pic/s".$row2['type'].".gif></a></TD>";                  
    	   echo "<TD width=2 valign=bottom><img src=";
    	   if($row2['health'] > 49)
    			echo "/pic/green.gif";
    	   else
    			if($row2['health'] > 24)
    		   	echo "/pic/yellow.gif";
    			else
    		   	echo "/pic/red.gif";
    	   echo " width=2 height=",$row2['health']*40/100,"></TD>";
    	   echo "<TD width=12 align=left valign=bottom><small>",$row2['turns'],"</small></TD>";
    	    
    	   if(($i+1)%5 == 0)
    			echo "</TR><TR height=40>";
		}	    	
		if(($i+1)%5 != 0)
    	    echo "<TD colspan=",3*(6-($i+1)%5),">&nbsp;</TD/";
		echo "    
	    </TR></TABLE>
	    </TABLE>
	    </TD></TR></TABLE>
	    </body></html>\n";
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


