<?php session_start(); ?>
<?php //ini_set('display_errors', 1); ?>
<?php include("model/M_main.php"); ?>
<?php include("controller/C_main.php"); ?>
<?php include("view/view_main.php"); ?>
<?php
	if (!isset($_SESSION['user'])) {
		header("location: login.php");
		exit;
	}	
	$elenco_autoclavi=reparti_steril();
	$elenco_materiali=elenco_materiali();


?>
<!DOCTYPE html>


<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>ImpegnoLotti - Liofilchem</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
  <link href="css/user_over.css?ver=1.1" rel="stylesheet">
  
  

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-edit"></i>
        </div>
        <div class="sidebar-brand-text mx-3">IMPEGNOLOTTI</div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
      <div class="sidebar-heading">
        Creazione Lotti
      </div>

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item active">
        <a class="nav-link" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="far fa-file"></i>
          <span>Creazione Lotto</span>
        </a>
        <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Opzioni:</h6>
            <a class="collapse-item active" href="index.php">Nuovo Lotto</a>
            <a class="collapse-item active" href="index.php?steril=1">Lotto di sterilizzazione</a>
            <!-- <a class="collapse-item" href="index.php">Ultimi lotti creati</a> -->
          </div>
        </div>
      </li>


      <!-- Divider -->
      <hr class="sidebar-divider">

      <div class="sidebar-heading">
        Viste Principali
      </div>

      <!-- Nav Item - Tables -->
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0)" onclick="archivio()">
          <i class="fas fa-fw fa-table"></i>
          <span>Archivio Lotti</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="javascript:void(0)" onclick="archivio_steril(0)">
          <i class="fas fa-fw fa-table"></i>
          <span>Lotti di Sterilizzazione</span></a>
      </li>


      <!-- Nav Item - Charts -->
	  <!--
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fas fa-user-circle"></i>
          <span>Operazioni riservate</span></a>
      </li>
	  !-->


      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
	<?php 
		$admin_lotti=$_SESSION['admin_lotti'];
		if ($admin_lotti=="1") {?>	
			  <div class="sidebar-heading">
				Tabelle
			  </div>

			  <!-- Nav Item - Pages Collapse Menu -->
			  <li class="nav-item">
				<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages_tab" aria-expanded="true" aria-controls="collapsePages">
				  <i class="fas fa-fw fa-folder"></i>
				  <span>Gestione Tabelle</span>
				</a>
				<div id="collapsePages_tab" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
				  <div class="bg-white py-2 collapse-inner rounded">
					<h6 class="collapse-header">Tabelle disponibili:</h6>
					<a class="dropdown-item" href="#" onclick='tab_autoclavi()'>
					  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
					  Macchine
					</a>
					<div class="collapse-divider"></div>
					<a class="dropdown-item" href="#" onclick='tab_materiali()'>
					  <i class="fas fa-cogs fa-sm fa-sm fa-fw mr-2 text-gray-400"></i>
					  Materiali
					</a>
		
				  </div>
				</div>
			  </li>
			  <!-- Divider -->
			  <hr class="sidebar-divider">
		<?php } ?>	  


      <!-- Heading -->
      <div class="sidebar-heading">
        Sessione
      </div>

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
          <i class="fas fa-fw fa-folder"></i>
          <span>Sessione in corso</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Login Screens:</h6>
			<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
			  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
			  Logout
			</a>
            <div class="collapse-divider"></div>
			<?php if (1==2) {?>
				<h6 class="collapse-header">Other Pages:</h6>
				<a class="collapse-item" href="#">404 Page</a>
				<a class="collapse-item" href="#">Blank Page</a>
			<?php } ?>	
          </div>
        </div>
      </li>


      <!-- Divider -->
      <hr class="sidebar-divider d-none d-md-block">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">
		<input type='hidden' name='userID' id='userID' value="<?php echo $_SESSION['id_user']; ?>">
		<input type='hidden' name='adminLOTTI' id='adminLOTTI' value="<?php echo $_SESSION['admin_lotti']; ?>">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Search -->
          <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            
			<div class="input-group-append">

			<select class='custom-select ml-1' id='select_tipo_ric' onchange='cerca_per(this.value)'>
			  <option selected value='0'>Cerca per...</option>
			  <option value='1'>Lotto</option>
			  <option value='2'>Codice</option>
			  <option value='3'>Prodotto</option>
			  <option value='4'>Protocollo</option>
			  <option value='5'>Data liberazione</option>
			</select>

			<input type="text" class="form-control bg-light border-0 small" placeholder="Cerca lotto..." aria-label="Search" aria-describedby="basic-addon2" id='lotto_rapido'>
			<button class="btn btn-primary ml-1" type="button" onclick='cercalotto()'>
				<i class="fas fa-search fa-sm"></i>
			</button>

				
            </div>
			
			
          </form>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            <li class="nav-item dropdown no-arrow d-sm-none">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>



            <!-- Nav Item - Messages -->
			<?php if (1==2) {?>
				<li class="nav-item dropdown no-arrow mx-1">
				  <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fas fa-envelope fa-fw"></i>
					<!-- Counter - Messages -->
					<span class="badge badge-danger badge-counter">7</span>
				  </a>
				  <!-- Dropdown - Messages -->
				  <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
					<h6 class="dropdown-header">
					  Centro Messaggi
					</h6>
					<a class="dropdown-item d-flex align-items-center" href="#">
					  <div class="dropdown-list-image mr-3">
						<img class="rounded-circle" src="img/user_log.png" alt="">
						<div class="status-indicator bg-success"></div>
					  </div>
					  <div class="font-weight-bold">
						<div class="text-truncate">Impegnare il lotto xyz per il...</div>
						<div class="small text-gray-500">Reparto XY...</div>
					  </div>
					</a>
					<a class="dropdown-item d-flex align-items-center" href="#">
					  <div class="dropdown-list-image mr-3">
						<img class="rounded-circle" src="img/user_log.png" alt="">
						<div class="status-indicator"></div>
					  </div>
					  <div>
						<div class="text-truncate">Cancellare il lotto xyz</div>
						<div class="small text-gray-500">Utente JJJ</div>
					  </div>
					</a>
					<a class="dropdown-item d-flex align-items-center" href="#">
					  <div class="dropdown-list-image mr-3">
						<img class="rounded-circle" src="img/user_log.png" alt="">
						<div class="status-indicator bg-warning"></div>
					  </div>
					  <div>
						<div class="text-truncate">Attenzione! Il lotto....</div>
						<div class="small text-gray-500">Utente ZZ</div>
					  </div>
					</a>
					<a class="dropdown-item text-center small text-gray-500" href="#">Leggi altri messaggi...</a>
				  </div>
				</li>
			<?php } ?>	


			<div class="topbar-divider d-none d-sm-block"></div>

			<!-- Nav Item - User Information -->
			<li class="nav-item dropdown no-arrow">
			  <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION['operatore']; ?></span>
				<img class="img-profile rounded-circle" src="img/user_log.png">
			  </a>
			  <!-- Dropdown - User Information -->
			  <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
				<a class="dropdown-item" href="#">
				  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
				  Profilo
				</a>
				<a class="dropdown-item" href="#">
				  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
				  Impostazioni
				</a>
				<a class="dropdown-item" href="#">
				  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
				  Activity Log
				</a>
				<div class="dropdown-divider"></div>
				<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
				  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
				  Logout
				</a>
			  </div>
			</li>

          </ul>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <div id='div_stat_rapid' style='display:none'>
		  <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Riepilogo statistico</h1>
          </div>

          <div class="row">

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Lotti (del giorno)</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">100</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lotti (nel mese)</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">2.100</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
			
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
						<div class="text-xs font-weight-bold text-info text-uppercase mb-1">Lotti (anno in corso)</div>
						<div class="h5 mb-0 font-weight-bold text-gray-800">25.200</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>		
			


            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Lotti Annullati</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
		</div>

          <div class="row">

            

              <!-- Default Card Example -->
			<?php 
				$steril=$_GET['steril'];
				$descr_lotto="Creazione Nuovo Lotto";
				if ($steril=="1") {
					echo "<div class='col-lg-12'>";
					$descr_lotto="Impegno Lotto di Sterilizzazione";
				} else echo "<div class='col-lg-6'>";	
			?>
              <div class="card mb-4">
                <a href="#collapseCardLotto" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardLotto">
                  <h4 class="m-0 font-weight-bold text-primary"><?php echo $descr_lotto; ?></h4>
                </a>
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseCardLotto">				
				
					<div class="card-body">
					  Compilare i campi nel form sottostante.


						<div id='div_form' class='mt-3'>
							<?php 
							$steril=$_GET['steril'];
							if ($steril!="1") { ?>
								<form class="user" autocomplete="off">
									<div class="input-group mb-3">
									  <div class="input-group-prepend">
										<label class="input-group-text" for="tipo_prodotto">Tipo Prodotto</label>
									  </div>
									  <select class="custom-select" id="tipo_prodotto">
										<option value="0" selected>Select...</option>
										<option value="1">Semilavorato</option>
										<option value="2">Prodotto Finito</option>
									  </select>
									</div>							
									
									<div class="input-group mb-3">
									  <div class="input-group-prepend">
										<div class="input-group-text">
										  Data impegno
										</div>
										
										
									  </div>

									  <input type="date" class="form-control" id='date_next' placeholder="Data" aria-label="data" aria-describedby="data" value="<?php echo $date_next; ?>">
									</div>
									
									<div class="input-group mb-3">

									  <input type="text" class="form-control" id='codice' placeholder="Codice Prodotto (cerca per codice o descrizione)" aria-label="Codice" aria-describedby="codice_prodotto">
									  <div class="input-group-append">
									  
										<button class="btn btn-outline-secondary" type="button" id="button-addon2" onclick="cerca_codice()">Cerca</button>
									  </div>							  
									</div>

									
									<hr>

								</form>
							<?php } ?>
							<?php
							if ($steril=="1") { ?>
								<form class='needs-validation_lottos user' id='form_steril' novalidate autocomplete="off">
									<div class="input-group mb-3">
									  <div class="input-group-prepend">
										<div class="input-group-text">
										  Data impegno
										</div>
									  </div>
									  <input type="date" class="form-control " id='date_next' placeholder="Data" aria-label="data" aria-describedby="data" value="<?php echo $date_next; ?>" required>


									</div>
									
									<div class="input-group mb-3">

									  <div class="input-group-prepend">
										<label class="input-group-text" for="autoclave">Macchina</label>
									  </div>
										<select class="custom-select" id="autoclave" required>
											<option value="" selected>Select...</option>
											<?php
												for ($sca=0;$sca<=count($elenco_autoclavi)-1;$sca++) {
													$stabilimento=$elenco_autoclavi[$sca]['stabilimento'];
													$reparto=$elenco_autoclavi[$sca]['reparto'];
													$k=$elenco_autoclavi[$sca]['codice'];
													$key=$stabilimento.";".$reparto.";".$k;
													
													$descrizione_reparto=$elenco_autoclavi[$sca]['descrizione_reparto'];
													echo "<option value='$key'>$k</option>";
												}
											?>
									  </select>
									</div>
									
								
									<div class="input-group mb-3">
									  <div class="input-group-prepend">
										<label class="input-group-text" for="materiale">Materiale</label>
									  </div>
										<select class="custom-select" id="materiale" onchange='add_materiali()'>
											<option value="0" selected>Select...</option>
											<?php
												$v_m="<select class='custom-select' id='materiale_hide' style='display:none'>";
												$v_m.="<option value='0' selected>Select...</option>";
												for ($sca=0;$sca<=count($elenco_materiali)-1;$sca++) {
													$id_mat=$elenco_materiali[$sca]['id'];
													$codice=$elenco_materiali[$sca]['codice'];
													$descrizione=$elenco_materiali[$sca]['descrizione'];
													$dis="";
													
													echo "<option value='$id_mat'>$descrizione</option>";
													$v_m.="<option value='$id_mat'>$codice</option>";
												}
												$v_m.="</select>";
												
											?>
									  </select>
									  <?php echo $v_m; ?>
									</div>
									<div class='info_materiali' id='div_materiali' style='display:none;border:1px dotted;padding:5px;padding-top:0'>
									</div>
									<div class='info_materiali' id='div_ciclo_materiali' style='display:none;border:1px dotted;padding:5px;margin-top:10px'>										
									</div>
								</form>
							<?php } ?>
							
						</div>
					  
					  
					</div>
				</div>
              </div>



            </div>
			<?php if ($steril!="1") {?>
				<div class="col-lg-6">

				  <!-- Collapsable Card Example -->
				  <div class="card shadow mb-4">
					<!-- Card Header - Accordion -->
					<a href="#collapseCardInfo" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardInfo">
					  <h4 class="m-0 font-weight-bold text-primary">Informazioni</h4>
					</a>
					<!-- Card Content - Collapse -->
					<div class="collapse show" id="collapseCardInfo">
					  <div class="card-body">
						  Per creare un nuovo lotto compilare i campi in successione.<br>
						  Tutte le operazioni saranno tracciate con possibilità di analisi da parte di un Administrator.<br><br>
						  In questa finestra sono visibili tutti i messaggi che richiamano l'attenzione da parte dell'utente in caso di avvisi, errori, informazioni sulle operazioni svolte.
					  </div>
					</div>
				  </div>




				</div>
			<?php } ?>
          </div>

              <!-- Collapsable Card Example -->
              <div class="card shadow mb-4">
                <!-- Card Header - Accordion -->
                <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardExample">
                  <h4 class="m-0 font-weight-bold text-primary">Area operazioni sui lotti prenotati</h4>
                </a>
							
				<nav class="navbar navbar-expand-lg navbar-light bg-light" style='display:none' id='nav_steril'>
				  
				  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				  </button>

				  <div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav mr-auto">
					  <li class="nav-item active">
						<a class="nav-link" href="javascript:void(0)">Filtri <span class="sr-only">(current)</span></a>
					  </li>

						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							  N° Max record
							</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdown">
								
								<?php 
								for ($sca=1;$sca<=7;$sca++) {
									if ($sca==1) {$value=10;$desc="10";}
									if ($sca==2) {$value=20;$desc="20";}
									if ($sca==3) {$value=50;$desc="50";}
									if ($sca==4) {$value=100;$desc="100";}
									if ($sca==5) {$value=200;$desc="200";}
									if ($sca==6) {$value=500;$desc="500";}
									if ($sca==7) {$value=1;$desc="Tutti";}
									$js="$('.dropMax').removeClass('active');";
									$js.="$(this).addClass('active');";
									$js.="$('#n_rec_inpage').val($value);";
									$js.="archivio_steril(0);";
									$active="";
									if ($sca==3) $active="active";
									echo "<a class='dropdown-item dropMax $active' href='javascript:void(0)' onclick=\"$js\">$desc</a>";
								
								}
								?>
					
							</div>
						</li>	
						<input type='hidden' name='n_rec_inpage' id='n_rec_inpage' value='50'>



						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							  Stato schede
							</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdown">
								<?php 
									$js="";
									$js.="$('.dropStato').removeClass('active');";
									$js.="$(this).addClass('active');";
									

									$jsx=$js."$('#stato_scheda').val('100');archivio_steril(0);";
									echo "<a class='dropdown-item dropStato active' href='javascript:void(0)' onclick=\"$jsx\">Tutte</a>";
									
									$jsx=$js."$('#stato_scheda').val('0');archivio_steril(0);";
									echo "<a class='dropdown-item dropStato' href='javascript:void(0)' onclick=\"$jsx\">Solo Schede aperte</a>";;
									
									$jsx=$js."$('#stato_scheda').val('1');archivio_steril(0);";
									echo "<a class='dropdown-item dropStato' href='javascript:void(0)' onclick=\"$jsx\">Solo schede chiuse</a>";
									
								?>
							</div>
						</li>	
						<input type='hidden' name='stato_scheda' id='stato_scheda' value='100'>


						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							  Macchina
							</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdown">
								<?php 
									$js="";
									$js.="$('.dropAutoclave').removeClass('active');";
									$js.="$(this).addClass('active');";
									
									$jsx=$js."$('#filtro_autoclave').val('0');archivio_steril(0);";
									
									echo "<a class='dropdown-item dropAutoclave active' href='javascript:void(0)' onclick=\"$jsx\">Tutte</a>";
									for ($sca=0;$sca<=count($elenco_autoclavi)-1;$sca++) {
										$stabilimento=$elenco_autoclavi[$sca]['stabilimento'];
										$reparto=$elenco_autoclavi[$sca]['reparto'];
										$k=$elenco_autoclavi[$sca]['codice'];
										$key=$stabilimento.";".$reparto.";".$k;
										
										$descrizione_reparto=$elenco_autoclavi[$sca]['descrizione_reparto'];
										
										$jsx=$js."$('#filtro_autoclave').val('$key');archivio_steril(0);";
										
										echo "<a class='dropdown-item dropAutoclave' href='javascript:void(0)' onclick=\"$jsx\">$k</a>";
									}
									
								?>
							</div>
						</li>	
						<input type='hidden' name='filtro_autoclave' id='filtro_autoclave' value='0'>

						
						<input type='date' class='form-control' id='da_data_s' aria-label='da data' aria-describedby='da data'>
						
						<input type='date' class='ml-2 form-control' id='a_data_s' aria-label='a data' aria-describedby='a data'>

					   <a href='javascript:void(0)' onclick="archivio_steril(0)"><button class="ml-2 btn btn-outline-success my-2 my-sm-0" id='button-cerca_date'>Cerca</button></a>
					</ul>
					
				  </div>
				</nav>				
				
				
                <!-- Card Content - Collapse -->
                <div class="collapse show" id="collapseCardExample">
                  <div class="card-body" id='stampa_lotti'>
                    Dopo aver creato il lotto (o famiglia di lotti), in questa area saranno disponibili le etichette pronte da stampare.
                  </div>
                </div>

              </div>
			  
			  <?php include("tabelle.php"); ?>
			  
        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->
      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Jolly Computer <?php echo date("Y");?></span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  
  
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Sicuri di uscire?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body" id='div_logout'>
			Selezionare "Logout" prima di chiudere la sessione di lavoro.

		</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Annulla</button>
          <a class="btn btn-primary" href="login.php?logout=1">Logout</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modals dynamic-->
	<div class="modal fade bd-example-modal-xl" id="modal_resp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" id='modal_resp_head'>
		<div id='resp_body'>
		</div>
    </div>
  </div>
  <div id='div_modal_generic'></div>
 <!-- -->
  


  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="vendor/jquery-easing/jquery.form.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>


  
  <script src="js/index.js?ver=7.525"></script>
  <script src="js/steril.js?ver=2.4"></script>
  <script src="js/tabelle.js?ver=1.6"></script>

</body>

</html>
