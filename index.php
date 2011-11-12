<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<title>Raumplanung V11 V12 F18</title>
<link href="style.css" type="text/css" rel="stylesheet"/>
<link href="jquery/jquery.datepick.css" type="text/css" rel="stylesheet"/>

<script type ="text/javascript" src="jquery/jquery.js"></script>

<script type ="text/javascript" src="jquery/jquery.datepick.js">
</script>
<!--letzte Bearbeitung:24.10.2011 by Bergner
	Raumplanung per website fuer die Comuterlabors der Ths
	eingefuegt wurde ein Test auf doppelte Planung und eine Hervorhebung der php-Meldungen
	jetzt dazu: Darstellung der Belegung aus Datenbanktabelle
	24.10.11 : Test auf frei laut Belegungsplan zugefügt

-->
</head>


<!-- Funktionsblock Datenbankmanagement in php -->
<?php

//Wurde etwas gewaehlt?
if($_GET['lehrer']!="Lehrer" && $_GET['datum']!="Datum" )
{ 
	save();
	
}else
{
	echo '<h1 class="red" >Erst waehlen!!</h1>';
}

/* Die komplexe Funktion save() ermittelt, ob der Raum amgewünschten Tag schon durch einen anderen
* Kollegen gebucht ist. Danach wird kontrolliert,ob der Raum planungshalber sowieso belegt ist.
* Treffen beide Bedingungen nicht zu, wird der Wunsch des Kollegen in die Tabelle der Vormerkungen
* eingtragen
*/



function save(){
include('conf.inc.php');

$lehrer=$_GET['lehrer'];
$stunde=$_GET['stunde'];
$klasse=$_GET['klasse'];
$datum=$_GET['datum'];
$raum=$_GET['raum'];

//Name des Wochentags berechnen-wegen Test ob Raum frei
$test_datum = $datum; 
$wochentage = array ('So','Mo','Di','Mi','Do','Fr','Sa'); 
list ($tag, $monat, $jahr) = split ('[.]', $test_datum) ; 
$dat = getdate(mktime ( 0,0,0, $monat, $tag, $jahr)); 
$wochentag = $dat['wday']; 
$wt= $wochentage[$wochentag]; 

//Name der Tabelle des Raumes ermitteln
$wo='plan'.$raum;

$res=mysql_connect($host,$user,$pwd) or die("geht nicht");

mysql_select_db($DB,$res);

// nicht benoetigte Eintraege loeschen
	$query="DELETE FROM $table WHERE Stunde='0'";
	$result=mysql_query($query);

//Test auf Doppelung	
	$query="SELECT * FROM $table Where Raum='$raum' AND Stunde='$stunde' AND Datum='$datum' ";
	$result1=mysql_query($query);
	$query="SELECT * FROM $wo Where Std=$stunde AND $wt!=''"; 
	$result2=mysql_query($query);
	if (mysql_num_rows($result1)!=0)
	{ 
		echo '<h1 class="red" >Schon verplant!</h1>';
//Ende Test auf Dopplung

//Test auf frei laut Belegungsplan	
	}elseif(mysql_num_rows($result2)!=0) {
		echo '<h1 class="red" >Raum nicht frei!</h1>';
	
//Ende Test auf frei


	}else{
//Eintragen		
		$query="INSERT INTO $table(Raum, Lehrer,Stunde,Klasse,Datum)
				values('$raum','$lehrer','$stunde','$klasse','$datum')";

		$result=mysql_query($query);

		mysql_close();

		
echo '<h1 class="red">ok!</h1>';
	}
	
}//end save


//Wunschliste anzeigen
function show(){
	include('conf.inc.php');
	$res=mysql_connect($host,$user,$pwd) or die("geht nicht");
	mysql_select_db($DB,$res);
	// nicht benoetigte Eintraege loeschen
	$query="DELETE FROM $table WHERE Stunde='0'";
	$result=mysql_query($query);	
	// Tabelle auslesen, Ausgabe filtern
	
	$query="SELECT*FROM $table ORDER BY Datum LIMIT 30";
	$result=mysql_query($query);

	 while ($data = mysql_fetch_assoc($result))
          // daten einer Spalte werden in dem Array $data gespeichert
  {
     echo "<tr>";
        echo "<td>".$data["Datum"]."</td>";
        echo "<td>".$data["Stunde"]."</td>";
        echo "<td>".$data["Lehrer"]."</td>";
        echo "<td>".$data["Klasse"]."</td>";
        echo "<td>".$data["Raum"]."</td>";
		echo "</tr>"; 
}
mysql_close();
}// Ende Show


//Der Belegungsplan wird dargestellt je nach mitgegebener Tabelle aus der DB
function fillTable($tab){
	include('conf.inc.php');
	$res=mysql_connect($host,$user,$pwd) or die("geht nicht");
	
	mysql_select_db($DB,$res);
// Tabelle auslesen, Ausgabe filtern
		
	$query="SELECT*FROM $tab" ;
	$result=mysql_query($query);
	
	 while ($data = mysql_fetch_assoc($result))
 /* Daten einer Spalte werden in dem Array $data gespeichert
 * dann wird jede leere Tabellenzelle gelb unterlegt. Diese Automatik ist gut im Blick
 * auf Stundenplanneugestaltung
 */
  {
		     
     echo "<tr>";
        echo "<td>".$data["Std"]."</td>";
			if ($data["Mo"]==""){$style=" bgcolor=\"#FFFF00\"";}else {$style=" bgcolor=\"#00FF00\"";}       
        echo "<td ".$style." >".$data["Mo"]."</td>";
			if ($data["Di"]==""){$style=" bgcolor=\"#FFFF00\"";}else {$style=" bgcolor=\"#00FF00\"";}  
        echo "<td ".$style." >".$data["Di"]."</td>";
        if ($data["Mi"]==""){$style=" bgcolor=\"#FFFF00\"";}else {$style=" bgcolor=\"#00FF00\"";}  
        echo "<td ".$style." >".$data["Mi"]."</td>";
        if ($data["Do"]==""){$style=" bgcolor=\"#FFFF00\"";}else {$style=" bgcolor=\"#00FF00\"";}  
        echo "<td ".$style." >".$data["Do"]."</td>";
        if ($data["Fr"]==""){$style=" bgcolor=\"#FFFF00\"";}else {$style=" bgcolor=\"#00FF00\"";}  
        echo "<td ".$style." >".$data["Fr"]."</td>";
		echo "</tr>"; 
}
mysql_close();
}//Ende fillTable
?>
<!-- Funktionsblock Ende  Beginn html-->


<body>



<div id="content">
<img class="logo" src="logo_rooms.jpg" />
<h1>Belegung der R&auml;ume V11 V12 F18</h1>	

<div id="eingabe">

<form name="F_eingabe" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="GET">
<!-- Lehrernamen auswaehlen -->
<select name="lehrer">
	<option >Lehrer</option>
	<option value="An">An</option>
	<option value="Bd">Bd</option>
	<option value="Be">Be</option>
	<option value="Bl">Bl</option>	
	<option value="Bn">Bn</option>
	<option value="Br">Br</option>
	<option value="Da">Da</option>
	<option value="Ec">Ec</option>
	<option value="Em">Em</option>
	<option value="En">En</option>
	<option value="Er">Er</option>
	<option value="Fi">Fi</option>
	<option value="FrC">FrC</option>
	<option value="Ge">Ge</option>
	<option value="Ha">Ha</option>
	<option value="Hau">Hau</option>
	<option value="He">He</option>
	<option value="Hn">Hn</option>
	<option value="Jb">Jb</option>
	<option value="Ka">Ka</option>
	<option value="Koe">Koe</option>
	<option value="Kor">Kor</option>
	<option value="Ks">Ks</option>
	<option value="La">La</option>
	<option value="Li">Li</option>
	<option value="MuA">MuA</option>
	<option value="MuB">MuB</option>
	<option value="Ni">Ni</option>
	<option value="Pa">Pa</option>
	<option value="Pau">Pau</option>
	<option value="Sae">Sae</option>
	<option value="Sik">Sik</option>
	<option value="Th">Th</option>
	<option value="Toe">Toe</option>
	<option value="Tr">Tr</option>
	<option value="Vl">Vl</option>
	<option value="Vp">Vp</option>
	<option value="Wi">Wi</option>
	<option value="Wo">Wo</option>

</select>
<!-- Ein Widget zur Datumswahl jquery wird angesprochen -->
<input type="text" id="datum" name="datum" value="Datum"/>


<!-- die Unterrichtsstunde -->
<select name="stunde">
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
</select>
<!-- Klassenbezeichner  Jg7-->
<select name="klasse">
	<option value="7.1">7.1</option>
	<option value="7.2">7.2</option>
	<option value="7.3">7.3</option>
	<option value="7.4">7.4</option>
	<option value="7.5">7.5</option>
	<option value="7.6">7.6</option>
<!-- Jg8 -->
	<option value="8.1">8.1</option>
	<option value="8.2">8.2</option>
	<option value="8.3">8.3</option>
	<option value="8.4">8.4</option>
	<option value="8.5">8.5</option>
<!--JG 9H-->
	<option value="9H1">9H1</option>

	<option value="9HP">9HP</option>
<!--Jg 10H --> 
	<option value="10HP">10HP</option>
	<option value="10H1">10H1</option>
	<option value="10H2">10H2</option>
	<option value="10H3">10H3</option>
<!--Jg 9R -->
	<option value="9R1">9R1</option>
	<option value="9R2">9R2</option>
<!-- Jg 10R -->
	<option value="10R1">10R1</option> 
	<option value="10R2">10R2</option>
</select>
<!-- die rooms -->
<select name="raum">
	<option value="V11">V11</option>
	<option value="V12">V12</option>
	<option value="F18">F18</option>
</select>
<!-- abschicken -->
<input type="submit" value="Senden, wenn sicher!" />
<a href="http://www.ths-berlin.de">zur&uuml;ck</a>
</form>

</div>



<div id="Anzeige">
<h2>Wunschliste</h2>
<table  class="myTab" border="1" >
	<tr>
		<td class="tabHead">Datum</td>
		<td class="tabHead">Stunde</td>
		<td class="tabHead">Lehrer</td>
		<td class="tabHead">Klasse</td>
		<td class="tabHead">Raum</td>
	</tr>
	<?php show(); ?>
	
</table>
</div>
<!-- Anzeige Belegungsplan aus Tabellen der DB -->
<div id="Woche">
	<h2>Belegungsplan</h2>	

<div id="plans">
	<h2>V11</h2>
<table  class="myTab" Border="1">
	<tr>
		<td class="tabHead">Std</td>
		<td class="tabHead">Mo</td>
		<td class="tabHead">Di</td>
		<td class="tabHead">Mi</td>
		<td class="tabHead">Do</td>
		<td class="tabHead">Fr</td>
	</tr>
	
	<?php fillTable("planV11"); ?>
</table>
</div>
<hr>


<h2>V12</h2>
<table  class="myTab" Border="1">
	<tr>
		<td class="tabHead">Std</td>
		<td class="tabHead">Mo</td>
		<td class="tabHead">Di</td>
		<td class="tabHead">Mi</td>
		<td class="tabHead">Do</td>
		<td class="tabHead">Fr</td>
	</tr>
	<?php fillTable("planV12"); ?>
</table>

</div>
<!-- Ende Content-->
<div id="space"><hr></div>
<div id="footer">



Made by G. Bergner,2010-2011 for THS Berlin
</div>

<!-- jquery-Funktion fuer Handler Datumspicker -->
<script type="text/javascript">
	$(function() {
		$('#datum').datepick();
	
	});
</script> 
<!-- Ende jquery-->
</body>
</html>

<!-- Am Ende eines steinigen Weges stehen wir und staunen -->

