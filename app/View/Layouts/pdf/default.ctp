<?php
/**
 *
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */


?>
<!DOCTYPE html>
<html lang="pl">
<head>
<?php 
    echo $this->Html->charset(); 
    echo $this->fetch('css');
    
?>    
    <title></title>
    <!--
    <style>body{font:90% "lucida grande",verdana,helvetica,arial,sans-serif;color:#000;margin:0 auto}table{width:100%;table-layout:fixed;border-collapse:collapse}table td{vertical-align:top}table th,table td{padding:0.3em}p{margin:0;padding:0}label{font-weight:300;color:grey;font-size:8pt;display:block}table{margin-bottom:5mm}table td.nr-col,table th.nr-col{width:13em}table td.nr-col{text-align:right;padding:0 3mm}table td.td1{vertical-align:top}table td.td1>p{padding:1mm}table td.td1 .termin{color:red;font-size:1.6em}.karty-pdf>table td{border:0.5px solid black}.karty-pdf table tr:last-child td{border:none}table td.nr-col>p.ord-nr{font-size:1.6em;color:#007300}table td.jobnr{color:red}table td.jobnr>span{color:grey}table td.nr-col>p.ekspres{color:red}.karty-pdf table tr.darker{background:none repeat scroll 0 0 #e0e0e0}.karty-pdf table tr:last-child{font-weight:bold;background:transparent}.karty-pdf table th.lp,.karty-pdf table td.lp{width:2em}.karty-pdf table th.id,.karty-pdf table td.id{width:4em}.karty-pdf table .perso{width:1.5em}.karty-pdf table .jobnr{width:5em}.karty-pdf table th.ile,.karty-pdf table td.ile{width:7em}.karty-pdf table .cena{width:5em}.karty-pdf table td.ile,.karty-pdf table td.cena{text-align:right}.adresy-uwagi-pdf table td{padding:2mm}.adresy-uwagi-pdf table .adresy{width:30%}.adresy-uwagi-pdf table .adresy>div{margin-bottom:1em}.adresy-uwagi-pdf table .adresy>div label{font-size:0.6em;display:block}.adresy-uwagi-pdf table .adresy div.kasa p{margin-bottom:2mm}.grubszy-nag{font-weight:bold;margin-bottom:0.4em}.adresy-uwagi-pdf table th{text-align:left}.czas-wydruku{padding:0 3mm;font-size:0.7em;text-align:right}
    </style>-->
    
</head>
<body>    
<?php echo $this->fetch('content');  
?>
</body>
</html>
