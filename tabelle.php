<div class="card shadow mb-4 tabelle" style='display:none' id='card_AC'>
	<a href="#collapseCardtable_AC" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardTable_AC">
	  <h4 class="m-0 font-weight-bold text-primary">Tabella Macchine</h4>
	</a>
	<!-- Card Content - Collapse -->
	<div class="collapse show" id="collapseCardtable_AC">
	  <div class="card-body" id='tab_AC'>
		Gestione Tabella (modifica e cancellazione sono precluse)
		<div class='mt-2 mb-2'>
			<button class='btn btn-success btn-lg' type='button' id='btn-crea-autoclave'  onclick="$('#div_new_AC').show();$('#div_tab_AC').hide();$('#btn-crea-autoclave').hide()">Definisci Nuova Macchina</button>
		</div>
		<div id='div_new_AC' class='mt-3 container-fluid' style='display:none;border:1px dotted;padding:8px'>
			<h5>Definizione Nuova Macchina</h5>

			<form method='post' class='needs-validation_AC' id='form_AC' novalidate autocomplete="off">
			  <input type='hidden' name='sub_AC' id='sub_AC'>
			  <div class="form-group">
				<label for='stab_AC'>Stabilimento</label>
				<select class="form-control" name='stab_AC' id='stab_AC' required>
					<option value=''>Select...</option>
					<option value='S1'>S1</option>
					<option value='S2'>S2</option>
					<option value='S3'>S3</option>
				</select>
			  </div>
			  <div class="form-group">
				<label for='codice_AC'>Codice</label>
				<input type='text' class="form-control" aria-describedby="Codice" placeholder='Codice' id='codice_AC' name='codice_AC' maxlength=30 required>
				<small id="Codice" class="form-text text-muted">Massimo 30 caratteri</small>
			  </div>
			  
			  <div class="form-group">
				<label for='reparto_AC'>Reparto</label>
				<input type='text' class="form-control" aria-describedby="Reparto" placeholder='Reparto' id='reparto_AC' name='reparto_AC' maxlength=20 required>
				<small id="Reparto" class="form-text text-muted">Massimo 20 caratteri</small>
			  </div>

			  <div class="form-group">
				<label for='descr_reparto_AC'>Descrizione</label>
				<input type='text' class="form-control" aria-describedby="Descrizione_reparto" placeholder='Descrizione reparto' id='descr_reparto_AC' name='descr_reparto_AC' maxlength=200 required>
				<small id="Descrizione_reparto" class="form-text text-muted">Massimo 200 caratteri</small>
			  </div>

			  
			  <button type="submit" id='btn-crea_AC' class="btn btn-primary">Crea</button>
			  <button type="button" class="ml-3 btn btn-secondary" onclick="$('#div_new_AC').hide();$('#div_tab_AC').show();$('#btn-crea-autoclave').show()">Chiudi</button>
				<div id='div_wait_AC' class='ml-4 spinner-border' style='display:none' role='status'>
					<span class='sr-only'>Creazione in corso...</span>
				</div>

			</form>							
		</div>
		<?php 
		
			$view="";
			$view.="<div id='div_tab_AC'>";
				$view.="<table class='table table-hover'>";
					$view.="<thead>";
						$view.="<tr>";
							$view.="<th style='text-align:center' scope='col'>#</th>";
							$view.="<th scope='col'>Stabilimento</th>";
							$view.="<th scope='col'>Codice</th>";
							$view.="<th scope='col'>Reparto</th>";
							$view.="<th scope='col'>Descrizione</th>";
						$view.="</tr>";
					$view.="</thead>";
					$view.="<tbody>";
					for ($sca=0;$sca<=count($elenco_autoclavi)-1;$sca++) {
						$stabilimento=$elenco_autoclavi[$sca]['stabilimento'];
						$reparto=$elenco_autoclavi[$sca]['reparto'];
						$k=$elenco_autoclavi[$sca]['codice'];
						$key=$stabilimento.";".$reparto.";".$k;										
						
						$descrizione_reparto=stripslashes($elenco_autoclavi[$sca]['descrizione_reparto']);
						$view.="<tr>";
							$view.="<td style='text-align:center'><i>".($sca+1)."</i></td>";
							$view.="<td>";
								$view.="<font color='cornflowerblue'><b>".$stabilimento."</b></font>";
							$view.="</td>";
							$view.="<td>";
								$view.="<font color='blue'><b>".$k."</b></font>";
							$view.="</td>";
							$view.="<td>";
								$view.="<font color='cornflowerblue'><b>".$reparto."</b></font>";
							$view.="</td>";
							$view.="<td>";
								$view.="<font color='cornflowerblue'><b>".$descrizione_reparto."</b></font>";
							$view.="</td>";
							
						$view.="</tr>";
					}	
					$view.="</tbody>";
				$view.="</table>";	
			$view.="</div>";
			echo $view;
		?>	
	  </div>
	</div>
</div>	


<div class="card shadow mb-4 tabelle" style='display:none' id='card_Materiali'>
	<!-- Card Header - Accordion -->
	<a href="#collapseCardtable_M" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardTable_M">
	  <h4 class="m-0 font-weight-bold text-primary">Tabella Materiali</h4>
	</a>
	<!-- Card Content - Collapse -->
	<div class="collapse show" id="collapseCardtable_M">
	  <div class="card-body" id='tab_AC'>
		Gestione Tabella (modifica e cancellazione sono precluse)

		<div class='mt-2 mb-2'>
			<button class='btn btn-success btn-lg' type='button' id='btn-crea-materiale'  onclick="$('#div_new_M').show();$('#div_tab_M').hide();$('#btn-crea-materiale').hide()">Definisci Nuovo Materiale</button>
		</div>
		<div id='div_new_M' class='mt-3 container-fluid' style='display:none;border:1px dotted;padding:8px'>
			<h5>Definizione Nuovo Materiale</h5>

			<form method='post' class='needs-validation_M' id='form_M' novalidate autocomplete="off">
			  <input type='hidden' name='sub_M' id='sub_M'>

			  <div class="form-group">
				<label for='codice_AC'>Codice</label>
				<input type='text' class="form-control" aria-describedby="Codice_Materiale" placeholder='Codice' id='codice_M' name='codice_M' maxlength=20 required>
				<small id="Codice_Materiale" class="form-text text-muted">Massimo 20 caratteri</small>
			  </div>


			  <div class="form-group">
				<label for='descr_M'>Descrizione</label>
				<input type='text' class="form-control" aria-describedby="Descrizione_Materiale" placeholder='Descrizione' id='descr_M' name='descr_M' maxlength=200 required>
				<small id="Descrizione_Materiale" class="form-text text-muted">Massimo 200 caratteri</small>
			  </div>

			  
			  <button type="submit" id='btn-crea_M' class="btn btn-primary">Crea</button>
			  
			  <button type="button" class="ml-3 btn btn-secondary" onclick="$('#div_new_M').hide();$('#div_tab_M').show();$('#btn-crea-materiale').show()">Chiudi</button>
				<div id='div_wait_M' class='ml-4 spinner-border' style='display:none' role='status'>
					<span class='sr-only'>Creazione in corso...</span>
				</div>

			</form>							
		</div>
		<?php 
		
			$view="";
			$view.="<div id='div_tab_M'>";
				$view.="<table class='table table-hover'>";
					$view.="<thead>";
						$view.="<tr>";
							$view.="<th style='text-align:center' scope='col'>#</th>";
							$view.="<th scope='col'>Codice</th>";
							$view.="<th scope='col'>Descrizione</th>";
						$view.="</tr>";
					$view.="</thead>";
					$view.="<tbody>";
					for ($sca=0;$sca<=count($elenco_materiali)-1;$sca++) {
						$codice_M=$elenco_materiali[$sca]['codice'];
						$descrizione_M=stripslashes($elenco_materiali[$sca]['descrizione']);
						$view.="<tr>";
							$view.="<td style='text-align:center'><i>".($sca+1)."</i></td>";
							$view.="<td>";
								$view.="<font color='blue'><b>".$codice_M."</b></font>";
							$view.="</td>";
							$view.="<td>";
								$view.="<font color='cornflowerblue'><b>".$descrizione_M."</b></font>";
							$view.="</td>";
							
						$view.="</tr>";
					}	
					$view.="</tbody>";
				$view.="</table>";	
			$view.="</div>";
			echo $view;
		?>	
		
		
	  </div>
	</div>
</div>	




