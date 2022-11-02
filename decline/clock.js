var zz = '';
function startclock()
{
  showtime();
    seconds ++;
    if(seconds > 59) {
	minutes++;
	seconds = 0;
	if(minutes > 59) {
	    hours ++;
	    minutes = 0;
	    if(hours > 23) {
    		hours = 0;
	    }
	}
    }
    setTimeout('startclock()', 1000);
}

function zerodraw(strin)
{
  if(strin < 10) zz = '0'+ strin;
  else zz = strin;
}

function showtime()
{
    var mm = '';
//  if(minutes < 3) mm = mm + '<font color=red>';
    zerodraw(hours);
    mm = mm + zz +  ':';
    zerodraw( minutes );
    mm = mm + zz +  ':';
    zerodraw( seconds );
    mm = mm + zz +  ' (IRK)';
    if(minutes < 3) mm = mm + '</font>';
    document.all['clock'].innerHTML = mm;
}

if(typeof showclock != 'undefined' )
{
  showclock();
}
allloaded = 1;
