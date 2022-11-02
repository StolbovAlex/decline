function showMap() {
    m=0;
    document.write('<DIV align=center><TABLE border=0 cellpadding="0" cellspacing="0" width="765"><TR><TD valign="top"><TABLE border="0" cellspacing="0" cellpadding="1" bgcolor=black>');

    document.write('<tr><td bgcolor=#DDDD00 align=center>Y/X</td>');	
    for(x=0;x<11;x++) {	
        document.write('<td bgcolor='+(((sx+x)%2==0) ? '#DDDD00' : '#FFFF99')+' align=center>'+(sx+x)+'</td>');	
    }
    document.write('</tr>');
    for (y = 0; y < 11; y++) { 
		cy=sy+y;
		document.write('<tr><td bgcolor='+(((cy%2)==0) ? '#DDDD00' : '#FFFF99')+' align=center>'+cy+'</td>');	
		for ( x = 0; x < 11; x++) {	
	    	cx = sx + x;
	    	h = map.substr(m,1);
	    	if (h == 'a') h=10;
	    	c = '';
	    	s = eval("(typeof o"+cx+cy+"=='undefined'?'':o"+cx+cy+")");
	    	alt = 'x'+cx+' y'+cy+' Высота '+h+' ';
	    	img = h;
	    	href = '';
	    	if ( s != '') {
				v=s.split(':');
				href=' style="cursor: hand"';
				img=v[1];
				c=(v[0]=='0'?'lime':(v[0]=='1'?'yellow':'red')); 
				switch(v[2]) {
		    		case 'a':
						if(v[0]==2) {
			    			if(v[3].length > 0) {
								alt=alt+'замок '+v[3]+' id '+v[4];
		    					href=href+' onClick="document.location.href = \'messages.php?id='+city_id+'&recv_name='+v[3]+'\'"';								    
			    			}
						}
						else {
							alt = alt+'здоровье='+v[3]+' ходов='+v[4];
							if ((x == 5) && (y == 5)) c = 'blue';
				    		href=href+' onClick="document.location.href = \'world.php?id='+city_id+'&id_sold='+v[5]+'\'"';
						}
						break;
		    		case 'c':
						if((x == 5) && (y == 5)) c = 'blue';
			    		alt = alt+'замок '+v[3]+' id '+v[4]+' население '+v[5];
						if(v[6]!='') alt+=' покровитель '+v[6];
			    		if(v[7]!='') alt+=' клан '+v[7];
						if(v[0]==2) href=href+' onClick="document.location.href = \'messages.php?id='+city_id+'&recv_name='+v[3]+'\'"';								    
						else href=href+' onClick="document.location.href = \'city.php?id='+v[4]+'\'"';
						break;
				}
	    	}
	    	document.write('<TD bgcolor="'+c+'"><IMG align="middle" title="'+alt+'" border=0 height=40 src="/pic/'+img+'.gif" width=40 onMouseOver="window.status=\''+alt+'\'; return true;" onMouseOut="window.status=\'\'; return false; " '+href+'></TD>');
			m++;
		}
		document.write('</tr>');
    }
    document.write('</TABLE></TD>');
}

function showArmies()
{
    selected = '';
    document.write('<TD rowspan="10" align="middle" bgcolor="#ffff99" valign="top" width="300" align="center"><TABLE width=300><TR bgcolor="#dddd00" align=center>')

    for (i = 0; i < a.length; i++ ) {
		v = a[i].split(':');
	    if (v[6] == '2') {
			selected = a[i];
			bc='style="border-color: black"';
			id=v[5];
	    }
    }

    if (selected!='') {
		v = selected.split(':');
		document.write('<TD width=145><strong>Движение</strong></TD><TD width=145><STRONG>Инфо</STRONG></TD></TR><TR valign=top><TD align=center>');
		if (v[4] > 0) {
	    	document.write('<table width="144" cellspacing=0 cellpadding=2><tr height=20 align=center><td colspan=2><button class="bttn" onclick="movesold(7,'+id+')" title="северо-запад"><span class="sign">&#8598;</span></button></td><td><button class="bttn" onclick="movesold(8,'+id+')" title="север"><span class="sign">&#8593;</span></button></td><td colspan=2><button class="bttn" onclick="movesold(1,'+id+')" title="северо-восток"><span class="sign">&#8599</span></button></td></tr><tr height=20 align=center><td colspan=2><button class="bttn" onclick="movesold(6,'+id+')" title="запад"><span class="sign">&#8592;</span></button></td><td>&nbsp;</td><td colspan=2><button class="bttn" onclick="movesold(2,'+id+')" title="восток"><span class="sign">&#8594;</span></button></td></tr><tr height=20 align=center><td colspan=2><button class="bttn" onclick="movesold(5,'+id+')" title="юго-запад"><span class="sign">&#8601;</span></button></td><td><button class="bttn" onclick="movesold(4,'+id+')" title="юг"><span class="sign">&#8595;</span></button></td><td colspan=2><button class="bttn" onclick="movesold(3,'+id+')" title="юго-восток"><span class="sign">&#8600;</span></button></td></tr>');
	    	if(typeof mag_h!='undefined') {
				document.write('<FORM action=world.php method=get name="form"><TR><TD colspan=5><SELECT name=id_unit class="bttn">');
				for(i = 0; i < mag_h.length; i++) {
		    		vv=mag_h[i].split(':');
		    		document.write('<OPTION value='+vv[0]+'>'+vv[1]);
				}
				document.write('<OPTION SELECTED></select><input class="bttn" type="hidden" name="id" value="'+city_id+'"><input class="bttn" type="hidden" name="id_sold" value="'+id+'"><input class="bttn" type="hidden" name="id_type" value="2"><INPUT class="bttn" name=bok type=submit value="Лечить"></TD></TR></FORM>');
	    	}
	    	if (typeof mag_t!='undefined') {
				document.write('<FORM action=world.php method=post name="form"><TR><TD colspan=5><SELECT class="bttn" name=gname>');
				for(i = 0; i < mag_t.length; i++) {
		    		vv=mag_t[i].split(':');
		    		document.write('<OPTION value='+vv[0]+'>'+vv[1]);
				}
				document.write('<OPTION SELECTED></select><br><input type="hidden" name="id_type" value="3"><INPUT class="bttn" name=bok type=submit value="Телепорт"><input type="hidden" name="id2" value="'+id+'"></TD></TR></FORM>');
	    	}
	    	if (typeof twohead_e!='undefined') {
				document.write('<FORM action=world.php method=post name="form"><TR><TD colspan=5>');
				document.write('<input type="hidden" name="id_type" value="4"><INPUT class="bttn" name=bok type=submit value="Вырвать Глаз"><input type="hidden" name="id2" value="'+id+'"></TD></TR></FORM>');
	 		}
	    	if (typeof spy_v!='undefined') {
				document.write('<TR><TD colspan=5 align=center><SELECT class="bttn" name=gname>Подглядывать<br>');
				for (i = 0;i < spy_v.length; i++) {
		    		document.write('<OPTION>'+spy_v[i]);
				}
				document.write('<OPTION SELECTED></select></TD></TR>');
	    	}
	    	document.write('</table>');
		}
		else
	    	document.write('<hr width=145 color="#ffff99">');
		document.write('</TD><TD width=145><TABLE width=140><TR><TD width=70>&nbsp;X '+v[0]+'</TD><TD width=70 align=right>&nbsp;Y '+v[1]+'</TD></TR><TR><TD>&nbsp;Атака</TD><TD align=right>'+v[7]+'</TD></TR><TR><TD>&nbsp;Защита</TD><TD align=right>'+v[8]+'</TD></TR><TR><TD>&nbsp;Здоровье</TD><TD align=right>'+v[3]+'%</TD></TR><TR><TD>&nbsp;Опыт</TD><TD align=right>'+v[9]+'</TD></TR><TR><TD>&nbsp;Ходов</TD><TD align=right>'+v[4]+'</TD></TR></TABLE></TD></TR><TR bgcolor="#dddd00" align=center>');
    }
    document.write('<TD colspan=2><STRONG>Выбор войска</STRONG></TD></TR><TR><TD colspan=2>');
    document.write('<table cellspacing=0 cellpadding=1 border=0><tr height=40>');
    for (i = 0; i < a.length; i++ ) {
		v = a[i].split(':');
		alt = 'x'+v[0]+' y'+v[1]+' здоровье='+v[3]+' ходов='+v[4];
		bc = 'style="border-color: white"';
		if (typeof v[6] != 'undefined') {
			selected=a[i];
			bc='style="border-color: black"';
		}
		document.write('<td width=40><IMG onclick="clicksold('+v[5]+')" src="pic/s'+v[2]+'.gif" width="40" height="40" border=1 '+bc+' name="id_sold" alt="'+alt+'" style="cursor: hand" onMouseOver="window.status=\''+alt+'\'; return true;" onMouseOut="window.status=\'\'; return false;"></TD>');
		document.write('<td width=2 valign=bottom><img src="');
		if (v[3] > 49) document.write('pic/green.gif');
		else 
			if (v[3] > 24) document.write('pic/yellow.gif');
			else document.write('pic/red.gif');
			document.write('" width=2 height="'+(40/100)*v[3]+'"></TD>');
			document.write('<td width=12 align=left valign=bottom><small>'+v[4]+'</small></td>');
			if((i+1)%5 == 0 && a.length != i+1 ) document.write('</TR><TR height=40>');
    }
    if(i%5 != 0) document.write('<TD colspan="' + 3*(6-((i+1)%5))+ '">&nbsp;</TD>');
    document.write('</TR></TABLE></TD></TR>');
    document.write('</TD></TR></TABLE></TD></TR></TABLE>');
    if(sbtxt.length>0) alert(sbtxt);
}

function clicksold(id_s)
{
    document.worldform.id_sold.value=id_s;
    document.worldform.submit();
}

function movesold(nap,id_s)
{
    document.moveform.nap.value=nap
    document.moveform.id_sold.value=id_s
    document.moveform.submit();
}

