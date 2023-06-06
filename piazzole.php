<?php
//session_set_cookie_params($lifetime);
session_start();

    
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="roberto" >

    <title>Bilaterale - Trasformazione piazzole</title>
<?php 
require_once('./req.php');

the_page_title();

if ($_SESSION['test']==1) {
  require_once ('./conn_test.php');
} else {
  require_once ('./conn.php');
}
?> 





</head>

<body>

<?php require_once('./navbar_up.php');
$name=dirname(__FILE__);
?>


<div class="container">


<form name="openfile" method="post" autocomplete="off" action="piazzola.php" >
<!--div class="row">
  <div class="col-md-6"> 
    <div class="form-group  ">
      <label for="via">Via:</label> <font color="red">*</font-->
      <!--select name="via-list" id="via-list" class="selectpicker show-tick form-control" 
      data-live-search="true" onChange="getCivico(this.value);" required=""-->
      <!--select name="via-list" id="via-list" class="selectpicker show-tick form-control" 
      data-live-search="true" onchange="writelist();" required="">

      <option value="">Seleziona la via</option>
      <?php            
      $query2="SELECT id_via, nome From topo.vie where id_comune=1;";
      $result2 = pg_query($conn, $query2);
      //echo $query1;    
      while($r2 = pg_fetch_assoc($result2)) { 
          $valore=  $r2['id_via']. ";".$r2['nome'];            
      ?>
                  
              <option name="codvia" value="<?php echo $r2['id_via'];?>" ><?php echo $r2['nome'];?></option>
      <?php } ?>

      </select>            
    </div>
  </div>
</div-->
<div class="row">
<div class="col-md-6"> 
    <div class="form-group  ">
      <label for="via">Piazzola:</label> <font color="red">*</font>
      <!--select name="via-list" id="via-list" class="selectpicker show-tick form-control" 
      data-live-search="true" onChange="getCivico(this.value);" required=""-->
      <select name="piazzola" id="piazzola" class="selectpicker show-tick form-control" 
      data-live-search="true" onchange="writelist();" required="">

      <option value="">Seleziona la piazzola</option>
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
    </div>
  </div>
</div>



<div class="row">

<div class="form-group  ">
<input type="submit" name="submit" id=submit class="btn btn-info" value="Recupera dettagli piazzola">
</div>
  </form>












</div>

<?php
require_once('req_bottom.php');
require('./footer.php');
?>





</body>

</html>