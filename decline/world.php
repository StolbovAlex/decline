<?php

include '../data/rules.php';
session_start();

$w = 11;
$h = 11;
$link;

function doctor($link, $mag, $unit)
{
	global $army;

	$level = get_level($mag['experience']) + 1;	// +1 т.к. дальность начинается считать от соседне с магом клетки
	// запросы в базу по маг и по лечиммому юниту свежие, нужно только перепроверить не ушел ли юнит от мага на дистанцию больше чем дальность лечения $level
	if((abs($mag['x']-$unit['x']) <= $level) && (abs($mag['y']-$unit['y']) <= $level)) {
	    $query1 = "SELECT * FROM `castles` WHERE `id`='{$unit['castle_id']}'";
	    $result1 = mysqli_query($link, $query1);
		if (!$result1) {
			die('Ошибка запроса: '.mysqli_error());
		}

	    if(mysqli_num_rows($result1) > 0) {
	        $row1 = mysqli_fetch_assoc($result1);		
			if($row1['race'] == 'Люди') {
		    	$unit['health'] = $unit['health'] + ($level*$level + 10);		// уровень лечения = ($level*$level)+10  - 11,14,19,26,35,46,59,74,91
	    		if ($unit['health'] >= 100) {
	    			$mag['experience'] = $mag['experience'] + ($level*$level + 10) - ($unit['health'] - 100);	// если догоняли до 100 то магу не полная экспа
	    			$unit['health'] = 100;
	    		}
	    		else {
	    			$mag['experience'] = $mag['experience'] + ($level*$level + 10);	// если лечение прошло полностью
	    		}	
	    	    
		    	$level1 = get_level($unit['experience']);
		    	$unit['attack'] = ($unit['health'] * ($army[$unit['type']]['attack'] + $level1)/100);	// пересчитываем атаку и защиту с учетом нового здоровья и новой экспы
		    	$unit['defense'] = ($unit['health'] * ($army[$unit['type']]['defense'] + $level1)/100);	
	    		$query1 = "UPDATE `units` SET `health`='{$unit['health']}', `attack`='{$unit['attack']}', `defense`='{$unit['defense']}' WHERE `id`='{$unit['id']}'";
	    		$result1 = mysqli_query($link, $query1);
	    		if (!$result1) {
					die('Ошибка запроса: '.mysqli_error());
				}

	    		$mag['turns'] = $mag['turns'] - 1;					// результат лечения -1 ход
	    		$query1 = "UPDATE `units` SET `turns`='{$mag['turns']}',`experience`='{$mag['experience']}' WHERE `id`='{$mag['id']}'";
	    		$result1 = mysqli_query($link, $query1);
				if (!$result1) {
					die('Ошибка запроса: '.mysqli_error());
				}	    	    
			}
			else {
		    	echo "<script type=\"text/javascript\">";
		    	echo "alert('Лечение возможно только расы людей.');";
		    	echo "</script>";		    
			}
	    }
	    else {	// надо бы обработчик если юнит есть а замка у него нет !!!
		echo "<script type=\"text/javascript\">";
		echo "alert('Не найден владелец юнита.');";
		echo "</script>";		    		    
	    }
	}
	else {
	    echo "<script type=\"text/javascript\">";
	    echo "alert('Юнит недосягаем для лечения.');";
	    echo "</script>";		    	
	}
	return $mag;
}


function mag_neighbor($link, $mag)		// список юнитов передаваемых JS для отображения в меню мага
{
	global $army;

	$level = get_level($mag['experience']) + 1;
	$query = "SELECT * FROM `units` WHERE `x`>='".($mag['x']-$level)."' AND `x`<='".($mag['x']+$level)."' AND `y`>='".($mag['y']-$level)."' AND `y`<='".($mag['y']+$level)."' AND `health`<'100' ";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}	

	if(mysqli_num_rows($result) > 0) {
	    echo "mag_h = new Array(";
	    for($i = 0; $i < mysqli_num_rows($result); $i++) {
		$row = mysqli_fetch_assoc($result);
		if($i != 0) echo ",";
		echo "'",$row['id'],":x",$row['x'],"y",$row['y'],"-",$army[$row['type']]['name'],"-",$row['health'],"%'";
	    }
	    echo ");\n";
	}
}

function get_unit($link, $x, $y) {
	// юниты сортируются по защите, атакуешь самого сильного
	$query = "SELECT * FROM `units` WHERE `x`='".$x."' and `y`='".$y."' ORDER BY defense DESC";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}	

//	for($i=0; $i < mysql_num_rows($result); $i++) {	
	    $row = mysqli_fetch_assoc($result);
//	}
	return $row;
}

function get_city($link, $x, $y) {
	// атака на город
	$query = "SELECT * FROM `castles` WHERE `x`='".$x."' and `y`='".$y."'";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}	
	$row = mysqli_fetch_assoc($result);

	return $row;
};

function attack_city($link, $unit, $city)
{
	global $army;

	$query = "LOCK TABLES `castles` WRITE, `units` WRITE, `messages` WRITE";

	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}	
		
	// рассчитываем потери юнита
	$tmp = (city_level($city['population'])+1)/100 - $unit['attack'];	// сила замка от 0.01 до 0.08 в обороне
	// при ударе снимается 5% золота и 5%+2 населения
	$city['population'] = $city['population'] - (ceil($city['population']/5)+ 2);
	if($city['population'] > 0) {
		if(($city['protector_id'] != 0) && ($city['protector_id'] != $unit['castle_id'])) {		// если при ударе имело место смены замка куда платится оброк
		    $query = "INSERT INTO `messages` VALUES(null,NOW(),0,'Decline','{$city['protector_id']}','{$city['protector_name']}','Замок {$city['castle_name']} теперь платит дань другому замку.')";
		    $result = mysqli_query($link, $query);
		    if (!$result) {
				die('Ошибка запроса: '.mysqli_error());
			}	
		}
		$gold = round(($city['gold'] / 5),2);
		$city['gold'] = $city['gold'] -  $gold; 

		$query = "UPDATE `castles` SET `population`='".$city['population']."', `gold`='".$city['gold']."', `protector_id`='".$unit['castle_id']."', `protector_name`='".$unit['castle_name']."', `protector_tax`='10' WHERE `id`='".$city['id']."'";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}	

		if($gold > 0) {
			$query = "UPDATE `castles` SET `gold`=`gold`+'".$gold."' WHERE `id`='".$unit['castle_id']."'";
			$result = mysqli_query($link, $query);
			if (!$result) {
				die('Ошибка запроса: '.mysqli_error());
			}	
		}		
		if($tmp < 0) {	// войско выжило	
		    $level = get_level($unit['experience']);						// считаем предыдущий уровень
		    $unit['health'] = (abs($tmp) / (($army[$unit['type']]['attack']) + $level)) * 100;	// здоровье это процент текущей защиты от максимальной с учетом опыта
		    // экспа за удар по мирным жителям не начисляется
		    $unit['attack'] = ($unit['health'] * ($army[$unit['type']]['attack'] + $level)/100);	// пересчитываем атаку и защиту с учетом старого здоровья и новой экспы
		    $unit['defense'] = ($unit['health'] * ($army[$unit['type']]['defense'] + $level)/100);	
		    $unit['turns'] = $unit['turns'] - 1;
		    $query = "UPDATE `units` SET `health`='{$unit['health']}', `attack`='{$unit['attack']}', `defense`='{$unit['defense']}', `turns`='{$unit['turns']}' WHERE `id`='{$unit['id']}'";
    	   	$result = mysqli_query($link, $query);
    	   	if (!$result) {
				die('Ошибка запроса: '.mysqli_error());
			}	
		}
		else {			// войско погибло при атаке замка
		    $query = "delete from `units` WHERE `id`='{$unit['id']}'";
		    $result = mysqli_query($link, $query);
		    if (!$result) {
				die('Ошибка запроса: '.mysqli_error());
			}	

		    $unit['id'] = 0;
		    echo "<script type=\"text/javascript\">";
		    echo "alert('Ваше войско погибло.');";
		    echo "</script>";
		}
	}
	else {	// снос замка, остатки золота + бонус за старость замка
		$timelife = intval((time() - strtotime($city['date_creation'])) / 86400); 	// время жизни в днях		
		$gold = $city['gold'] + $timelife * ($timelife + 100)/10 + $city['destroyed']*($city['destroyed']+5);	// 

		$query = "DELETE FROM `castles` WHERE `id`='".$city['id']."'";	// удалении самого замка
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}	

		$query = "DELETE FROM `units` WHERE `castle_id`='".$city['id']."'";	// удалении юнитов замка
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}	

		$query = "DELETE FROM `messages` WHERE `recv_id`='".$city['id']."'";	// удалении сообщений замка
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}	

		$query = "UPDATE `castles` SET `gold`=`gold`+'".$gold."', `destroyed`=`destroyed`+'1' WHERE `id`='".$unit['castle_id']."'";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}	
	}

	$query = "UNLOCK TABLES";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}	

	echo "<script type=\"text/javascript\">";
	echo "alert('Вы заработали ",$gold," монет.');";
	echo "</script>";
	
	return $unit;
}


function attack_unit($link, $unitA, $unitD)
{
	global $army;

	$query = "LOCK TABLES `units` WRITE, `messages` WRITE";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}	

	// максимальный перепад высот 7, добавляется или убавляется 10% к атаке, 
	$tmp = $unitD['defense'] - ($unitA['attack'] + (get_map($unitD['x'],$unitD['y']) - get_map($unitA['x'],$unitA['y']))/10);
	//если силы оказались равны - рандом
	while($tmp == 0) $tmp = $tmp - (rand(0,1)-0.5);
	if($tmp > 0) {// погиб нападающий
		// для начала правим экспу - добавляем цену юнита, но не из таблицы army (там у не покупаемых нитов цена 0) а из базы, там фактическая цена
		// для обычных юнитов она переносится из тацлицы в базу при покупке, для спец. (н.п. ангел) при получении этого юнита. +10% от опытв погибшего юнита
		$level = get_level($unitD['experience']);							// считаем предыдущий уровень
		$unitD['health'] = (abs($tmp) / (($army[$unitD['type']]['defense']) + $level)) * 100;		// здоровье это процент текущей защиты от максимальной с учетом опыта
		$unitD['experience'] = $unitD['experience'] + $unitA['cost'] + $unitA['experience']/10;		// начисляем экспу
		$level = get_level($unitD['experience']);							// считаем новый уровень
		$unitD['attack'] = ($unitD['health'] * ($army[$unitD['type']]['attack'] + $level)/100);		// пересчитываем атаку и защиту с учетом старого здоровья и новой экспы
		$unitD['defense'] = ($unitD['health'] * ($army[$unitD['type']]['defense'] + $level)/100);	

		$query = "UPDATE `units` SET `health`='{$unitD['health']}', `experience`='{$unitD['experience']}', `attack`='{$unitD['attack']}', `defense`='{$unitD['defense']}' WHERE `id`='{$unitD['id']}'";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}	

		$query = "delete from `units` WHERE `id`='{$unitA['id']}'";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}	

		$unitA['id'] = 0;
		echo "<script type=\"text/javascript\">";
		echo "alert('Ваше войско погибло.');";
		echo "</script>";

		$query = "INSERT INTO `messages` VALUES(null,NOW(),0,'Decline','{$unitD['castle_id']}','{$unitD['castle_name']}','Ваше войско {$army[$unitD['type']]['name']} x={$unitD['x']} y={$unitD['y']} было атаковано.')";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}
	}
	else { //погиб защитник
		$unitA['turns'] = $unitA['turns'] - 1;
		$unitA['x'] = $unitD['x'];	// атакующий юнит встает на место убитого целевого 
		$unitA['y'] = $unitD['y'];
		$level = get_level($unitA['experience']);							// считаем предыдущий уровень
		$unitA['health'] = (abs($tmp) / (($army[$unitA['type']]['attack']) + $level)) * 100;		// здоровье это процент текущей защиты от максимальной с учетом опыта
		$unitA['experience'] = $unitA['experience'] + $unitD['cost'] + $unitD['experience']/10;		// начисляем экспу
		$level = get_level($unitD['experience']);							// считаем новый уровень
		$unitA['attack'] = ($unitA['health'] * ($army[$unitA['type']]['attack'] + $level)/100);		// пересчитываем атаку и защиту с учетом старого здоровья и новой экспы
		$unitA['defense'] = ($unitA['health'] * ($army[$unitA['type']]['defense'] + $level)/100);	

		$query = "UPDATE `units` SET `x`='{$unitA['x']}', `y`='{$unitA['y']}', `health`='{$unitA['health']}', `experience`='{$unitA['experience']}', `turns`='{$unitA['turns']}', `attack`='{$unitA['attack']}', `defense`='{$unitA['defense']}' WHERE `id`='{$unitA['id']}'";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}	

		$query = "DELETE FROM `units` WHERE `id`='{$unitD['id']}'";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}

		$query = "INSERT INTO `messages` values(null,NOW(),0,'Decline','{$unitD['castle_id']}','{$unitD['castle_name']}','Ваше войско {$army[$unitD['type']]['name']} x={$unitD['x']} y={$unitD['y']} было уничтожено войском {$army[$unitA['type']]['name']} из замка {$unitA['castle_name']}.')";
		$result = mysqli_query($link, $query);
		if (!$result) {
			die('Ошибка запроса: '.mysqli_error());
		}	
	}
	
	$query = "UNLOCK TABLES";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}	
	return $unitA;
}

function unit_move($link, $unit, $nap) {
	$x = $unit['x'];
	$y = $unit['y'];
	$turns = $unit['turns'];
	// при наличии у юнита ходов ...
	if($turns > 0) {
		switch($nap) {
			case 1:	$x=$x+1; $y=$y-1; break;
			case 2:	$x=$x+1; break;
			case 3:	$x=$x+1; $y=$y+1; break;
			case 4:	$y=$y+1; break;
			case 5:	$x=$x-1; $y=$y+1; break;
			case 6:	$x=$x-1; break;
			case 7:	$x=$x-1; $y=$y-1; break;
			case 8:	$y=$y-1; break;
		};
		// движение разрешено только на высоты от 3 и выше
		if(get_map($x, $y) > 2) {
			$target_unit = get_unit($link, $x, $y);
			$target_city = get_city($link, $x, $y);
			// если препятствие в виде юнита, самым сильным в точке (только для города)
			if ($target_unit != NULL) {	// на пути юнит
				// проверка на мирное соглашение
   				$query3 = "SELECT * FROM `peace` WHERE (`castle_A`={$unit['castle_id']} AND `castle_B`={$target_unit['castle_id']}) OR (`castle_A`={$target_unit['castle_id']} AND `castle_B`={$unit['castle_id']})"; // мирное соглашение    
				$result3 = mysqli_query($link, $query3);
				if (!$result3) {
					die('Ошибка запроса: '.mysqli_error());
				}
				if ($unit['castle_id'] == $target_unit['castle_id']) {	// свой
					if ($target_city != NULL) {	// но он в своем городе
						if ($unit['castle_id'] == $target_city['id']) { // заходим в город
							$unit['x'] = $x;
							$unit['y'] = $y;
							$unit['turns'] = $turns - 1;
							$query = "UPDATE `units` SET `x`='".$x."', `y`='".$y."', `turns`='".$unit['turns']."' WHERE `id`='".$unit['id']."'";
							$result = mysqli_query($link, $query);
							if (!$result) {
								die('Ошибка запроса: '.mysqli_error());
							}
						}
					}
					return $unit;	// в случае ссли юнит свой и он не взамке - ни чего не делаем
				}
				else { // чужой
					if (mysqli_num_rows($result3) == 0) {	// не в союзе
						$unit = attack_unit($link, $unit,$target_unit);
						return $unit;
					}
					else {
						echo "<script type=\"text/javascript\">";
						echo "alert('У Вас мирное соглашение с этим замком.');";
						echo "</script>";						
						return $unit;
					}
				}
			}
			// сиюда попадаем если юнитов нет 
			if($target_city != NULL) { // но есть город без юнитов
				$query3 = "SELECT * FROM `peace` WHERE (`castle_A`={$unit['castle_id']} AND `castle_B`={$target_city['id']}) OR (`castle_A`={$target_city['id']} AND `castle_B`={$unit['castle_id']})"; // мирное соглашение    
				$result3 = mysqli_query($link, $query3);
				if (!$result3) {
					die('Ошибка запроса: '.mysqli_error());
				}
				if($unit['castle_id'] == $target_city['id']) {	// и он свой 
					$unit['x'] = $x;
					$unit['y'] = $y;
					$unit['turns'] = $turns - 1;
					$query = "UPDATE `units` SET `x`='".$x."', `y`='".$y."', `turns`='".$unit['turns']."' WHERE `id`='".$unit['id']."'";
					$result = mysqli_query($link, $query);
					if (!$result) {
						die('Ошибка запроса: '.mysqli_error());
					}
					return $unit;
				}
				else {
					if (mysqli_num_rows($result3) == 0) {	// не в союзе
						$unit = attack_city($link, $unit,$target_city); // на чужой нападаем
						return $unit;
					}
					else {
						echo "<script type=\"text/javascript\">";
						echo "alert('У Вас мирное соглашение с этим замком.');";
						echo "</script>";
						return $unit;
					}
				}
			}
			
			// нет препятствий, бля, без goto избыточно получилось
			$unit['x'] = $x;
			$unit['y'] = $y;
			$unit['turns'] = $turns - 1;
			$query = "UPDATE `units` SET `x`='".$x."', `y`='".$y."', `turns`='".$unit['turns']."' WHERE `id`='".$unit['id']."'";
			$result = mysqli_query($link, $query);
			if (!$result) {
				die('Ошибка запроса: '.mysqli_error());
			}
		}
	}
	return $unit;
}

function generate_armylist($link, $sel_unit) {
	$query = "SELECT * FROM `units` WHERE `castle_id`='".$_GET['id']."'";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}

	echo "a = new Array(";
	for ($i=0; $i < mysqli_num_rows($result); $i++) {
		$row = mysqli_fetch_assoc($result);
		if($i != 0) echo ",";
		// координаты x и y
		echo "'",$row['x'],":",$row['y'],":";		// v[0], v[1]
		// тип юнита
		echo $row['type'],":";				// v[2]
		// здоровье
		echo $row['health'],":";			// v[3]
		// ходы
		echo $row['turns'],":";				// v[4]
		// id
		echo $row['id'],":";				// v[5]
		// not selected
		if ($row['id'] != $sel_unit) {
			echo "1";				// v[6]
		}
		else {
			//selected
			echo "2:";
			// атака
			echo $row['attack'],":";		// v[7]
			// защита
			echo $row['defense'],":";		// v[8]
			// опыт
			echo $row['experience'];		// v[9]
			// если выбран маг - информации по магу будет выводится после генерации массива юнитов.
			if($row['type'] == 9) {
				$mag_h = $row;
			}
		}
		echo "'";
	}
	echo ");\n";
	
	if(isset($mag_h)) {	// спец юниты - маги, сюда попадаем если маг выбран.
		mag_neighbor($link, $mag_h);
	}	
	// дальше будут остальные спец юниты
}

function generate_object($link, $x, $y) {
	$sx = $x - intval($GLOBALS['w']/2);
	$sy = $y - intval($GLOBALS['h']/2);
	
	// обработка замков
	$object = array();
	
	$query = "SELECT * FROM `castles` WHERE `x`>='".$sx."' AND `x`<='".($sx+$GLOBALS['w'])."' AND `y`>='".$sy."' AND `y`<='".($sy+$GLOBALS['h'])."'";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}

	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
	    $row = mysqli_fetch_assoc($result);
        echo "o",$row['x'],$row['y'],"='";			// v[0]
		// свой замок или нет, еще есть тип 0 - х.з. что за тип - отображался LIME !!!
	    echo ($row['id'] == $_GET['id']) ? "1:" : "2:";	// v[2]
		// тип иконки для замка - размер
	    echo "city",city_level($row['population']),":";	// v[3]
		// тип объекта - замок;
	    echo "c:";										// v[4]
		// имя замка
        echo $row['castle_name'],":";					// v[5]
		// id замка
        echo $row['id'],":";							// v[6]	
// население 
	    echo $row['population'],":";					// v[7]
// доп параметры - покровитель и клан, потом
	    echo "';\n";
	    
	    $object[$row['x'].$row['y']] = $row['id'];
    }
    // обработка юнитов
	$query = "SELECT * FROM `units` WHERE `x`>='".$sx."' AND `x`<='".($sx+$GLOBALS['w'])."' AND `y`>='".$sy."' AND `y`<='".($sy+$GLOBALS['h'])."'";
	$result = mysqli_query($link, $query);
	if (!$result) {
		die('Ошибка запроса: '.mysqli_error());
	}
	for ($i = 0; $i < mysqli_num_rows($result); $i++) {
		$row = mysqli_fetch_assoc($result);
		//если координаты юнита не сопадают с координатами замка - отображаем
		if (!isset($object[$row['x'].$row['y']])) {
			echo "o",$row['x'],$row['y'],"='";	// v[0]
			// свой замок или нет, еще есть тип 0 - х.з. что за тип - отображался LIME !!!
			echo ($row['castle_id'] == $_GET['id']) ? "1:" : "2:";	// v[0]
			// тип юнита
			echo "s",$row['type'],":";				// v[1]
			// тип объекта - юнит;
			echo "a:";						// v[2]
			if($row['castle_id'] == $_GET['id']) {
				// для своего нита печатаем здоровье и ходы
				echo $row['health'],":";
				echo $row['turns'],":";
			}
			else {
				// имя замка
				echo $row['castle_name'],":";				// v[3]
				// id замка
				echo $row['castle_id'],":";				// v[4]
			}
			// id юнита
			echo $row['id'],":";						// v[5]
			// доп параметры - покровитель и клан, потом
			echo "';\n";
		}
	}
}


function generate_map($x, $y) {
    $inFileName  = '/var/www/data/decline.map';
    $inFile = fopen($inFileName, 'rb');

    $sx = $x - intval($GLOBALS['w']/2);
    $sy = $y - intval($GLOBALS['h']/2);

    // чтение первых 4-х байт за один раз
    $bytes = fread($inFile, 4);
    $width = base_convert(base_convert(ord($bytes[1]), 10, 16).base_convert(ord($bytes[0]), 10, 16), 16, 10);
    $height = base_convert(base_convert(ord($bytes[3]), 10, 16).base_convert(ord($bytes[2]), 10, 16), 16, 10);
    fseek($inFile, $width * $sy + $sx, SEEK_CUR);
    
    echo "sx=".$sx.";sy=".$sy.";\n";
    echo"map='";
    for($i = 0; $i < $GLOBALS['w']; $i++) {
	$bytes = fread($inFile, $GLOBALS['w']);
	for($j = 0; $j < $GLOBALS['h']; $j++)
		echo dechex(ord($bytes[$j]));
	fseek($inFile, $width - $GLOBALS['w'], SEEK_CUR);
    };
    echo "';\n";
    fclose($inFile);
};
?>

<html>
<head>
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Карта</title>
</head>
<body topmargin=5 leftmargin=2 bgcolor="#dfdfb0">
<?php
/*
 * world.php
 * выдает рабочее поле для замка
 */
    if (!isset($_SESSION['UID'])) {
		echo "<script type=\"text/javascript\">";
		echo "alert('Необходимо авторизоваться.');";
		echo "document.location.href='/index.php';";
		echo "</script>";
		exit;
    }
    // если выбран замок
    if (isset($_GET["id"])) {
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

		if (mysqli_num_rows($result) == 0) {
			mysqli_close($link);
			echo "<script type=\"text/javascript\">";
			echo "alert('Для доступа к запрошенным рессурсам необходимо авторизоваться.');";
			echo "document.location.href='/index.php';";
			echo "</script>";
			exit;
		}
		$row = mysqli_fetch_assoc($result);
        $x = $cx = $row['x'];
        $y = $cy = $row['y'];
        echo "<table width=760 align=center><tr><td align=center><strong><font size=4>Карта мира<br>x",$cx," y",$cy,"</font></strong></td>";
		echo "<td width=468></td><td align=center><b><br>Время на Сервере<br><b><div id=clock></div><script language=javascript src=/clock.js></script></b><td></table>";
		// верхнее меню
		echo "<br><TABLE align=\"center\" border=\"1\" width=\"760\"><TR><TD bgcolor=\"#ffff99\" align=\"center\">";
		echo "<A href=/city.php?id=",$_GET['id'],">Назад в замок</A>&nbsp;&nbsp;";
		echo "<A href=/login.php>Мои замки</A>&nbsp;&nbsp;";
		echo "<A href=/buy.php?id=",$_GET['id'],">&nbsp;Наем войска</A>&nbsp;&nbsp;";
		echo "<A href=/dembel.php?id=",$_GET['id'],">Демобилизация</A>&nbsp;&nbsp;";
		echo "<A href=/peace.php?id=",$_GET['id'],">Мирные Соглашения</A>&nbsp;&nbsp;";
		echo "<A href=/logout.php>&nbsp;Выход из игры</A></TD></TR></TABLE>";
		// проверка на выбор юнита
		$sel_unit = 0;	// не выбран ни один юнит по умолчанию
		if (isset($_GET['id_sold'])) {
			$sel_unit = $_GET['id_sold'];
			$query1 = "SELECT * FROM `units` WHERE `id`='".$_GET['id_sold']."'";
			$result1 = mysqli_query($link, $query1);
			if (!$result1) {
				die('Ошибка запроса: '.mysqli_error());
			}
			// наличие юнита на карте не означает что он еще есть в базе, его могли убить....
			if(mysqli_num_rows($result1) != 0) {
				$row1 = mysqli_fetch_assoc($result1);
				// если есть юнит - заменяем координаты города его координатами
				$x = $row1['x'];
				$y = $row1['y'];
				// есть ли команда на движение
				if (isset($_GET['nap'])) {
					$row1 = unit_move($link, $row1, $_GET['nap']);
					// результаты движения функция вернет в туже структуру, если id = 0 значит юнит перестал сущестововать
					if ($row1['id'] == 0) {
						// если юнита больше нет возвращаем координаты замка
						$x = $cx;
						$y = $cy;
					}
					else {
						// если осталься жив передаем новые коодинаты
						$x = $row1['x'];
						$y = $row1['y'];
					}
				}
				if (isset($_GET['id_type'])) {		// спец функции
					switch($_GET['id_type']) {
						case 2:		// лечение магом
							if (isset($_GET['id_unit'])) {	// проверяем исходные данные целевого юнита
								$query2 = "SELECT * FROM `units` WHERE `id`='".$_GET['id_unit']."'";
								$result2 = mysqli_query($link, $query2);
								if (!$result2) {
									die('Ошибка запроса: '.mysqli_error());
								}

								if (mysqli_num_rows($result2) != 0) {
									$row2 = mysqli_fetch_assoc($result2);
									$row1 = doctor($link, $row1, $row2);
								}
								else {
									echo "<script type=\"text/javascript\">";
									echo "alert('Юнит для лечения не обнаружен.');";
									echo "</script>";
								}
							}
							else {
								echo "<script type=\"text/javascript\">";
								echo "alert('Не задан второй юнит.');";
								echo "</script>";
							}
							break;
						case 3:
							break;
					}	
				}
			}
		}
        // генерируем рабочее поле с данными для javascript
        echo "<script language=\"JavaScript\" src=\"/world.js\"></script>\n";
        echo "<script language=\"Javascript\"><!--\n";
		echo "var hours = ",date("H"),";\n";
		echo "var minutes = ",date("i"),";\n";
		echo "var seconds = ",date("s"),";\n";
		echo "city_id = ",$_GET['id'],";\n";
		generate_map($x, $y);
		generate_object($link, $x, $y);
		generate_armylist($link, $sel_unit);
		echo "startclock()\n";
		echo "showMap();\n";
		echo "showArmies();\n";
		echo "//--></script>\n";
		// описание форм
		echo "<form action=world.php method=get name=worldform id=worldform><input type=hidden name=id value=",$_GET['id'],"><input type=hidden name=id_sold value=\"\"></form>";
		echo "<form action=world.php method=get name=moveform id=moveform><input type=hidden name=id value=",$_GET['id'],"><input type=hidden name=nap value=\"\"><input type=hidden name=id_sold value=\"\"></form>";
		mysqli_close($link);
		//
    }
    else {
		echo "<script type=\"text/javascript\">";
		echo "alert('Замок не выбран.');";
		echo "document.location.href='/login.php';";
		echo "</script>";
		exit;
    }
?>
