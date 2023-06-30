<?php
//session_set_cookie_params($lifetime);
session_start();

/*
if(!isset($_COOKIE['un'])) {
    //echo "Cookie named un is not set!";
  } else {
    //echo "Cookie un is set!<br>";
    //echo "Value is: " . $_COOKIE['un'];
    $_SESSION['username']=$_COOKIE['un'];
  }


//$id=pg_escape_string($_GET['id']);

$user = $_SERVER['AUTH_USER'];

$username = $_SERVER['PHP_AUTH_USER'];


if (!$_SESSION['username']){
  //echo 'NON VA BENE';
  $_SESSION['origine']=basename($_SERVER['PHP_SELF']);
  $_COOKIE['origine']=basename($_SERVER['PHP_SELF']);
  header("location: ./login.php");
  //exit;
}   
*/ 
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="roberto" >

    <title>Bilaterale - Trasformazione piazzola </title>
<?php 
require_once('./req.php');

the_page_title();

if ($_SESSION['test']==1) {
  require_once ('./conn_test.php');
} else {
  require_once ('./conn.php');
}
?> 


<style>
#successo { display: none; }
</style>


</head>

<body>

<?php require_once('./navbar_up.php');

if ($id_role_SIT >1) {
  redirect('no_permessi.php');
  //exit;
}

$name=dirname(__FILE__);
?>


<div class="container">

<script>
  function servizioScelta(val) {
    document.getElementById('openservizio').submit();
  }


  function removeButton(via, percorso) {
    console.log("Bottone schiacciato");


     
      
      console.log('Id via '+ via);
      console.log('Id percorso '+percorso);



      var http = new XMLHttpRequest();
      var url = 'delete_via_percorso.php';
      var params = 'id_via='+encodeURIComponent(via)+'&id_percorso='+encodeURIComponent(percorso)+'';
      http.open('POST', url, true);

      //Send the proper header information along with the request
      http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

      http.onreadystatechange = function() {//Call a function when the state changes.
          if(http.readyState == 4 && http.status == 200) {
              if (http.responseText == 3) {
                //alert("Intervento chiuso con successo");
              } else {
                //alert(http.responseText);
              }
          }
      }
      http.send(params);

      $('#vie_percorsi').bootstrapTable('refresh');
      return false;

  }



</script>


<hr>
<form name="openservizio" method="post" id="openservizio" autocomplete="off" action="duplica_percorso.php" >
<div class="row">

<div class="form-group col-lg-6">
  <!--label for="via">servizio:</label> <font color="red">*</font-->
				
				
  <select class="selectpicker show-tick form-control" 
  data-live-search="true" name="perc_partenza" id="perc_partenza" required="">

  <option name="perc_partenza" value="NO">Seleziona il percorso da cui prendere le piazzole</option>
  <?php            
  $query2="select id_percorso, cod_percorso, descrizione, versione, id_servizio
  from elem.percorsi p 
  where id_categoria_uso in (3,6)
  and id_servizio in (SELECT id_servizio
  FROM elem.servizi where riempimento = 1) 
  order by id_servizio, descrizione";
  $result2 = pg_query($conn, $query2);
  //echo $query1;    
  while($r2 = pg_fetch_assoc($result2)) { 
      $valore=  $r2['id_percorso'];            
  ?>
              
          <option name="perc_partenza" value="<?php echo $r2['id_percorso'];?>" ><?php echo $r2['cod_percorso'] .' - ' .$r2['descrizione']. ' (ver. '.$r2['versione'].')';?></option>
  <?php } ?>

  </select>  
  <!--small>L'elenco delle piazzole..  </small-->        
</div>



<div class="form-group col-lg-6">
  <!--label for="via">servizio:</label> <font color="red">*</font-->
				
				
  <select class="selectpicker show-tick form-control" 
  data-live-search="true" name="servizio" id="servizio" required="">

  <option name="servizio" value="NO">Seleziona un servizio</option>
  <?php            
  $query2="SELECT id_servizio, descrizione
  FROM elem.servizi where riempimento =1 order by 2;";
  $result2 = pg_query($conn, $query2);
  //echo $query1;    
  while($r2 = pg_fetch_assoc($result2)) { 
      $valore=  $r2['id_servizio'];            
  ?>
              
          <option name="servizio" value="<?php echo $r2['id_servizio'];?>" ><?php echo $r2['id_servizio'] .' - ' .$r2['descrizione'];?></option>
  <?php } ?>

  </select>  
  <!--small>L'elenco delle piazzole..  </small-->        
</div>



<div  name="conferma2" id="conferma2" class="form-group col-lg-3 ">
<input type="submit" name="submit" id=submit class="btn btn-info" value="Recupera elementi nuovo percorso">
<!--button type="submit" class="btn btn-info">
Recupera dettagli servizio
</button-->
</div>



</div> <!-- fine row-->
</form>

<br>
<hr>


<?php
$id_percorso_originale=$_POST['perc_partenza'];

$id_servizio=$_POST['servizio'];

$cod_new=$_POST['perc_new'];

$check_stato_intervento=0;

$query1="SELECT id_servizio, descrizione
FROM elem.servizi where id_servizio = $1";

$result1 = pg_prepare($conn, "query1", $query1);
$result1 = pg_execute($conn, "query1", array($id_servizio));
$status1= pg_result_status($result1);

while($r1 = pg_fetch_assoc($result1)) {
  $desc_servizio=$r1['descrizione'];
  $tipo_rifiuto=$r1['tipo_rifiuto'];
}


$query2="SELECT id_percorso, descrizione, cod_percorso, versione
FROM elem.percorsi where id_percorso = $1";

$result2 = pg_prepare($conn, "query2", $query2);
$result2 = pg_execute($conn, "query2", array($id_percorso_originale));
$status2= pg_result_status($result2);

while($r2 = pg_fetch_assoc($result2)) {
  $desc_percorso_originale=$r2['descrizione'];
}

$query3="select distinct eap.frequenza, fo.descrizione_long  
from elem.elementi_aste_percorso eap 
join etl.frequenze_ok fo on fo.cod_frequenza = eap.frequenza::int
where id_asta_percorso in (select id_asta_percorso from elem.aste_percorso ap 
where ap.id_percorso=$1)";

$result3 = pg_prepare($conn, "query3", $query3);
$result3 = pg_execute($conn, "query3", array($id_percorso_originale));
$status3= pg_result_status($result3);



$query4="select t.id_turno, t.cod_turno  
from elem.percorsi p  
join elem.turni t on t.id_turno  = p.id_turno 
where p.id_percorso=$1";

$result4 = pg_prepare($conn, "query4", $query4);
$result4 = pg_execute($conn, "query4", array($id_percorso_originale));
$status4= pg_result_status($result4);



?> 
<h4> Percorso originale: <?php echo $desc_percorso_originale?> 
<br> Turno: 
<?php
while($r4 = pg_fetch_assoc($result4)) {
  echo $r4['cod_turno'];
}
?>
<br> Frequenze:
<ul>
<?php
while($r3 = pg_fetch_assoc($result3)) {
  echo "<li>".$r3['descrizione_long']."</li>";
}
?>
</ul>
</h4>
<h4> Servizio nuovo: <?php echo $desc_servizio?> 
</h4>

<h4> Nuovo percorso: <?php echo $cod_new?> 
</h4>

<form name="openservizio2" method="post" id="openservizio2" autocomplete="off" action="duplica_percorso.php" >
<div class="row">
<input type="hidden" id="perc_partenza" name="perc_partenza" value="<?php echo $id_percorso_originale?>">
<input type="hidden" id="servizio" name="servizio" value="<?php echo $id_servizio?>">

<div class="form-group col-lg-6">
  <!--label for="via">servizio:</label> <font color="red">*</font-->
				
				
  <select class="selectpicker show-tick form-control" 
  data-live-search="true" name="perc_new" id="perc_new" required="">

  <option name="perc_new" value="NO">Seleziona il percorso da cui prendere le piazzole</option>
  <?php            
  $query5="select id_percorso, cod_percorso, descrizione, versione, id_servizio
  from elem.percorsi p 
  where id_categoria_uso in (3,6)
  and id_servizio =$1";
  $result5 = pg_prepare($conn, "query5", $query5);
  $result5 = pg_execute($conn, "query5", array($id_servizio));
  $status5= pg_result_status($result5);
  //echo $query1;    
  while($r5 = pg_fetch_assoc($result5)) { 
      $valore=  $r5['cod_percorso']."_new";            
  ?>
              
          <option name="perc_new" value="<?php echo $r5['cod_percorso']."_new";?>" ><?php echo $r5['cod_percorso'] .' - ' .$r5['descrizione']. ' (ver. '.$r5['versione'].')';?></option>
  <?php } ?>

  </select>  
  <!--small>L'elenco delle piazzole..  </small-->        
</div>






<div  name="conferma2bis" id="conferma2bis" class="form-group col-lg-3 ">
<input type="submit" name="submit" id=submit class="btn btn-info" value="Crea nuovo codice percorso template">
<!--button type="submit" class="btn btn-info">
Recupera dettagli servizio
</button-->
</div>



</div> <!-- fine row-->
</form>




<hr>

<?php 
?>


<div id="spazio_tabella" class="row">
            <h3>
            <div class="col-lg-12" id="tabella_title" >
                    <h3>File per creazione template</h3>
            </div>
              </h3>
            <!--div class="noprint col-lg-6" >
            <button class="btn btn-info noprint" onclick="printClass('fixed-table-container')">
            <i class="fa fa-print" aria-hidden="true"></i> Stampa tabella </button>
            </div-->
				<div class="noprint" id="toolbar">
				<!--select class="form-control noprint">
					<option value="">Esporta i dati visualizzati</option>
					<option value="all">Esporta tutto (lento)</option>
					<option value="selected">Esporta solo selezionati</option>
				</select-->
				</div>
				<div id="tabella">
				<table  id="nuovo_percorso" class="table-hover" data-toggle="table" data-url="./tables/nuovo_percorso.php?p=<?php echo $id_percorso_originale;?>&s=<?php echo $id_servizio;?>" 
				data-show-search-clear-button="true"   data-show-export="true" data-export-type=['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'doc', 'pdf'] 
				data-search="true" data-click-to-select="true" data-show-print="true"  
				data-pagination="false" data-page-size=75 data-page-list=[10,25,50,75,100,200,500]
				data-sidePagination="true" data-show-refresh="true" data-show-toggle="false" data-show-columns="true" 
				data-filter-control="true" data-show-footer="false" data-toolbar="#toolbar">
        
        
<thead>

 	<tr>
        <!--th class="noprint" data-field="state" data-checkbox="true"></th-->    
		<th data-field="id_piazzola" data-sortable="true" data-visible="true"  data-filter-control="input">Id_piazzola</th>
		<th data-field="indirizzo" data-sortable="false" data-visible="true"  data-filter-control="input">Via</th>
		<th data-field="freq" data-sortable="false" data-visible="true"  data-filter-control="select">Frequenza percorso origine</th>
    <th data-field="elem_originale" data-sortable="false" data-filter-control="select" data-visible="true">Elementi percorso origine</th>
    <th data-field="elem_new" data-sortable="false" data-filter-control="select" data-visible="true">Elementi nuovo percorso elementi</th>
    <th data-field="num_new" data-sortable="false" data-filter-control="select" data-visible="true">Numero nuovi elementi</th>
    </tr>
</thead>
</table>



<script>




function Delete(value, row) {
    return '<form onsubmit="return removeButton('+row.id_via+','+row.id_percorso+');"><button class="btn btn-danger" type="submit">\
    <i class="fas fa-trash" title="Rimuovi piazzole '+row.nome+' dal percorso'+row.descrizione+'"></i></button></form>';
}


</script>
</div>	<!--tabella-->







           


</div> <!--row-->


</div>

<?php
require_once('req_bottom.php');
require('./footer.php');
?>



<script type="text/javascript">

$(function () {
	$('select').selectpicker();
});

</script>



</body>

</html>