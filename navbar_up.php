<?php


// Faccio il controllo su SIT

$query_role='SELECT  su.id_user, sr.id_role, sr."name" as "role" FROM util.sys_users su
join util.sys_roles sr on sr.id_role = su.id_role  
where su."name" ilike $1;';
$result_n = pg_prepare($conn, "my_query_navbar1", $query_role);
$result_n = pg_execute($conn, "my_query_navbar1", array($_SESSION['username']));

$check_SIT=0;
while($r = pg_fetch_assoc($result_n)) {
  $role_SIT=$r['role'];
  $id_role_SIT=$r['id_role'];
  //$id_user_SIT=$r['id_user'];
  $_SESSION['id_user']=$r['id_user'];
  $check_SIT=1;
}



?>

<div class="banner"> <div id="banner-image"></div> 
<h3>  <a class="navbar-brand link-light" href="#">
    <img class="pull-left" src="img\amiu_small_white.png" alt="SIT" width="85px">
    <span>SIT - Passaggio a bilaterale</span> 
  </a> 
</h3>
</div>
<nav class="navbar navbar-inverse navbar-fixed-top navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <!--a class="navbar-brand" href="#">
    <img class="pull-left" src="img\amiu_small_white.png" alt="SIT" width="85px">
    </a-->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <ul class="navbar-nav ms-auto flex-nowrap">
        <!--li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li-->
        <?php if ($id_role_SIT > 0) { ?>
        <li class="nav-item">
          <a class="nav-link" href="./piazzole.php">Modifica piazzole</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./vie_percorsi.php">Vie - Percorsi</a>
        </li>
        <!--li class="nav-item">
          <a class="nav-link" href="./ordini.php"> Modifica percorsi</a>
        </li>
        
        <li class="nav-item">
          <a class="nav-link" href="./chiusura.php">Chiusura interventi</a>
        </li-->
        <?php } ?>
        
      </ul>
      <div class="collapse navbar-collapse flex-grow-1 text-right" id="myNavbar">
        <ul class="navbar-nav ms-auto flex-nowrap">
          <i class="fas fa-user"></i>Connesso come <?php echo $_SESSION['username'];?> (<?php echo $role_SIT;?>)
      </ul>

    </div>
  </div>
</nav>
