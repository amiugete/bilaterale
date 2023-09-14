<?php
//session_set_cookie_params($lifetime);
session_start();


/*if(!isset($_COOKIE['un'])) {
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
$name=dirname(__FILE__);
//echo $id_role_SIT;
//exit;
if ((int)$id_role_SIT == 0) {
  redirect('no_permessi.php');
  //exit;
}

?>


<div class="container">

<script>
  function piazzolaScelta(val) {
    document.getElementById('openpiazzola').submit();
    
    /*window.location.href = window.location.href;
    var id_piazzola=document.getElementById('piazzola').value;
    console.log(id_piazzola);
    console.log(val);
    $.ajax({
      type: "POST",
      url: "piazzola.php",
      data: {'piazzola':val},
      success: function() {   
          location.reload();  
      }
    });*/



    /*var id_piazzola=document.getElementById('piazzola').value;
    console.log(id_piazzola);
    var http = new XMLHttpRequest();
    var url = 'piazzola.php';
    var params = 'piazzola='+encodeURIComponent(id_piazzola)+'';
    http.open('POST', url, true);

    //Send the proper header information along with the request
    http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    http.onreadystatechange = function() {//Call a function when the state changes.
        if(http.readyState == 4 && http.status == 200) {
          console.log('Sono qua 0');
            if (http.responseText == 3) {
              console.log('Sono qua 1');
              //alert("Intervento chiuso con successo");
            } else {
              console.log('Sono qua 2');
              //alert(http.responseText);
            }
        }
    }
    http.send(params);*/
    /*$("#dettagli").load(location.href + " #dettagli");
    $("#comp_piazz").load(location.href + " #comp_piazz");
    $("#dettagli").hide();
    $("#dettagli").show();
    $("#comp_piazz").hide();
    $("#comp_piazz").show();*/
    //window.location.reload();

    //return false;
  }
</script>

<script type="text/javascript">
        
function clickButton2() {
      console.log("Bottone update piazzola cliccato");


     
      var id_piazzola=document.getElementById('id_piazzola').value;
      console.log(id_piazzola);

      var civ=document.getElementById('civ').value;
      console.log(civ);
      
      var rif=document.getElementById('rif').value;
      console.log(rif);
      
      var note=document.getElementById('note').value;
      console.log(note);

      if ($('input[name="privato"').is(':checked')){
        var privato=1;
      } else {
        var privato=0;
      }
      console.log(privato);
  


      var http = new XMLHttpRequest();
      var url = 'update_piazzola.php';
      //var params = 'id_piazzola='+encodeURIComponent(id_piazzola)+'&civ='+encodeURIComponent(civ)+'&rif='+encodeURIComponent(rif)+'&note='+encodeURIComponent(note)+'&privato='+encodeURIComponent(privato)+'';
      var params = new FormData();
      params.append('id_piazzola', id_piazzola);
      params.append('civ', civ);
      params.append('rif', rif);
      params.append('note', note);
      params.append('privato', privato);
      http.open('POST', url, true);

      //Send the proper header information along with the request
      /*http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

      http.onreadystatechange = function() {//Call a function when the state changes.
          if(http.readyState == 4 && http.status == 200) {
              console.log(http.responseText);
          }
      }*/
      http.onload = function () {
        // do something to response
        console.log(this.responseText);
      };

      http.send(params);
      
      $("#dettagli_piazzola").hide();
      console.log('Nascosto DIV');*/
      $( "#refreshDataContainer" ).load(window.location.href + " #refreshDataContainer");
      console.log('Ricaricato DIV');
      $("#dettagli_piazzola").show();
      console.log('show DIV');*/
      //$("#dettagli_piazzola").load(location.href + " #dettagli_piazzola>*","");
    
      //$('#refreshDataContainer').load('piazzola.php #dettagli_piazzola');
      window.location.reload(true);
      return false;

  }




</script>


<hr>
<form name="openpiazzola" method="post" id="openpiazzola" autocomplete="off" action="piazzola.php" >
<div class="row">

<div class="form-group col-lg-6">
  <!--label for="via">Piazzola:</label> <font color="red">*</font-->
				
				
  <select class="selectpicker show-tick form-control" 
  data-live-search="true" name="piazzola" id="piazzola" onchange="piazzolaScelta(this.value);" required="">

  <option name="piazzola" value="NO">Seleziona una piazzola</option>
  <?php            
  $query2="SELECT id_piazzola, concat(via, ',',civ, ' - ',riferimento)  as rif
  FROM elem.v_piazzole_dwh vpd;";
  $result2 = pg_query($conn, $query2);
  //echo $query1;    
  while($r2 = pg_fetch_assoc($result2)) { 
      $valore=  $r2['id_via']. ";".$r2['nome'];            
  ?>
              
          <option name="piazzola" value="<?php echo $r2['id_piazzola'];?>" ><?php echo $r2['id_piazzola'] .' - ' .$r2['rif'];?></option>
  <?php } ?>

  </select>  
  <!--small>L'elenco delle piazzole..  </small-->        
</div>






<div  name="conferma2" id="conferma2" class="form-group col-lg-3 ">
<!--input type="submit" name="submit" id=submit class="btn btn-info" value="Recupera dettagli piazzola"-->
<!--button type="submit" class="btn btn-info">
Recupera dettagli piazzola
</button-->
</div>



</div> <!-- fine row-->
</form>

<br>
<hr>


<?php
$id_piazzola=$_POST['piazzola'];
if (!$id_piazzola){
  $id_piazzola=$_GET['piazzola'];
}
$check_stato_intervento=0;

if ($id_piazzola){
?> 
<h1> Piazzola <?php echo $id_piazzola?> 
</h1>
<div id="refreshDataContainer">
<div id="dettagli_piazzola" class="row">
<?php
$query_piazzola="SELECT v.nome as via, p.numero_civico, p.foto, p.riferimento, p.note,
p.suolo_privato,
st_y(st_transform(p2.geoloc,4326)) as lat, 
st_x(st_transform(p2.geoloc,4326)) as lon
from elem.piazzole p 
join elem.aste a on a.id_asta = p.id_asta 
join topo.vie v on v.id_via = a.id_via 
join geo.piazzola  p2 on p2.id = p.id_piazzola 
where p.id_piazzola = $1";

$result_p = pg_prepare($conn, "my_query_p", $query_piazzola);
$result_p = pg_execute($conn, "my_query_p", array($id_piazzola));
$statusp= pg_result_status($result_p);

$check_foto=0;
?>


<form autocomplete="off" id="edit_piazzola" action="" onsubmit="return clickButton2();">
<input type="hidden" id="id_piazzola" name="id_piazzola" value=<?php echo $id_piazzola?>>
<div class="row g-3 align-items-center">
<?php
while($r_p = pg_fetch_assoc($result_p)) {
  $check_foto=$r_p['foto'];
  ?>
  <div class="form-group col-md-4">
      <label for="via"> Via </label>
      <input disabled="" type="text" name="via" id="via" class="form-control" value="<?php echo $r_p['via'];?>">
    </div>
    <div class="form-group col-md-1">
      <label for="via"> Civico </label>
      <input type="text" name="civ" id="civ" class="form-control" value="<?php echo $r_p['numero_civico'];?>">
    </div>
    <div class="form-group  col-md-6">
      <label for="rif"> Riferimento </label> <font color="red">*</font>
      <input type="text" name="rif" id="rif" class="form-control" value="<?php echo $r_p['riferimento'];?>" required="">
    </div>

    <div class="form-group  col-md-6">
      <label for="note"> Note </label>
      <input type="text" name="note" id="note" value="<?php echo $r_p['note'];?>" class="form-control" >
    </div>

    <div class="form-group col-md-2">
      <input class="form-check-input" type="checkbox" value="privato" name="privato" id="privato"
      <?php
      if ($r_p['suolo_privato']==1){
        echo ' checked=';
      }
      ?>
      >
      <label class="form-check-label" for="privato">
        Suolo privato
      </label>
    </div>   
    <div class="form-group  col-md-2">
      <button type="submit" class="btn btn-info"
    <?php if ($check_edit==0){echo 'disabled=""';}?>
    >
      <i class="fa-solid fa-pen-to-square"></i>Aggiorna piazzola
      </button>
    </div>
    <div class="form-group  col-md-2">
      <a id="sit_btn1" class="btn btn-info pc" href="<?php echo $url_sit?>/#!/home/edit-piazzola/<?php echo $id_piazzola?>/" target="_new">
    <i class="fa-solid fa-arrow-up-right-from-square"></i> Visualizza su SIT
    </a>
    </div>

<?php
}
?>
</form>
<hr>

<form autocomplete="off" id="messaggio" action="invio_mail_assterritorio.php" method="post">
<input type="hidden" id="id_piazzola" name="id_piazzola" value=<?php echo $id_piazzola?>>
<div class="row g-3 align-items-center">
<div class="form-group  col-md-10">
  <label for="testo_mail" class="form-label">Messaggio mail per assterritorio</label>
  <textarea class="form-control" id="testo_mail" name="testo_mail" rows="3"></textarea>
</div>
<div class="form-group  col-md-2">
      <button type="submit" class="btn btn-info">
      <i class="fa-solid fa-at"></i>Invia mail
      </button>
</div>
</div>
</form>
<hr>
</div>
</div>

<div class="row">
<div class="col-md-6"> 

<?php 
// controllo se la piazzola esiste o se è stata eliminata

$query_eliminata= "select 
case 
  when data_eliminazione is null then 0
  else 1
end eliminata, data_eliminazione
from elem.piazzole p where id_piazzola = $1";
$result_el = pg_prepare($conn, "my_query_el", $query_eliminata);
$result_el = pg_execute($conn, "my_query_el", array($id_piazzola));

while($r_el = pg_fetch_assoc($result_el)) {
  $check_eliminata=$r_el['eliminata'];
  $data = $r_el['data_eliminazione'];
}
if ($check_eliminata==1)
{
  ?>
  <div id="comp_piazz">
  <h3><i class="fa-solid fa-trash"></i> Piazzola eliminata il <?php echo $data; ?></h3>
  </div>
<?php
} else {
?>

<div id="comp_piazz">
<h4>Composizione attuale</h4>
<?php 

$query_elementi= "select e.id_elemento, 
te.tipo_rifiuto,
tr.nome as rifiuto,
te.tipologia_elemento,
tr.colore,
te2.descrizione as tipo_raccolta,
te.descrizione as tipo_elem, 
concat (ep.descrizione, ' - ', ep.nome_attivita) as cliente, 
string_agg(distinct vi.stato_descrizione, ',') as stato_intervento, 
max(vi.stato) as id_stato_intervento,
max(vi.odl) as odl,
case 
  when te.tipologia_elemento in ('L', 'P', 'C') and te.tipo_rifiuto in (1,3,4,5,7)
  then 1
  else 0
end da_rimuovere
from elem.elementi e
join elem.tipi_elemento te on te.tipo_elemento = e.tipo_elemento 
join elem.tipi_rifiuto tr on tr.tipo_rifiuto = te.tipo_rifiuto
join elem.tipologie_elemento te2 on te2.tipologia_elemento = te.tipologia_elemento
left join elem.elementi_privati ep on ep.id_elemento = e.id_elemento 
left join gestione_oggetti.v_intervento vi on e.id_elemento = vi.elemento_id and vi.stato in (1,5)
where id_piazzola = $1
group by e.id_elemento, 
te.tipo_rifiuto,
tr.nome,
te.tipologia_elemento,
tr.colore,
te2.descrizione ,
te.descrizione , 
ep.descrizione, ep.nome_attivita
order by tr.nome, te.descrizione";

$result_e = pg_prepare($conn, "my_query_e", $query_elementi);
$result_e = pg_execute($conn, "my_query_e", array($id_piazzola));
$status1= pg_result_status($result_e);
//echo $status1."<br>";
// recupero i dati dal DB di SIT
$check_bilaterale=0;
echo "<ul>";
while($r = pg_fetch_assoc($result_e)) {

    // descrizione elemento

    //print_r($r);
    if($r['tipologia_elemento']=='B'|| $r['tipologia_elemento']=='R' ){
      $check_bilaterale=1;
    }
    echo "<li>";
    if ($r['da_rimuovere']==1){
      echo '<b><span style="color: '.$r['colore'].';">
      <i class="fa-solid fa-eraser"  title="da rimuovere"></i>';
    } else {
      echo '<b><span style="color: '.$r['colore'].';">
      <i class="fa-solid fa-check" title="da lasciare"></i>';
    }
    echo $r['rifiuto'] .'</b></span> - ';
    echo $r['tipo_elem']. ' ('.$r['tipo_raccolta'].')';
    
    if (trim($r['cliente']) !='-'){
      echo ' - '. $r['cliente'];
    }
    if ($r['stato_intervento']!=''){
      echo '<b style="color:red"> Intervento '.$r["stato_intervento"].' ';
      if ($r["id_stato_intervento"]==5){
        $check_stato_intervento=1;
        echo '(Ordine di lavoro = '.$r["odl"].')';
      }
      echo '</b>';
    }
    $query_percorsi="select p.id_percorso, p.cod_percorso, p.descrizione, fo.descrizione_long, p.stagionalita 
    from elem.elementi_aste_percorso eap 
    join elem.aste_percorso ap on ap.id_asta_percorso = eap.id_asta_percorso 
    join elem.percorsi p on p.id_percorso = ap.id_percorso
    join etl.frequenze_ok fo on fo.cod_frequenza = p.frequenza 
    where id_elemento = $1 and p.id_categoria_uso in (3,6)";
    $result_p = pg_prepare($conn, "my_query_percorsi", $query_percorsi);
    $result_p = pg_execute($conn, "my_query_percorsi", array($r["id_elemento"]));
    $status1= pg_result_status($result_p);
    $check_percorsi=0;
    echo '<ul>';
    while($rp = pg_fetch_assoc($result_p)) {
      echo '<li> 
      <form class="form-inline"  method="post" autocomplete="off" action="rm_percorso.php" >
      <div class="form-row align-items-center">
      <i class="fa-solid fa-road"></i>'. $rp['cod_percorso']. ' - '.$rp['descrizione']. ' ('.$rp['descrizione_long'].')'; 
      
      $check_percorsi=1;
      ?>
      
        <input type="hidden" id="piazzola" name="piazzola" value="<?php echo $id_piazzola?>">
        <input type="hidden" id="elemento" name="elemento" value="<?php echo $r["id_elemento"]?>">
        <input type="hidden" id="percorso" name="percorso" value="<?php echo $rp["id_percorso"]?>">
        <button  type="submit" class="btn btn-danger btn-sm  mb-2">
        <i class="fa-solid fa-trash-can" title="Rimuovi elementi di tipo <?php echo $r['tipo_elem'];?> da percorso"></i>
        </button>
      </div>
      </form>
      
      </li>
    <?php
    }
    if ($check_percorsi==0){
      echo '<li><i class="fa-solid fa-road-circle-xmark" style="color:red" title="Nessun percorso associato"></i></li>';
    }
    
    // Qua devo fare un form 

    ?>
  
    <form class="form-inline" method="post" autocomplete="off" action="add_percorso.php" >
    <div class="row">
    <input type="hidden" id="piazzola1" name="piazzola" value="<?php echo $id_piazzola?>">
    <input type="hidden" id="elemento1" name="elemento" value="<?php echo $r["id_elemento"]?>">
    <div class="col-auto">
    <!--div class="form-group col-lg-6"-->
      <!--label for="via">Piazzola:</label> <font color="red">*</font-->
            
            
      <select class="selectpicker show-tick form-control" 
      data-live-search="true" name="percorso" id="percorso" required="">

      <option name="percorso" value="NO">Seleziona un percorso</option>
      <?php            
      $queryp2="select p.id_percorso, p.cod_percorso, p.descrizione  
      from elem.percorsi p 
      join elem.elementi_servizio es on es.id_servizio = p.id_servizio 
      join elem.elementi e on e.tipo_elemento = es.tipo_elemento 
      where p.id_categoria_uso in (3,6)
      and e.id_elemento = $1
      and p.id_percorso not in (select ap.id_percorso  
      from elem.elementi_aste_percorso eap 
      join elem.aste_percorso ap on ap.id_asta_percorso = eap.id_asta_percorso 
      join elem.percorsi p on p.id_percorso = ap.id_percorso
      where id_elemento = $1 and p.id_categoria_uso in (3,6))";
      $result_p2 = pg_prepare($conn, "my_query_percorsi2", $queryp2);
      $result_p2 = pg_execute($conn, "my_query_percorsi2", array($r["id_elemento"]));
      //echo $query1;    
      while($rp2 = pg_fetch_assoc($result_p2)) { 
          $valore=  $r2['id_via']. ";".$r2['nome'];            
      ?>
                  
              <option name="percorso" value="<?php echo $rp2['id_percorso'];?>" ><?php echo $rp2['cod_percorso'] .' - ' .$rp2['descrizione'];?></option>
      <?php } ?>

      </select>  
      <!--small>L'elenco delle piazzole..  </small-->        
    </div>





  
        <div class="col-auto" >
        <!--input type="submit" name="submit" id=submit class="btn btn-info" value="Recupera dettagli piazzola"-->
        <button type="submit" class="btn btn-info">
        Add</button>
        </div>


        </div>
      </form>


    <?php


    
    echo '</ul>';



    echo "</li>";
} // fine while
echo "</ul>";

?>


<hr>
<form autocomplete="off" id="prospects_form3" action="eliminazione.php" method="post">
  <input type="hidden" id="piazzola" name="piazzola" value="<?php echo $id_piazzola?>">
  <button id="delete_btn1" class="btn btn-danger pc" <?php if ($id_role_SIT<5){echo 'disabled=""';}?>> 
  <i class="fa-solid fa-trash" title="Elimina piazzola"></i>
   Elimina piazzola </button>
  </form>
  <?php if ($id_role_SIT<5){echo '<br>Modifica consentita solo a assterritorio. Per eliminare una piazzole utilizzare il form per l\'invio delle mail';}?>
</div>
<?php
}

?>

<div id="successo">
  <h3> Piazzola modificata</h3>
  <form autocomplete="off" id="prospects_form3" action="" method="post">
  <input type="hidden" id="piazzola" name="piazzola" value="<?php echo $id_piazzola?>">
  <button class="btn btn-warning" onClick="window.location.reload();"> <i class="fa-solid fa-arrow-rotate-right"></i> Ricarica piazzola</button>
  </form>
</div>

<hr>


<script type="text/javascript">
        
function clickButton() {
      console.log("Bottone form cliccato");


     
      var id_piazzola=document.getElementById('id_piazzola').value;
      console.log(id_piazzola);

      var indi=document.getElementById('indi').value;
      console.log(indi);
      //var indi_st=document.getElementById('indi_st').value;
      var indi_st=$("input[name='indi_st']:checked").val();
      console.log(indi_st);

      var carta=document.getElementById('carta').value;
      console.log(carta);
      //var carta_st=document.getElementById('carta_st').value;
      var carta_st=$("input[name='carta_st']:checked").val();
      console.log(carta_st);

      var multi=document.getElementById('multi').value;
      console.log(multi);
      //var multi_st=document.getElementById('multi_st').value;
      var multi_st=$("input[name='multi_st']:checked").val();
      console.log(multi_st);

      var org=document.getElementById('org').value;
      console.log(org);
      //var org_st=document.getElementById('org_st').value;
      var org_st=$("input[name='org_st']:checked").val();
      console.log(org_st);
  


      var http = new XMLHttpRequest();
      var url = 'mod_piazzola.php';
      var params = 'id_piazzola='+encodeURIComponent(id_piazzola)+'&indi='+encodeURIComponent(indi)+'&indi_st='+encodeURIComponent(indi_st)+'&carta='+encodeURIComponent(carta)+'&carta_st='+encodeURIComponent(carta_st)+'&multi='+encodeURIComponent(multi)+'&multi_st='+encodeURIComponent(multi_st)+'&org='+encodeURIComponent(org)+'&org_st='+encodeURIComponent(org_st)+'';
      http.open('POST', url, true);

      //Send the proper header information along with the request
      http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

      /*http.onreadystatechange = function() {//Call a function when the state changes.
          
          if (http.readyState === XMLHttpRequest.DONE) {
          const status = http.status;
          if (status === 0 || (status >= 200 && status < 400)) {
            // The request has been completed successfully
            console.log(http.responseText);
            $("#bilat").hide();
            $("#comp_piazz").load(location.href + " #comp_piazz");
            $("#successo").show();
            window.location.reload();
            return false;
          } else {
            console.log(http.responseText);
            alert("Problemi con le modifiche alle piazzole!");
            // Oh no! There has been an error with the request!
          }
  }

      }
      http.send(params);*/
      
    

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

      console.log(http.responseText);
      $("#bilat").hide();
      $("#comp_piazz").load(location.href + " #comp_piazz");
      $("#successo").show();
      window.location.reload(true);
      return false;

      

  }




</script>




<?php if ($check_bilaterale==0 and  $check_eliminata==0) {?>
<?php if ($check_edit==1){?>
<!--form autocomplete="off" id="bilat" action="" onsubmit="return clickButton();"-->
<form name="bilat" method="POST" autocomplete="off" action="mod_piazzola.php">

<input type="hidden" id="id_piazzola" name="id_piazzola" value=<?php echo $id_piazzola?>>

<div class="row g-3 align-items-center">
<div class="col-md-3">
  <label for="indi" class="form-label">Indifferenziato</label>
  </div>
  <div class="col-md-2">
  <input type="number" class="form-control" id="indi" name="indi" placeholder="" min=0 max=100 value=1>
  </div>

  <div class="col-md-7">
  <div class="form-check form-check-inline">
  <input class="form-check-input" type="radio" name="indi_st" id="1" value="1" checked>
  <label class="form-check-label" for="indi_st">Standard</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="radio" name="indi_st" id="0" value="0">
  <label class="form-check-label" for="indi_st">Ridotta</label>
</div>
</div>

</div>

<div class="row g-3 align-items-center">
<div class="col-md-3">
  <label for="carta" class="form-label">Carta</label>
  </div>
  <div class="col-md-2">
  <input type="number" class="form-control" id="carta" name="carta" placeholder="" min=0 max=100 value=1>
</div>

<div class="col-md-7">
  <div class="form-check form-check-inline">
  <input class="form-check-input" type="radio" name="carta_st" id="carta_st" value="1" checked>
  <label class="form-check-label" for="carta_st">Standard</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="radio" name="carta_st" id="carta_st" value="0">
  <label class="form-check-label" for="carta_st">Ridotta</label>
</div>
</div>
</div>

<div class="row g-3 align-items-center">
<div class="col-md-3">
  <label for="carta" class="form-label">Multimateriale</label>
  </div>
  <div class="col-md-2">
  <input type="number" class="form-control" id="multi" name="multi" placeholder="" min=0 max=100 value=1>
</div>
<div class="col-md-7">
  <div class="form-check form-check-inline">
  <input class="form-check-input" type="radio" name="multi_st" id="multi_st" value="1" checked>
  <label class="form-check-label" for="multi_st">Standard</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="radio" name="multi_st" id="multi_st" value="0">
  <label class="form-check-label" for="multi_st">Ridotta</label>
</div>
</div>
</div>

<div class="row g-3 align-items-center">
<div class="col-md-3">
  <label for="carta" class="form-label">Organico</label>
  </div>
  <div class="col-md-2">
  <input type="number" class="form-control" id="org" name="org" placeholder="" min=0 max=100 value=1>
</div>
<div class="col-md-7">
  <div class="form-check form-check-inline">
  <input class="form-check-input" type="radio" name="org_st" id="org_st" value="1" checked>
  <label class="form-check-label" for="org_st">Standard</label>
</div>
<div class="form-check form-check-inline">
  <input class="form-check-input" type="radio" name="org_st" id="org_st" value="0">
  <label class="form-check-label" for="org_st">Ridotta</label>
</div>
</div>
</div>
<br>
<div class="form-group  ">
<!--input type="submit" name="submit" id=submit class="btn btn-info" value="Trasforma a bilaterale"-->
<button type="submit" class="btn btn-info"
<?php if ($check_stato_intervento==1){?>
title="Non posso trasformare la piazzole perchè c'è un intervento preso in carico. Contattare manutenzione cassonetti" disabled=""
<?php }?>
<?php if ($check_edit==0){echo 'disabled=""';}?>
>
<i class="fa-solid fa-arrows-turn-to-dots"></i>Trasforma a bilaterale
</button>
<?php if ($check_stato_intervento==1){?>
<br><small>Non posso trasformare la piazzola perchè c'è un intervento preso in carico. Contattare manutenzione cassonetti</small>
<?php }?>
</div>

</form>
<?php }?>

<?php } else if ($check_eliminata==0){ echo "La piazzola è già bilaterale"; }?>

</div>



<div class="col-md-6"> 
<?php if ($check_foto == 1) {
//$timemod=filemtime('/foto_SIT/sit/'.$id_piazzola.'.jpg');
//echo 'FIle modificato il '.$timemod;
?>

<img src="../foto/sit/<?php echo $id_piazzola?>.jpg?hash=<?php echo filemtime('/foto_SIT/sit/'.$id_piazzola.'.jpg')?>" class="rounded img-fluid" alt="Immagine piazzola <?php echo $id_piazzola?> non presente">
<hr>
<?php }
?>
<?php if ($check_edit==1){?>
<form  action="upload_foto.php" method="post" enctype="multipart/form-data">
<!--form  action="" onsubmit="return clickButton2();" method="post" enctype="multipart/form-data"-->
<!--form id="form_foto" method="post" enctype="multipart/form-data"-->
<input type="hidden" id="piazzola" name="piazzola" value="<?php echo $id_piazzola?>">
<div class="mb-3">
  <label for="formFile" class="form-label">
  <?php if ($check_foto == 1) {
    echo 'Modifica immagine';
  } else {
    echo 'Aggiungi immagine:';
  }
  ?>
  </label>
  <input type="file" class="form-control form-control-sm" name="fileToUpload" id="fileToUpload" required="">
  </div>
  <div class="mb-3">
  <input type="submit" value="Carica foto" name="submit" class="btn btn-primary mb-3" >
  </div>
</form>
<?php }?>
</div>

</div>


<hr>



</div>

<?php
} #piazzola
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