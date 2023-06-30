<?php
session_set_cookie_params($lifetime);
session_start();


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
if ($id_role_SIT >=5) {
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
<form name="openservizio" method="post" id="openservizio" autocomplete="off" action="vie_percorsi.php" >
<div class="row">

<div class="form-group col-lg-6">
  <!--label for="via">servizio:</label> <font color="red">*</font-->
				
				
  <select class="selectpicker show-tick form-control" 
  data-live-search="true" name="servizio" id="servizio" onchange="servizioScelta(this.value);" required="">

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
<!--input type="submit" name="submit" id=submit class="btn btn-info" value="Recupera dettagli servizio"-->
<!--button type="submit" class="btn btn-info">
Recupera dettagli servizio
</button-->
</div>



</div> <!-- fine row-->
</form>

<br>
<hr>


<?php
$id_servizio=$_POST['servizio'];
$check_stato_intervento=0;

$query1="SELECT id_servizio, descrizione
FROM elem.servizi where id_servizio = $1";

$result1 = pg_prepare($conn, "query1", $query1);
$result1 = pg_execute($conn, "query1", array($id_servizio));
$status1= pg_result_status($result1);

while($r1 = pg_fetch_assoc($result1)) {
  $desc_servizio=$r1['descrizione'];
}
?> 
<h1> Servizio: <?php echo $desc_servizio?> 
</h1>


<?php 
?>


<div id="spazio_tabella" class="row">
            <h3>
            <div class="col-lg-12" id="tabella_title" >
                    <h3>Elenco vie - percorsi  </h3>
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
				<table  id="vie_percorsi" class="table-hover" data-toggle="table" data-url="./tables/vie_percorsi.php?s=<?php echo $id_servizio;?>" 
				data-show-search-clear-button="true"   data-show-export="true" data-export-type=['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'doc', 'pdf'] 
				data-search="true" data-click-to-select="true" data-show-print="true"  
				data-pagination="true" data-page-size=75 data-page-list=[10,25,50,75,100,200,500]
				data-sidePagination="true" data-show-refresh="true" data-show-toggle="false" data-show-columns="true" 
				data-filter-control="true" data-show-footer="false" data-toolbar="#toolbar">
        
        
<thead>

 	<tr>
        <!--th class="noprint" data-field="state" data-checkbox="true"></th-->    
		<th data-field="nome" data-sortable="true" data-visible="true"  data-filter-control="input">Nome via</th>
		<th data-field="id_via" data-sortable="false" data-visible="false"  data-filter-control="input">Id via</th>
		<th data-field="descrizione" data-sortable="false" data-visible="true"  data-filter-control="select">Percorso</th>
    <th data-field="id_percorso" data-sortable="false" data-filter-control="select" data-visible="false">Id percorso</th>
    <th data-field="n_elem" data-sortable="false" data-filter-control="select" data-visible="true">Numero elementi</th>
    <?php if ($role_SIT=='IT' or $role_SIT=='ADMIN'){?>
    <th data-field="aggiornamenti" data-sortable="true" data-visible="true" data-formatter="Delete">Rimuovi via</th>
      <?php }?>
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