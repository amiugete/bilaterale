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



    <title>Bilaterale - Nuova piazzola </title>
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
?>


<div class="container">
<hr>
<h3>Nuova piazzola bilaterale </h3>
<hr>
<div  id="nuova" class="row">







<div class="col-md-6">
  


<form name="bilat" method="post" autocomplete="off" action="nuova_piazzola2.php">
<!--form autocomplete="off" id="bilat" action="" onsubmit="return clickButton();"-->

<script>
 
function getCivico(val) {

  valArr = val.split('|');
  console.log(valArr);
    
  // Accessing individual values
  cod_via=valArr[0];
  console.log(cod_via);

  lat=valArr[1];
  lon=valArr[2];

  var zoom = 18;
  console.log(lat);
  console.log(lon);
  

  console.log('Sono qua');

// add a marker
//var marker = L.marker([lat, lon],{}).addTo(mymap);
// set the view
mymap.setView([lat, lon], zoom);

  $.ajax({
    type: "POST",
    url: "get_civico.php",
    data:'cod='+cod_via,
    success: function(data){
      //alert('Sono qua');
      $("#civico-list").empty();
    $("#civico-list").html(data);
    //$('#civico-list option:selected').remove();
    $('#civico-list').selectpicker('refresh');
      //alert('Nel mezzo non funge');
    }
  });


}




function getZoom(val) {

valArr = val.split('|');
console.log(valArr);
  
// Accessing individual values
cod_civico=valArr[0];
console.log(cod_civico);

lat=valArr[1];
lon=valArr[2];

var zoom = 18;
console.log(lat);
console.log(lon);


console.log('Sono qua 2');


document.getElementById('lat').value = lat.toString();
			 document.getElementById('lon').value = lon.toString();
		
  /*popup
  .setLatLng(e.latlng)
  .setContent("Le coordinate di questo punto sulla mappa sono le seguenti lat:" + e.latlng.lat.toString() +" e lon:"+ e.latlng.lng.toString() +" e sono state automaticamente inserite nel form")
  .openOn(mymap);*/
    
    popup
    .setLatLng([lat, lon])
    .setContent("La piazzola è stata posizionata in corrispondenza del civivo. Clicca su un altro punto sulla mappa per posizionare correttamente la piazzola")
    .openOn(mymap);
    
    
    //var latlng = e.value.split(',');
  //alert(latlng);

  setTimeout(function() {
      mymap.closePopup();
    }, 5000);
  // add a marker
  if (marker) { // check
      mymap.removeLayer(marker); // remove
    }

// add a marker
marker = L.marker([lat, lon],{}).addTo(mymap);
// set the view
mymap.setView([lat, lon], zoom);




}
</script>

<style> 
  /* Faccio andare sopra alla mappa il select*/ 
  .dropdown-menu {
      z-index: 2000;
    }
  </style>

<div class="row g-3 align-items-center">
  <div class="form-group  col-md-6">
  <label for="via">Via:</label> <font color="red">*</font>
                <select name="codvia" id="via-list" class="selectpicker show-tick form-control" data-live-search="true" 
                onChange="getCivico(this.value);" required="">
                <option name="codvia" value="">Seleziona la via</option>
<?php            
$query2="select v.id_via, v.nome, c.descr_comune as comune , 
st_y(st_centroid(st_transform(mnv.geom, 4326))) as lat,
st_x(st_centroid(st_transform(mnv.geom, 4326))) as lon
from topo.vie v 
join topo.comuni c on v.id_comune = c.id_comune
join geo.mv_nomi_via mnv on mnv.id_via = v.id_via 
order by v.nome;";
$result2 = pg_query($conn, $query2);
//echo $query1;    
while($r2 = pg_fetch_assoc($result2)) { 
    //$valore=  $r2['id_via']. ";".$r2['desvia'];            
?>
            
        <option name="codvia" value="<?php echo $r2['id_via'].'|'.$r2['lat'].'|'.$r2['lon'];?>" ><?php echo $r2['nome'].' ('.$r2['comune'].')';?></option>
  <?php } ?>

  </select>            
  </div>


<div class="form-group  col-md-6">
  <label for="id_civico">Civico:</label>
    <!--select class="selectpicker show-tick form-control" name="id_civico" id="civico-list"  data-live-search="true" -->
    <select class="selectpicker show-tick form-control" name="id_civico" id="civico-list" 
    onChange="getZoom(this.value);">
      <!--option name="id_civico" value="">Seleziona il civico</option-->
    </select>         
  </div>

</div>


<div class="row g-3 align-items-center">
    <div id="mapid" style="width: 100%; height: 600px;"></div>
</div>


<div class="row g-3 align-items-center">
<div class="form-group col-md-6">
  <label for="lat"> Latitudine </label> <font color="red">*</font>
  <input readonly="" type="text" name="lat" id="lat" class="form-control" required="">
</div>

<div class="form-group col-md-6">
  <label for="lon"> Longitudine </label> <font color="red">*</font>
  <input readonly="" type="text" name="lon" id="lon" class="form-control" required="">
</div>
</div>
<hr>


</div><!-- chiudo col -->



<div class="col-md-6"> 

<div class="form-group">
  <label for="rif"> Riferimento </label> <font color="red">*</font>
  <input type="text" name="rif" id="rif" class="form-control" required="">
</div>

<div class="form-group">
  <label for="note"> Note </label>
  <input type="text" name="note" id="note" class="form-control" >
</div>

<div class="form-check">
  <input class="form-check-input" type="checkbox" value="privato" name="privato" id="privato">
  <label class="form-check-label" for="privato">
    Suolo privato
  </label>
</div>


<hr>

<div class="row g-3 align-items-center">
<h4>Composizione piazzola bilaterale </h4>
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
<hr>
<div class="row g-3 align-items-center">
<button type="submit" class="btn btn-info">
<i class="fa-solid fa-plus"></i> Crea piazzola
</button>
</div>

</div> <!-- chiudo row -->


<hr>



</div>

<?php
require_once('req_bottom.php');
require_once('./mappa_georef.php');
require_once('./footer.php');
?>



<script type="text/javascript">

// questo non fa funzionare il select cascade
/*$(function () {
	$('select').selectpicker();
});*/

</script>



</body>

</html>