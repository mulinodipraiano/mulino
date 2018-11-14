<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $settings['gbook_title']; ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=<?php echo $lang['enc']; ?>" />
<link href="../../../style2.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
.style1 {color: #EB7130}
.style2 {color: #FFFFFF}
-->
</style>
<script type="text/javascript"><!--
function openSmiley()
{
	w=window.open("smileys.php", "smileys", "fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=yes,resizable=yes,directories=no,location=no,width=500,height=300");
	if(!w.opener)
	{
		w.opener=self;
	}
}
//-->
</script>
<link href="<?php echo $settings['tpl_path']; ?>style.css" rel="stylesheet" type="text/css" />
<script type="text/JavaScript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_nbGroup(event, grpName) { //v6.0
  var i,img,nbArr,args=MM_nbGroup.arguments;
  if (event == "init" && args.length > 2) {
    if ((img = MM_findObj(args[2])) != null && !img.MM_init) {
      img.MM_init = true; img.MM_up = args[3]; img.MM_dn = img.src;
      if ((nbArr = document[grpName]) == null) nbArr = document[grpName] = new Array();
      nbArr[nbArr.length] = img;
      for (i=4; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
        if (!img.MM_up) img.MM_up = img.src;
        img.src = img.MM_dn = args[i+1];
        nbArr[nbArr.length] = img;
    } }
  } else if (event == "over") {
    document.MM_nbOver = nbArr = new Array();
    for (i=1; i < args.length-1; i+=3) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = (img.MM_dn && args[i+2]) ? args[i+2] : ((args[i+1])? args[i+1] : img.MM_up);
      nbArr[nbArr.length] = img;
    }
  } else if (event == "out" ) {
    for (i=0; i < document.MM_nbOver.length; i++) {
      img = document.MM_nbOver[i]; img.src = (img.MM_dn) ? img.MM_dn : img.MM_up; }
  } else if (event == "down") {
    nbArr = document[grpName];
    if (nbArr)
      for (i=0; i < nbArr.length; i++) { img=nbArr[i]; img.src = img.MM_up; img.MM_dn = 0; }
    document[grpName] = nbArr = new Array();
    for (i=2; i < args.length-1; i+=2) if ((img = MM_findObj(args[i])) != null) {
      if (!img.MM_up) img.MM_up = img.src;
      img.src = img.MM_dn = (args[i+1])? args[i+1] : img.MM_up;
      nbArr[nbArr.length] = img;
  } }
}
//-->
</script>
</head>

<body onload="MM_preloadImages('../../../graphics/itnav-over01.jpg','../../../graphics/itnav-over02.jpg','../../../graphics/itnav-over03.jpg','../../../graphics/itnav-over04.jpg','../../../graphics/itnav-over05.jpg','../../../graphics/itnav-over06.jpg','../../../graphics/itnav-over07.jpg','../../../../graphics/itnav-over08.jpg')">
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="356" height="260" valign="top" class="td-top"><a href="http://www.ilmulinodipraiano.com"><img src="../../../graphics/title01.jpg" width="356" height="68" border="0" /></a><br />
      <a href="../../../eng/description.html" target="_top" onclick="MM_nbGroup('down','group1','descr','',1)" onmouseover="MM_nbGroup('over','descr','../../../graphics/itnav-over01.jpg','',1)" onmouseout="MM_nbGroup('out')"><img src="../../../graphics/itnav-up01.jpg" alt="" name="descr" width="240" height="29" border="0" id="descr" onload="" /></a> <br />
      <a href="../../../eng/accomodations.html" target="_top" onclick="MM_nbGroup('down','group1','accom','',1)" onmouseover="MM_nbGroup('over','accom','../../../graphics/itnav-over02.jpg','',1)" onmouseout="MM_nbGroup('out')"><img src="../../../graphics/itnav-up02.jpg" alt="" name="accom" width="240" height="23" border="0" id="accom" onload="" /></a> <br />
      <a href="../../../images/photography.html" target="_top" onclick="MM_nbGroup('down','group1','photos','',1)" onmouseover="MM_nbGroup('over','photos','../../../graphics/itnav-over03.jpg','',1)" onmouseout="MM_nbGroup('out')"><img src="../../../graphics/itnav-up03.jpg" alt="" name="photos" width="240" height="23" border="0" id="photos" onload="" /></a> <br />
      <a href="../../../eng/location.html" target="_top" onclick="MM_nbGroup('down','group1','villalocation','',1)" onmouseover="MM_nbGroup('over','villalocation','../../../graphics/itnav-over04.jpg','',1)" onmouseout="MM_nbGroup('out')"><img src="../../../graphics/itnav-up04.jpg" alt="" name="villalocation" width="240" height="24" border="0" id="villalocation" onload="" /></a> <br />
      <a href="../../../eng/facilities.html" target="_top" onclick="MM_nbGroup('down','group1','facilities','',1)" onmouseover="MM_nbGroup('over','facilities','../../../graphics/itnav-over05.jpg','',1)" onmouseout="MM_nbGroup('out')"><img src="../../../graphics/itnav-up05.jpg" alt="" name="facilities" width="240" height="23" border="0" id="facilities" onload="" /></a> <br />
      <a href="../../../eng/rates.html" target="_top" onclick="MM_nbGroup('down','group1','rates','',1)" onmouseover="MM_nbGroup('over','rates','../../../graphics/itnav-over06.jpg','',1)" onmouseout="MM_nbGroup('out')"><img src="../../../graphics/itnav-up06.jpg" alt="" name="rates" width="240" height="25" border="0" id="rates" onload="" /></a> <br />
      <a href="../../../eng/contact.html" target="_top" onclick="MM_nbGroup('down','group1','contact','',1)" onmouseover="MM_nbGroup('over','contact','../../../graphics/itnav-over07.jpg','',1)" onmouseout="MM_nbGroup('out')"><img src="../../../graphics/itnav-up07.jpg" alt="" name="contact" width="240" height="27" border="0" id="contact" onload="" /></a> <br /><img name="guestbook" src="../../../../graphics/itnav-over08.jpg" border="0" alt="" onLoad="" /> <br />
    <div align="right"></div></td>
    <td width="404" valign="top" class="td-top"><div align="left"><img src="../../../graphics/pic-desc01.jpg" width="404" height="273" /></div></td>
  </tr>
	
<?php
if (isset($settings['notice']) && !empty($settings['notice']))
{
	echo '<div class="gbook_sign_notice">'.$settings['notice'].'</div>';
}
?>
<!--NOTICE END -->
</table>



