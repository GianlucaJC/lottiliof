<?php
//ini_set('display_errors', 1);
//ini_set('log_errors', 1);
//ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
//error_reporting(E_ALL);
	$obj_u=$_POST['obj_u'];
	$obj_u = json_decode($obj_u);

	$obj_m=$_POST['obj_m'];
	$obj_m = json_decode($obj_m);

	//echo $obj_u->{'lotto'};

	set_time_limit(300);

					  require('pdf/fpdf.php');
					  class PDF extends FPDF{
  								var $widths;
								var $aligns;

								
								//Page header
									function Header(){
										$this->SetFont('Times','B',26);
										$txt="Liofilchem®";
										$this->Cell(70,15,$txt,1,0,'C');
										$this->SetFont('Times','B',10);				

										$x=$this->GetX();
										$y=$this->GetY();
										$this->MultiCell(50,5,'SCHEDA DI STERILIZZAZIONE MATERIALI',1,'C');
										
										$this->SetXY($x+50,$y);
										$x=$x+50;
										$this->MultiCell(50,5,'MOD 29                                   Rev. 4 del 14.03.2018             Pag '.$this->PageNo().'/{nb}',1,'L');
										
										$this->SetXY($x+50,$y);
										$this->SetFont('Times','B',12);		
										$testo="SOP 31";
										$this->Cell(20,15,$testo,1,1,'C');

									}

									//footer
									function Footer(){
										$this->SetFont('Arial','I',8);
										
										$this->SetY(-20);										
										$this->Cell(0,10,'____________________________________________________________________________',0,0,'C');
										
										$this->SetY(-15);										
										$this->Cell(0,10,'Questo documento è di proprietà della Liofilchem S.r.l. che se ne riserva tutti i diritti',0,0,'C');
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

				
$pdf = new PDF('P', 'mm');

$pdf->SetTextColor(0,0,0);  ////imposta testo nero
$pdf->SetFont('Arial','B',10);							
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetAuthor('Liofilchem');
$pdf->SetTitle('Mod29');

$pdf->Ln(5);
$pdf->SetFillColor(0,0,0);
$pdf->SetFont('Times','B',10);
$testo="DATA: ".$obj_u->{'date_next'};
$pdf->Cell(50,6,$testo,0,0,'L');	

$pdf->Ln(10);
$pdf->SetFont('Times','',13);
$testo="LOTTO DI STERILIZZAZIONE: ";
$pdf->Cell(65,6,$testo,0,0,'L');	

$pdf->SetFont('Times','B',13);
$testo=$obj_u->{'lotto'};
$pdf->Cell(110,6,$testo,0,1,'L');	


$pdf->Ln(10);
$pdf->SetFont('Times','B',13);
$testo="MATERIALE STERILIZZATO";
$pdf->Cell(70,15,$testo,1,0,'C');


$testo="QUANTITA'";
$pdf->Cell(50,15,$testo,1,0,'C');


$testo="LOTTO";
$pdf->Cell(70,15,$testo,1,1,'C');

$pdf->SetFont('Times','B',11);
for ($sca=0;$sca<=count($obj_m->{'codice_materiale'})-1;$sca++) {
	$testo=$obj_m->{'descrizione'}[$sca];
	$pdf->Cell(70,8,$testo,1,0,'C');
	$testo=$obj_m->{'qta'}[$sca];
	$pdf->Cell(50,8,$testo,1,0,'C');
	$testo=$obj_m->{'lotto_materiale'}[$sca];
	$pdf->Cell(70,8,$testo,1,1,'C');
}
$pdf->SetFont('Times','',10);
$testo="N.B. Per la sterilizzazione in autoclave inserire almeno una striscia di nastro indicatore (ref. P11667) prima di avviare il ciclo";
$pdf->Cell(190,15,$testo,0,1,'L');

$pdf->Ln(3);
$pdf->SetFont('Times','B',13);
$testo="CODICE MACCHINA:";
$pdf->Cell(50,10,$testo,0,0,'L');

$pdf->SetFont('Times','B',13);
$testo=$obj_u->{'autoclave'};
$pdf->Cell(70,10,$testo,0,1,'L');

$pdf->SetFont('Times','B',13);
$testo="CICLO IMPOSTATO";
$pdf->Cell(70,10,$testo,0,0,'L');

$pdf->SetFont('Times','',13);
$testo="Tempo:";
$pdf->Cell(15,10,$testo,0,0,'L');

$pdf->SetFont('Times','B',13);
$testo=$obj_u->{'tempo'};
$pdf->Cell(40,10,$testo,0,0,'L');

$pdf->SetFont('Times','',13);
$testo="Temperatura:";
$pdf->Cell(25,10,$testo,0,0,'L');

$pdf->SetFont('Times','B',13);
$testo=$obj_u->{'temperatura'}."°C";
$pdf->Cell(50,10,$testo,0,1,'L');


$pdf->SetFont('Times','B',13);
$testo="INIZIO STERILIZZAZIONE";
$pdf->Cell(70,10,$testo,0,0,'L');

$pdf->SetFont('Times','',13);
$testo="Ora:";
$pdf->Cell(15,10,$testo,0,0,'L');

$pdf->SetFont('Times','B',13);
$testo=$obj_u->{'inizio_ora_steril'};
$pdf->Cell(40,10,$testo,0,0,'L');

$pdf->SetFont('Times','',13);
$testo="Temperatura:";
$pdf->Cell(25,10,$testo,0,0,'L');

$pdf->SetFont('Times','B',13);
$testo=$obj_u->{'inizio_temp_steril'}."°C";
$pdf->Cell(50,10,$testo,0,1,'L');



$pdf->SetFont('Times','B',13);
$testo="FINE STERILIZZAZIONE";
$pdf->Cell(70,10,$testo,0,0,'L');

$pdf->SetFont('Times','',13);
$testo="Ora:";
$pdf->Cell(15,10,$testo,0,0,'L');

$pdf->SetFont('Times','B',13);
$testo=$obj_u->{'fine_ora_steril'};
$pdf->Cell(40,10,$testo,0,0,'L');

$pdf->SetFont('Times','',13);
$testo="Temperatura:";
$pdf->Cell(25,10,$testo,0,0,'L');

$pdf->SetFont('Times','B',13);
$testo=$obj_u->{'fine_temp_steril'}."°C";
$pdf->Cell(50,10,$testo,0,1,'L');

if($obj_u->{'file_pdf_scontrino'} == "1") {
	$check_s = "4"; 
	$check_ns = ""; 
}	
else {
	$check_s = ""; 
	$check_ns = "4"; 
}	
//$pdf->Ln(3);
$pdf->SetFont('Times','B',13);
$pdf->Cell(70, 10, "REPORT CICLO DI STERILIZZAZIONE", 0, 1);
$pdf->SetX(15);
$pdf->SetFont('ZapfDingbats','', 10);
$pdf->Cell(5, 5, $check_s, 1, 0);
$pdf->SetFont('Times','',13);
$pdf->Cell(15, 5, "Disponibile", 0, 1);
$pdf->Ln(3);
$pdf->SetX(15);
$pdf->SetFont('ZapfDingbats','', 10);
$pdf->Cell(5, 5, $check_ns, 1, 0);
$pdf->SetFont('Times','',13);
$pdf->Cell(15, 5, "Non Disponibile", 0, 1);


$pdf->Ln(15);
$pdf->SetFont('Times','',13);

$testo="Firma esecuzione";
$pdf->Cell(156,5,$testo,0,1,'R');
$testo="_________________________";
$pdf->Cell(168,10,$testo,0,1,'R');

$pdf->Ln(7);
$testo="Firma controllo";
$pdf->Cell(156,5,$testo,0,1,'R');
$testo="_________________________";
$pdf->Cell(168,10,$testo,0,1,'R');



$pdf->SetFont('Arial','',9);	
$pdf->SetTextColor(0,0,0);
set_time_limit(30);

@unlink('mod29.pdf');
$HTTP_USER_AGENT=$_SERVER['HTTP_USER_AGENT'];
if(strstr($HTTP_USER_AGENT,"MSIE")) $pdf->Output("mod29.pdf","F");
else $pdf->Output("mod29.pdf","F");
echo "OK";

?>