dom = (document.getElementById)? true : false;
nn4 = (document.layers)? true : false;
ie4 = (!dom && document.all)? true : false;

var maxmenu = 4; // кол-во выпадашек
var start = 0; //
var activmenu = 0; //
var cur = 0; //
var tim;
var overactiv = 0; //
var overcolor = '#DE2020';
var outcolor = '#DE2020';

function showmenu(el)
{
 if (dom) document.getElementById(el).style.visibility = "visible";
 else if (ie4) document.all[el].style.visibility = "visible";
 else if (nn4) document.layers[el].visibility = "show";
 activmenu = 1;
}

function hidemenu(el)
{
 if (dom) document.getElementById(el).style.visibility = "hidden";
 else if (ie4) document.all[el].style.visibility = "hidden";
 else if (nn4) document.layers[el].visibility = "hide";
 activmenu = 0;
}

function hideall()
{
  for (i=1; i<=maxmenu; i++)
 {
 if (dom) document.getElementById('menu'+i).style.visibility = "hidden";
 else if (ie4) document.all['menu'+i].style.visibility = "hidden";
 else if (nn4) document.layers['menu'+i].visibility = "hide";
 }
 activmenu = 0;
}

function noactiv()
{
if (start != 0) hidemenu(cur)
}

function activ(el)
{
if (activmenu == 1)
{
if (el != cur) {hidemenu(cur); showmenu(el); cur = el; }
}
   else {showmenu(el); cur = el; }
}

function ClickActiv()
{
if (activmenu == 1) hidemenu(cur);
else showmenu(cur);
}

function timeactiv()
{
 overactiv=0;
 tim=setTimeout('tactiv()','1500');
}

function tactiv()
{
if (overactiv == 0 && activmenu == 1) hidemenu(cur);
}

function timedisable()
{
if (start != 0)
{clearTimeout(tim); overactiv=1}
else start = 1;
}

// --- функция подсветки ---

function bgLighting (div,optColor)
{
        if (optColor == 'over') {
        if (dom) document.getElementById(div).style.backgroundColor = overcolor;
        if(ie4) document.all[div].style.backgroundColor = overcolor;
        if(nn4) document.layers[div].bgColor = overcolor;
    }
    else {
        if (dom) document.getElementById(div).style.backgroundColor = outcolor;
        if(ie4) document.all[div].style.backgroundColor = outcolor;
        if(nn4) document.layers[div].bgColor = outcolor;
    }
}

var status1 = 1;

function SHblock(item)
{
if (item == 'infoblock1') {block = status1}

if (block == 1)
{
  if (dom) document.getElementById(item).style.display = 'none';
  else if (ie4) document.all[item].style.display = 'none';
  block = 0;
}
else
{
  if (dom) document.getElementById(item).style.display = 'block';
  else if (ie4) document.all[item].style.display = 'block';
  block = 1;
}
if (item == 'infoblock1') {status1 = block}
}
