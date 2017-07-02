<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<style type="text/css">
#voucher-main { 
	width:700px;
	height:319px;
	background-image:url(/images/voucher.png);
	background-repeat: no-repeat; 
	font-family: Verdana, Arial, "Times New Roman", Times, serif !important;
	position:relative;
}

#voucher-content { 
	position:absolute;
	left: 85px;
	top: 80px; 
}
#voucher-details { 
	position:absolute;
	font-size:16px;
    font-weight:bold;
	left: 285px;
	top: 120px; 
}
</style>
<div id="voucher-main">
	<div id="voucher-content">
		<div id="voucher-title">
			<h3>This gift voucher entitles 1 person to the following course:<br />
				{{{$voucher->course->name}}}, at {{{$voucher->location->parentName}}} Coffee School.</h3>
		</div>
		<div id="voucher-details">
			Gift Voucher ID: {{{$voucher->id}}}<br />
			Expires: {{{$voucher->expiry_date}}}
		</div>
	</div>
</div>
</body>
</html>
