<?php
/*
 * parser.php
 * Обрабатывает часовые изменения и переходы хода у замков.
 * Запускаяется каждый час в 0 минут 0 секунд
 */
    include 'rules.php';

	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
//	$link = mysqli_connect('mysql', 'decline', 'dfdbkjy5', 'decline');
	$link = mysqli_connect('172.20.0.4', 'decline', 'dfdbkjy5', 'decline');
	if (mysqli_connect_errno()) {
		die('Failed to connect to MySQL: '.mysqli_connect_error());
	}
   
    $query = "LOCK TABLES `castles` WRITE,`units` WRITE";
    $result = mysqli_query($link, $query);
    if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}
                                                
    $query = "SELECT * FROM `castles`";
	echo time(),"-",$query,"\n";
    $result = mysqli_query($link, $query);
    if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}

   $hour = date("H");
   for($i=0; $i < mysqli_num_rows($result); $i++) {	// парсим по замкам
		$row = mysqli_fetch_assoc($result);	   
		//сначала по юнитам чтобы знать цену аренды 	
		$query1 = "SELECT * FROM `units` WHERE `castle_id`='{$row['id']}'";
		echo time(),"-",$query1,"\n";
	   $result1 = mysqli_query($link, $query1);
    	if (!$result1) {
			die('Ошибка запроса: '.mysqli_error());
		}
		$rent = 0;
      $allunits = mysqli_num_rows($result1);
		for($j=0; $j < $allunits; $j++) {				// парсим по юнитам
	    	$row1 = mysqli_fetch_assoc($result1);
	    	$rent = $rent + $row1['rent'];
         if(($row['hour_turn'] == $hour) && (intval((time() - strtotime($row['hour_change']))/3600) < 24)) {	// если настал час перехода хода и не было смена хода за последние 24 часа
//    		if(1) {				// для теста при каждом запуске здоровье обновляется каждый час
            if(($row1['experience'] >= 1500) && ($row1['type'] != 8) && ($row1['type'] != 30)) { // если экспа 1500 все юниты кроме мага и ангела становятся ангелами.
         	   $query2 = "UPDATE `units` SET `type`='30', `experience`='0', `cost`='0', `rent`='0', `health`='100', `turns`='10', `attack`='10', `defense`='10' WHERE `id`='{$row1['id']}'";
					echo time(),"-",$query2,"\n";   
	    			$result2 = mysqli_query($link, $query2);
    				if (!$result2) {
						die('Ошибка запроса: '.mysqli_error());
					}
            }
            else {	// пересчет параметров в обучном режиме
	        		if(($row['x'] == $row1['x']) && ($row['x'] == $row1['x']))
		    			$row1['health'] = $row1['health'] + 25;		// если юнит в замке то восстанавливается 25% здоровья 
    	    		else 
	    				$row1['health'] = $row1['health'] + 10;		// если юнит не в замке то восстанавливается 10% здоровья
					if($row1['health'] > 100)
		    			$row1['health'] = 100;				// 100% максимум
    	    		// ходы обновляются на переходе хода
    	    		$level = get_level($row1['experience']);		// шаги, атака и защита восполняются с учетом опыта
					$row1['turns'] = $army[$row1['type']]['turns'] + $level;	// восстанавливаются полностью
					$row1['attack'] = ($row1['health'] * ($army[$row1['type']]['attack'] + $level)/100);       // пересчитываем атаку и защиту с учетом нового здоровья 
           		$row1['defense'] = ($row1['health'] * ($army[$row1['type']]['defense'] + $level)/100);
            	$query2 = "UPDATE `units` SET `health`='{$row1['health']}', `turns`='{$row1['turns']}', `attack`='{$row1['attack']}', `defense`='{$row1['defense']}' WHERE `id`='{$row1['id']}'";
					echo time(),"-",$query2,"\n";   
	    			$result2 = mysqli_query($link, $query2);
    				if (!$result2) {
						die('Ошибка запроса: '.mysqli_error());
					}
				}
	    	}                                                                                                                      
		}
		if(($hour % 3) == 0) {	// 0,3,6,9,12,15,18,21 - часы начисления дохода и снятия оброка и прироста населения.
//		if(($hour % 1) == 0) {	// каждый запуск скрипта начисления дохода и снятия оброка и прироста населения.
   			$query1 = "SELECT * FROM `castles` WHERE `protector_id`={$row['id']}";     // все замки которые платят налог
	   		$result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}
			$sumtax = 0.0;		// суммарный налог со всех оброчников
	 		for($j = 0; $j < mysqli_num_rows($result1); $j++) {	// сбор оброка
        		$row1 = mysqli_fetch_assoc($result1);              
                $curcitytax = round($row1['gold']*$row1['protector_tax']/100,2);
                $newcitygold = $row1['gold'] - $curcitytax/8;	// за три часа списывается только треть от суточной
            	$query2 = "UPDATE `castles` SET `gold`='{$newcitygold}' WHERE `id`={$row1['id']}";	// обновляем значение золота после уплаты оброка
				echo time(),"-",$query2,"\n";   
	    		$result2 = mysqli_query($link, $query2);
    			if (!$result2) {
					die('Ошибка запроса: '.mysqli_error());
				}
				$sumtax += $curcitytax;
			}
	    	// рассчитываемые параметры
	    	$volume = (1.00 - $row['population']/30000)*100;   // объем произведеной продукции - с ростом начеления растет колличество бездельников, производительность падает (50 - 100)
	    	$consumption = (1.00 - ($row['population'] + $allunits * 10)/30000)*50;// объем потребления - с ростом начеления растет колличество бездельников, производительность падает (49 - 99)
	    	$income =  round($volume*$row['tax']/100/8,2);   					// приход золота за три часа
	    	$food = round(($volume * (1-$row['tax']/100) - $consumption)/8,2);  // оставшееся кол-во продуктов оставшееся от потребления начелением и армией за три часа	
	    	$pay = round($row['gold']*$row['protector_tax']/100,2); 			// сколько платим оброка в сутки
	    	// пересчитываем пищу и население
	    	$row['food'] = $row['food']+$food;
	    	$popup = floor($row['food'] / 2);					// прирост населения, на восспроизводство требоется удвоенное кол-во пищи
	    	$row['population'] = $row['population'] + $popup;           	
	    	$row['food'] = $row['food'] - $popup * 2;				// оставляем только дробную часть наколения
	    	if($row['population'] > 15000) {					// передел начеления
			$row['food'] = $row['food'] - (15000 - $row['population']) * 2; // возвращаем пищу на склад, это позволяет ее накопить а потом 100% налогом получить деньги
			$row['population'] = 15000;		
			if($row['food'] > 100.00)
			    $row['food'] = 100.00;					// предел по пище
	    	}	
	    
	    	// пересчет золота		
	    	$pay=round($pay/8,2);	// суточный оброк и налог платим порциями каждые три часа
	    	$rent=round($rent/8,2);
	    	$sumtax=round($sumtax/8,2);
	    	$row['gold'] = $row['gold'] + $income - $pay - $rent + $sumtax;     // начисление калога каждые три часа, списание и начисление оброка и содержание юнитов 1/8 от суточной суммы
    		$query1 = "UPDATE `castles` SET `gold`='{$row['gold']}',`population`='{$row['population']}',`food`='{$row['food']}' WHERE `id`='{$row['id']}'";    	    
			echo time(),"-",$query1,"\n"; 
	    	$result1 = mysqli_query($link, $query1);
    		if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}
	    	// перечисляем налог	    
    		$query1 = "UPDATE `castles` SET `gold`=`gold`+'{$pay}' WHERE `id`='{$row['protector_id']}'";    	    
			echo time(),"-",$query1,"\n";
	    	$result1 = mysqli_query($link, $query1);
    		if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}
	    }
	}    	
    
    $query = "UNLOCK TABLES";
    $result = mysqli_query($link, $query);
    if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}
    mysqli_close($link);	
?>