<?php
//ini_set('display_errors', 1);
//ini_set('log_errors', 1);
//ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
//error_reporting(E_ALL);
	set_time_limit(300);

					  require('pdf/fpdf.php');
					  class PDF extends FPDF{
  								var $widths;
								var $aligns;
								var $tipo_vis;
								var $data_ric;
								
								//Page header
									function Header(){
										$operatore=$_SESSION['operatore'];
										$id_user=$_SESSION['id_user'];
										$data_ric=$this->Get_dataric();
										$tipo_vis=$this->Get_tipovis();	////statistiche x incarichi
										$this->SetFont('Times','B',8);				
										$testo="Operatore: $id_user-$operatore";
										$this->Cell(70,4,$testo,0,1,'L');
										$this->SetFont('Times','B',20);				

										$this->Cell(70,15,'Liofilchem S.r.l.',1,0,'C');
										$this->SetFont('Times','B',10);				

										$this->Cell(140,15,'DOCUMENTI DI REGISTRAZIONE',1,0,'C');
										$x=$this->GetX();
										$y=$this->GetY();						
												
										if ($tipo_vis=='1') $this->MultiCell(70,5,'MOD 91                                                               Revisione 4 del 19.12.2012                                            ',1,'L');
										if ($tipo_vis=='2') $this->MultiCell(70,5,'MOD 96                                                               Revisione 4 del 27.11.2013                                            ',1,'L');
										$this->SetFont('Times','B',12);		
										$this->Ln(4);
										if ($tipo_vis=='1') $testo="CARICO SEMILAVORATO";
										if ($tipo_vis=='2') $testo="CARICO PRODOTTO FINITO";

										$this->Cell(280,7,$testo,0,1,'C');


										
										$this->Ln(2);
										$testo="DATA PRODUZIONE $data_ric";
										$this->Cell(200,7,$testo,0,0,'L');

										$testo="NUMERO DI CARICO";
										$this->Cell(50,10,$testo,0,1,'L');

										$this->SetFont('Times','B',8);		
										$testo="LOTTO";
										$this->Cell(30,13,$testo,1,0,'C');
										$testo="CODICE";
										$this->Cell(30,13,$testo,1,0,'C');
										$testo="PRODOTTO";
										if ($tipo_vis=='1') $wi=75; else $wi=119;								
										$this->Cell($wi,13,$testo,1,0,'C');
										$testo="QUANTITA'";
										if ($tipo_vis=='2') $wi=20; else $wi=30;																				
										$this->Cell($wi,13,$testo,1,0,'C');
										
										if ($tipo_vis=='2') {
											$testo="COD. ALT.";
											$this->Cell(15,13,$testo,1,0,'C');										
										}
										$testo="C.Q.";
										$this->Cell(15,13,$testo,1,0,'C');										

										$testo="PROTOCOLLO";
										if ($tipo_vis=='2') $wi=35; else $wi=40;
										$this->Cell($wi,13,$testo,1,0,'C');
										
										if ($tipo_vis=='1') {
											$testo="SCADENZA";
											$this->Cell(22,13,$testo,1,0,'C');
											$testo="LIBERAZIONE";
											$this->Cell(22,13,$testo,1,0,'C');
										}

										$testo="CARICATO";
										$this->Cell(16,13,$testo,1,1,'C');

									}

									//footer
									function Footer(){
										$this->SetFont('Arial','I',8);
										
										$this->SetY(-20);										
										$this->Cell(0,10,'____________________________________________________________________________',0,0,'C');
										
										$this->SetY(-15);										
										$this->Cell(0,10,'Questo documento è di proprietà della Liofilchem S.r.l. che se ne riserva tutti i diritti',0,0,'C');
										
										//1.5 cm dal basso												
										$this->SetY(-10);
										//Arial italic 8
										$this->SetFont('Arial','I',8);
										//numero pagina
										$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	
									}
									
									function Set_ini($tipo,$data)
									{									
										$this->tipo_vis=$tipo;
										$this->data_ric=$data;
									}
									function Get_dataric()
									{									
										return $this->data_ric;										
									}
									function Get_tipovis()
									{									
										return $this->tipo_vis;										
									}
									
									function SetWidths($w)
									{
										//Set the array of column widths
										$this->widths=$w;
									}

									function SetAligns($a)
									{
										//Set the array of column alignments
										$this->aligns=$a;
									}

									function Row($data)
									{
										//Calculate the height of the row
										$nb=0;
										for($i=0;$i<count($data);$i++)
											$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
										$h=5*$nb;
										//Issue a page break first if needed
										$this->CheckPageBreak($h);
										//Draw the cells of the row
										for($i=0;$i<count($data);$i++)
										{
											$w=$this->widths[$i];
											$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
											//Save the current position
											$x=$this->GetX();
											$y=$this->GetY();
											//Draw the border
											$this->Rect($x,$y,$w,$h);
											//Print the text
											$this->MultiCell($w,5,$data[$i],0,$a);
											//Put the position to the right of the cell
											$this->SetXY($x+$w,$y);
										}
										//Go to the next line
										$this->Ln($h);
									}

									function CheckPageBreak($h)
									{
										//If the height h would cause an overflow, add a new page immediately
										    if($this->GetY()+$h>$this->PageBreakTrigger)
											{$this->AddPage($this->CurOrientation);
											$this->SetX(20);}
									}

									function NbLines($w,$txt)
									{
										//Computes the number of lines a MultiCell of width w will take
										$cw=&$this->CurrentFont['cw'];
										if($w==0)
											$w=$this->w-$this->rMargin-$this->x;
										$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
										$s=str_replace("\r",'',$txt);
										$nb=strlen($s);
										if($nb>0 and $s[$nb-1]=="\n")
											$nb--;
										$sep=-1;
										$i=0;
										$j=0;
										$l=0;
										$nl=1;
										while($i<$nb)
										{
											$c=$s[$i];
											if($c=="\n")
											{
												$i++;
												$sep=-1;
												$j=$i;
												$l=0;
												$nl++;
												continue;
											}
											if($c==' ')
												$sep=$i;
											$l+=$cw[$c];
											if($l>$wmax)
											{
												if($sep==-1)
												{
													if($i==$j)
														$i++;
												}
												else
													$i=$sep+1;
												$sep=-1;
												$j=$i;
												$l=0;
												$nl++;
											}
											else
												$i++;
										}
										return $nl;
									}	

						}	

$tipo_vis=$tipo;						
$pdf = new PDF('L', 'mm');

$pdf->Set_ini($tipo,$da_data);

$pdf->SetTextColor(0,0,0);  ////imposta testo nero
$pdf->SetFont('Arial','B',10);							
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetAuthor('Liofilchem');
$pdf->SetTitle('Carico');

$pdf->SetFillColor(0,0,0);
$pdf->SetFont('Times','B',10);	

while($res = $result->fetch_array()){

	$results = array_map('utf8_encode', $res);	


	$pdf->SetTextColor(0,0,0);
	$tipo=stripslashes($results['DBtipo']);
	$lotto=stripslashes($results['DBlotto']);
	$codice=stripslashes($results['DBcodice']);
	$prodotto=stripslashes($results['DBprodotto']);
	$quantita=stripslashes($results['DBquantita']);
	$controllo=stripslashes($results['DBcontrollo']);
	$cq_pf=stripslashes($results['DBcq_pf']);
	$protocollo=stripslashes($results['DBprot']);
	$verifica=stripslashes($results['DBverifica']);
	$scadenza=data_it($results['DBscadenza']);
	$disp=data_it($results['DBdisp']);
	if ($scadenza=='01-01-1980' || $scadenza =="--" || strlen($scadenza)!=10) $scadenza="";
	if ($disp=='01-01-1980' || $disp =="--" || strlen($disp)!=10) $disp="";
	$pdf->Cell(30,6,$lotto,1,0,'C');	
	$pdf->Cell(30,6,$codice,1,0,'L');	
	$prodotto=substr($prodotto,0,40);
	if ($controllo!='!') {		
		$pdf->SetFont('Times','B',8);	
		if ($tipo_vis=='1') $wi=75; else $wi=119;								
		$pdf->Cell($wi,6,$prodotto,1,0,'L');	
		$pdf->SetFont('Times','B',10);	
		if ($tipo_vis=='2') $wi=20; else $wi=30;
		$pdf->Cell($wi,6,$quantita,1,0,'L');	
		$pdf->Cell(15,6,$controllo,1,0,'L');	
		if ($tipo_vis=='2') $pdf->Cell(15,6,$cq_pf,1,0,'L');	
		if ($tipo_vis=='2') $wi=35; else $wi=40;

		$pdf->Cell($wi,6,$protocollo,1,0,'L');	
		if ($tipo_vis=='1') {
			$pdf->Cell(22,6,$scadenza,1,0,'L');
			$pdf->Cell(22,6,$disp,1,0,'L');
		}
		$pdf->Cell(16,6,$verifica,1,1,'L');
		
	}
	else {
		$pdf->SetTextColor(255,69,0);
		$pdf->Cell(220,6,"-  -  -   E L E M E N T O  R I M O S S O   -  -  -  ",1,1,'C');	
	}	
		
	
}
$pdf->SetFont('Arial','',9);	
$pdf->SetTextColor(0,0,0);
set_time_limit(30);

@unlink('stampa.pdf');
$HTTP_USER_AGENT=$_SERVER['HTTP_USER_AGENT'];
if(strstr($HTTP_USER_AGENT,"MSIE")) $pdf->Output("stampa.pdf","F");
else $pdf->Output("stampa.pdf","F");

function data_it($data)
{
  $array = explode("-", $data); 
  $data_it = $array[2]."-".$array[1]."-".$array[0]; 
  if ($data_it=='00-00-0000') $data_it='';
  return $data_it; 
}	

function dataform($data,$tipo){
	$dataform="0000-00-00"; 
	if ($tipo=="en") {		
		if (strlen($data)!=10) return $dataform;
		if (substr($data,2,1)!="/" && substr($data,2,1)!="-") return $dataform;
		if (substr($data,5,1)!="/" && substr($data,5,1)!="-") return $dataform;
	}	
	if ($tipo=="en"){
		$da=substr($data,0,2);
		$mo=substr($data,3,2);
		$ye=substr($data,6,4);
		$dataform=$ye."-".$mo."-".$da; 
	}
	
 return $dataform;
} 

?>