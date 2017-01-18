<?php
include "setting.php";
echo <<< EOF

<html>
<image src="simitlogo.jpg"><br>
Developer: Ng Jey Ruey (jeyruey@simit.com.my)<br>
Project Leader: KS Tan (kstan@simit.com.my)<br>
Organization: <a href='http://www.simit.com.my'>Sim IT Sdn Bhd</a><br>
    <h1>PHP Jasper XML ($version) Example</h1><br>
	
    <p><B>Example:</B></p>
    <li><a href='sample1.php' target='_blank'>Sample 1 <a> (Standard column base report)</li>
    <li><a href='sample2.php' target='_blank'>Sample 2</a> (Standard official document)</li>
    <li><a href='sample3.php' target='_blank'>Sample 3</a> (A5 Landscape Receipt)</li>
    <li><a href='sample4.php' target='_blank'>Sample 4</a> (Chinese Proft Of Concept)</li>
    <li><a href='sample5.php?id=1' target='_blank'>Sample 5</a> (Use TCPDF, with writeHTML output) (add text properties expression "writeHTML"="true")</li>

</html>
EOF;
?>
