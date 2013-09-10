<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
<script type="text/javascript" src="jscripts/tiny_mce/plugins/asciimath/js/ASCIIMathMLwFallback.js"></script>
<script type="text/javascript" src="jscripts/tiny_mce/plugins/asciisvg/js/ASCIIsvgPI.js"></script>
<script type="text/javascript">
var AScgiloc = 'http://www.imathas.com/imathas/filter/graph/svgimg.php';
var AMTcgiloc = "http://www.imathas.com/cgi-bin/mimetex.cgi";
</script>

<!--
Alternative, using ver 2.0 ASCIIMathML, Latex processing disabled to avoid
dollar sign confusion
<script type="text/javascript" src="ASCIIMathML2wMnGFallback.js"></script>
-->
</head>
<body>
<?php

//This file is used for testing purposes - to display the output of the 
//editor
echo stripslashes($_POST['elm1']);
?>
</body>
</html>