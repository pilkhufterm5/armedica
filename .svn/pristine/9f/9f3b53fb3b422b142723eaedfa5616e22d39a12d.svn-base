<?
ob_start();
$date = date("D M d Y H:i:s");

?>
<html>
<head>
<title>Accounting System - Confidential</title>
<link rel="stylesheet" href="http://whatever.com/style.css" type="text/css">
</head>
<body>
<p><img src=http://www.someserver/someimage.jpg width=200 height=40></img></p>
<p>WebERP Accounting System
<br>
<? echo " Server Date and Time is "; ?>
<? echo $date; ?>
<br>
<? 
echo "Report generated from {$_SERVER['REMOTE_ADDR']}"; 
?>
</p><a name="p213-58"></a><p class="auto" id="p213-58">
<?
$connection = mysql_connect ('localhost', 'root','kamikase') or die("Error connecting to database");
mysql_select_db ('weberp305', $connection) or die("Error selecting database");
?>
</p><p>
The following Invoices are still unpaid.
</p>
<p>
<table width="100%">
<tr class="row"><td>Company</td><td>Type</td><td>Number</td><td>Account Name</td><td>Issue Date</td><td>Balance</td></tr>
<?php
$result = mysql_query("SELECT companies.coyname, systypes.typename, debtortrans.transno, debtorsmaster.name, date_format(debtortrans.trandate,'%D %b %y') as invoicedate, (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight +debtortrans.ovdiscount - debtortrans.alloc) as balance
FROM debtorsmaster, paymentterms, debtortrans, systypes, companies
WHERE systypes.typeid = debtortrans.type
AND debtorsmaster.paymentterms = paymentterms.termsindicator
AND debtorsmaster.debtorno = debtortrans.debtorno
AND ABS(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)>0.1");
$i = 0;
while($result_ar = mysql_fetch_assoc($result)){
?>
<tr <?php if($i%2 == 1){
echo "class='body2'";
}else{
echo "class='body1'";}?>>
<td>
<?php echo $result_ar['coyname']; ?></td>
<?php echo $result_ar['typename']; ?></td>
<td><?php echo $result_ar['transno']; ?></td>
<td><?php echo $result_ar['name']; ?></td>
<td><?php echo $result_ar['invoicedate']; ?></td>
<td>
<?php echo $result_ar['balance']; ?>
</td>
</tr>
<?php
$i+=1;
}
?>
</table>
</p>
</body>
</html>
<?
$body = ob_get_contents();
ob_end_clean();
$eol="\n";
$headers = '';
# To Email Address
$emailaddress="bowikaxu@bowikaxu.com";
# Message Subject
$emailsubject="Outstanding Invoices - ".date("jS M Y H:i:s");
# Message Body
$headers .= 'From: Weberp <weberp@someserver.com>'.$eol;
$headers .= 'Reply-To: Weberp <weberp@someserver.com>'.$eol;
$headers .= 'Return-Path: Weberp <weberp@someserver.com>'.$eol;    // these two to set reply address
$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
# Boundry for marking the split & Multitype Headers
$mime_boundary=md5(time());
$headers .= 'MIME-Version: 1.0'.$eol;
$headers .= "Content-Type: text/html; charset=iso-8859-1".$eol;
$headers .= "Content-Transfer-Encoding: 8bit".$eol;
$msg = "";
$msg .= $body.$eol.$eol;
# Finished
#$msg .= "--".$mime_boundary."--".$eol.$eol;  // finish with two eol's for better security. see Injection.
# SEND THE EMAIL
  mail($emailaddress, $emailsubject, $msg, $headers);
?>