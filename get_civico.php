<?php
session_start();
//require('../validate_input.php');;



//echo $_POST["cod"];

if ($_SESSION['test']==1) {
    require_once ('./conn_test.php');
  } else {
    require_once ('./conn.php');
  }


if(!empty($_POST["cod"])) {
    //echo "Sono qua <br>";
    $query = "SELECT cod_civico, testo,
    st_y(st_centroid(st_transform(cc.geoloc, 4326))) as lat,
    st_x(st_centroid(st_transform(cc.geoloc, 4326))) as lon 
    FROM etl.civici_comune cc 
    where cod_strada = $1 
    ORDER BY 1;";
    #echo $query;
    $result = pg_prepare($conn, "myqueryciv", $query);
    $result = pg_execute($conn, "myqueryciv", array($_POST["cod"]));
    ?>
    <option name="id_civico" value="">Seleziona il civico</option>
    <?php
    while($r = pg_fetch_assoc($result)) { 
    ?>
        <option name="id_civico" value="<?php echo $r['cod_civico'].'|'.$r['lat'].'|'.$r['lon'];?>"><?php echo $r['testo'];?></option>

    <?php
    } 
} 
?>