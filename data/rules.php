<?php

$inFileName = '/var/www/data/decline.map';
$width = 589;   // ширина карты x
$height = 392;  // высота карты y

$army = array(// название		расса			атака	     защита	   ходы		цена	    аренда	
        array('name'=>'Тень',		'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 0
        array('name'=>'Пикеносец',	'race'=>'Люди',		'attack'=>1, 'defense'=>1, 'turns'=> 3, 'cost'=>5,  'rent'=>0.5),       //type 1
        array('name'=>'Охраник',	'race'=>'Люди',		'attack'=>1, 'defense'=>2, 'turns'=> 3, 'cost'=>10, 'rent'=>0.8),       //type 2
        array('name'=>'Лучник',		'race'=>'Люди',		'attack'=>2, 'defense'=>1, 'turns'=> 3, 'cost'=>10, 'rent'=>0.8),       //type 3
        array('name'=>'Разведчик',	'race'=>'Люди',		'attack'=>2, 'defense'=>1, 'turns'=> 6, 'cost'=>30, 'rent'=>2.0),       //type 4
        array('name'=>'Рыцарь',		'race'=>'Люди',		'attack'=>4, 'defense'=>3, 'turns'=> 5, 'cost'=>80, 'rent'=>5.0),       //type 5
        array('name'=>'Тяжелый рыцарь',	'race'=>'Люди',		'attack'=>3, 'defense'=>5, 'turns'=> 2, 'cost'=>60, 'rent'=>3.5),       //type 6
        array('name'=>'Паладин',	'race'=>'Люди',		'attack'=>6, 'defense'=>5, 'turns'=> 4, 'cost'=>120,'rent'=>8.0),       //type 7
        array('name'=>'Балиста',	'race'=>'Люди',		'attack'=>4, 'defense'=>1, 'turns'=> 3, 'cost'=>40, 'rent'=>1.5),       //type 8
        array('name'=>'Маг',            'race'=>'Люди',		'attack'=>2, 'defense'=>2, 'turns'=> 4, 'cost'=>80, 'rent'=>6.0),       //type 9
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 10
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 11
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 12
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 13
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 14
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 15
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 16
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 17
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 18
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 19
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 20
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 21
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 22
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 23
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 24
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 25
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 26
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 27
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 28
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 29
        array('name'=>'Ангел',          'race'=>'Люди',		'attack'=>10,'defense'=>10,'turns'=> 10,'cost'=>0,  'rent'=>0.0),  	//type 30
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 31
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 32
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 33
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 34
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 35
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 36
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 37
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 38
        array('name'=>'Тень',           'race'=>'Люди',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 39
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 40
        array('name'=>'Лучник',         'race'=>'Орки',		'attack'=>3, 'defense'=>2, 'turns'=> 3, 'cost'=>25, 'rent'=>1.0), 	//type 41
        array('name'=>'Толстяк', 	'race'=>'Орки',		'attack'=>2, 'defense'=>8, 'turns'=> 2, 'cost'=>150,'rent'=>2.0), 	//type 42
        array('name'=>'Катапульта', 	'race'=>'Орки',		'attack'=>5, 'defense'=>1, 'turns'=> 3, 'cost'=>60, 'rent'=>2.0), 	//type 43
        array('name'=>'Двухголовый', 	'race'=>'Орки',		'attack'=>5, 'defense'=>6, 'turns'=> 4, 'cost'=>140,'rent'=>6.0), 	//type 44
        array('name'=>'Дракон', 	'race'=>'Орки',		'attack'=>9, 'defense'=>6, 'turns'=> 8, 'cost'=>900,'rent'=>10.0), 	//type 45
        array('name'=>'Динозавр', 	'race'=>'Орки',		'attack'=>4, 'defense'=>3, 'turns'=> 5, 'cost'=>90, 'rent'=>2.0), 	//type 46
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 47        
        array('name'=>'Огр маг', 	'race'=>'Орки',		'attack'=>6, 'defense'=>6, 'turns'=> 6, 'cost'=>0,  'rent'=>6.0), 	//type 48
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 49        
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 50        
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 51        
        array('name'=>'Всевидящее Око', 'race'=>'Орки',		'attack'=>1, 'defense'=>1, 'turns'=> 8, 'cost'=>0,  'rent'=>0.0),       //type 52        
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 53        
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 54        
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 55        
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 56        
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 57        
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 58        
        array('name'=>'Тень',           'race'=>'Орки',		'attack'=>0, 'defense'=>0, 'turns'=> 0, 'cost'=>0,  'rent'=>0.0),       //type 59        
        array('name'=>'Паук',		'race'=>'Dark Empire',	'attack'=>4, 'defense'=>3, 'turns'=> 3, 'cost'=>0,  'rent'=>0.0),       //type 60        
        array('name'=>'Минотавр',       'race'=>'Dark Empire',	'attack'=>6, 'defense'=>6, 'turns'=> 3, 'cost'=>0,  'rent'=>0.0),       //type 61        
        array('name'=>'Черный паладин', 'race'=>'Dark Empire',	'attack'=>6, 'defense'=>13,'turns'=> 13,'cost'=>0,  'rent'=>0.0));	//type 62        


function get_level($experience)
{
    $level = 0;
    while(((($level+1)*($level+1)*100) <= $experience) && ($level <= 12)) {	// максимально возможный уровень - 12
	$level++;								// 100, 400, 900, 1600, 2500, 3600, 4900, 6400, 8100, 10000 12100 14400     это градация первой деки
    }
    return $level;
}

function city_level($population)
{
    $level = 300;
    for($i = 0; $i <= 7; $i++) {
	if($level > $population)						// уровни замка 300,600,1200,2400,4800,9600,19200
	    break;
	$level = $level * 2;
    }   
    return $i;     
}

function get_map($x, $y) {
	global $inFileName;
	global $width;
	global $height;
	
        $inFile = fopen($inFileName, 'rb');
        // чтение первых 4-х байт за один раз
        $bytes = fread($inFile, 4);
        $width = base_convert(base_convert(ord($bytes[1]), 10, 16).base_convert(ord($bytes[0]), 10, 16), 16, 10);
        $height = base_convert(base_convert(ord($bytes[3]), 10, 16).base_convert(ord($bytes[2]), 10, 16), 16, 10);
        fseek($inFile, $width * $y + $x, SEEK_CUR);
        $byte = fread($inFile, 1);
        fclose($inFile);
        return (ord($byte));
}
?>
