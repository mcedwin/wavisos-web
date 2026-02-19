<?php

$path = dirname(__FILE__); 

$a='';
chdir($path);
//exec("git add .");  
exec("git pull",$output, $return_var);
var_dump($output);
echo "<h3 align = center> Succesfully commited all the files.</h3>";

?>