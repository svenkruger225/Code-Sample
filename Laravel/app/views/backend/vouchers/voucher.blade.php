<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>The Coffee School {{$voucher->course->name}} Voucher</title>
<style type="text/css">
<!--
body {
	font-family: Verdana, Geneva, sans-serif;
	line-height: 1.1;
}

h1{	font-size: 52px; margin: 4px 0px 6px}
h2{	font-size: 28px; margin: 3px 0px 5px}
h3{	font-size: 18px; margin: 3px 0px 3px}
h4{	font-size: 16px; margin: 2px 0px 2px}
h5{	font-size: 12px;  margin:0}
h6{	font-size: 8px;  margin:0}
p{	font-size: 12px;  margin:1px 0px 1px}
#voucher-main {
	width:700px;
	height:321px;
	background-color: transparent;
	background-image: url(/images/voucher_bg.png);
	background-repeat: no-repeat;
	position:relative;
}
#voucher-content { 
	position:absolute;
	left: 65px;
	top: 30px; 
}

-->
</style>
</head>

<body>

<div id="voucher-main">
<div id="voucher-content">
	<div><h1><em>Gift Voucher</em></h1></div>
	<div><h4>This gift voucher entitles 1 person to the following course:</h4></div>
	<div><h4>{{$voucher->course->name}}. at {{$voucher->location->name}} Coffee School</h4></div>
	<div>&nbsp;<br>&nbsp;<br>&nbsp;</div>
	<div>
		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		  <tr>
			<td width="50%" align="left" valign="bottom">
            	<h2>Coffee School</h2>
			    <p><b>SYDNEY | MELBOURNE | BRISBANE | PERTH</b></p>
                <p>Ph: (02) 9652 6771, Mob: 0425 304 774</p>
                <p>info@coffeeschool.com.au | www.coffeeschool.com.au</p>
            </td>
			<td width="50%" align="left" valign="bottom">
            	<h4>Gift Voucher ID: {{$voucher->id}}</h4>
			    <h4>Expires: {{date('d F Y', strtotime($voucher->expiry_date))}}</h4>
				<p>&nbsp;</p>
                <p>To book using this voucher please enter your<br />voucher ID at www.coffeeschool.com.au</p>
            </td>
		  </tr>
		</table>
	</div>
</div>
</div>


</body>
</html>
