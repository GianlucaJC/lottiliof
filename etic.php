<?php
session_start();

					  
					  require('pdf/fpdf.php');
					  class PDF extends FPDF{
					  
						var $widths;
						var $aligns;
						var $tipo_vis;
						var $data_ric;
						var $angle=0;
						
						////estensione JS
						var $javascript;
						var $n_js;				
						
						
						////////////// metodi per barcode
				
								var $T128;                                             // tableau des codes 128					var $ABCset="";                                        // jeu des caractères éligibles au C128
								var $Aset="";                                          // Set A du jeu des caractères éligibles
								var $Bset="";                                          // Set B du jeu des caractères éligibles
								var $Cset="";                                          // Set C du jeu des caractères éligibles
								var $SetFrom;                                          // Convertisseur source des jeux vers le tableau
								var $SetTo;                                            // Convertisseur destination des jeux vers le tableau
								var $JStart = array("A"=>103, "B"=>104, "C"=>105);     // Caractères de sélection de jeu au début du C128
								var $JSwap = array("A"=>101, "B"=>100, "C"=>99);       // Caractères de changement de jeu

								//____________________________ Extension du constructeur _______________________
								function PDF($orientation='P', $unit='mm', $format='A4') {
									
									parent::FPDF($orientation,$unit,$format);

									$this->T128[] = array(2, 1, 2, 2, 2, 2);           //0 : [ ]               // composition des caractères
									$this->T128[] = array(2, 2, 2, 1, 2, 2);           //1 : [!]
									$this->T128[] = array(2, 2, 2, 2, 2, 1);           //2 : ["]
									$this->T128[] = array(1, 2, 1, 2, 2, 3);           //3 : [#]
									$this->T128[] = array(1, 2, 1, 3, 2, 2);           //4 : [$]
									$this->T128[] = array(1, 3, 1, 2, 2, 2);           //5 : [%]
									$this->T128[] = array(1, 2, 2, 2, 1, 3);           //6 : [&]
									$this->T128[] = array(1, 2, 2, 3, 1, 2);           //7 : [']
									$this->T128[] = array(1, 3, 2, 2, 1, 2);           //8 : [(]
									$this->T128[] = array(2, 2, 1, 2, 1, 3);           //9 : [)]
									$this->T128[] = array(2, 2, 1, 3, 1, 2);           //10 : [*]
									$this->T128[] = array(2, 3, 1, 2, 1, 2);           //11 : [+]
									$this->T128[] = array(1, 1, 2, 2, 3, 2);           //12 : [,]
									$this->T128[] = array(1, 2, 2, 1, 3, 2);           //13 : [-]
									$this->T128[] = array(1, 2, 2, 2, 3, 1);           //14 : [.]
									$this->T128[] = array(1, 1, 3, 2, 2, 2);           //15 : [/]
									$this->T128[] = array(1, 2, 3, 1, 2, 2);           //16 : [0]
									$this->T128[] = array(1, 2, 3, 2, 2, 1);           //17 : [1]
									$this->T128[] = array(2, 2, 3, 2, 1, 1);           //18 : [2]
									$this->T128[] = array(2, 2, 1, 1, 3, 2);           //19 : [3]
									$this->T128[] = array(2, 2, 1, 2, 3, 1);           //20 : [4]
									$this->T128[] = array(2, 1, 3, 2, 1, 2);           //21 : [5]
									$this->T128[] = array(2, 2, 3, 1, 1, 2);           //22 : [6]
									$this->T128[] = array(3, 1, 2, 1, 3, 1);           //23 : [7]
									$this->T128[] = array(3, 1, 1, 2, 2, 2);           //24 : [8]
									$this->T128[] = array(3, 2, 1, 1, 2, 2);           //25 : [9]
									$this->T128[] = array(3, 2, 1, 2, 2, 1);           //26 : [:]
									$this->T128[] = array(3, 1, 2, 2, 1, 2);           //27 : [;]
									$this->T128[] = array(3, 2, 2, 1, 1, 2);           //28 : [<]
									$this->T128[] = array(3, 2, 2, 2, 1, 1);           //29 : [=]
									$this->T128[] = array(2, 1, 2, 1, 2, 3);           //30 : [>]
									$this->T128[] = array(2, 1, 2, 3, 2, 1);           //31 : [?]
									$this->T128[] = array(2, 3, 2, 1, 2, 1);           //32 : [@]
									$this->T128[] = array(1, 1, 1, 3, 2, 3);           //33 : [A]
									$this->T128[] = array(1, 3, 1, 1, 2, 3);           //34 : [B]
									$this->T128[] = array(1, 3, 1, 3, 2, 1);           //35 : [C]
									$this->T128[] = array(1, 1, 2, 3, 1, 3);           //36 : [D]
									$this->T128[] = array(1, 3, 2, 1, 1, 3);           //37 : [E]
									$this->T128[] = array(1, 3, 2, 3, 1, 1);           //38 : [F]
									$this->T128[] = array(2, 1, 1, 3, 1, 3);           //39 : [G]
									$this->T128[] = array(2, 3, 1, 1, 1, 3);           //40 : [H]
									$this->T128[] = array(2, 3, 1, 3, 1, 1);           //41 : [I]
									$this->T128[] = array(1, 1, 2, 1, 3, 3);           //42 : [J]
									$this->T128[] = array(1, 1, 2, 3, 3, 1);           //43 : [K]
									$this->T128[] = array(1, 3, 2, 1, 3, 1);           //44 : [L]
									$this->T128[] = array(1, 1, 3, 1, 2, 3);           //45 : [M]
									$this->T128[] = array(1, 1, 3, 3, 2, 1);           //46 : [N]
									$this->T128[] = array(1, 3, 3, 1, 2, 1);           //47 : [O]
									$this->T128[] = array(3, 1, 3, 1, 2, 1);           //48 : [P]
									$this->T128[] = array(2, 1, 1, 3, 3, 1);           //49 : [Q]
									$this->T128[] = array(2, 3, 1, 1, 3, 1);           //50 : [R]
									$this->T128[] = array(2, 1, 3, 1, 1, 3);           //51 : [S]
									$this->T128[] = array(2, 1, 3, 3, 1, 1);           //52 : [T]
									$this->T128[] = array(2, 1, 3, 1, 3, 1);           //53 : [U]
									$this->T128[] = array(3, 1, 1, 1, 2, 3);           //54 : [V]
									$this->T128[] = array(3, 1, 1, 3, 2, 1);           //55 : [W]
									$this->T128[] = array(3, 3, 1, 1, 2, 1);           //56 : [X]
									$this->T128[] = array(3, 1, 2, 1, 1, 3);           //57 : [Y]
									$this->T128[] = array(3, 1, 2, 3, 1, 1);           //58 : [Z]
									$this->T128[] = array(3, 3, 2, 1, 1, 1);           //59 : [[]
									$this->T128[] = array(3, 1, 4, 1, 1, 1);           //60 : [\]
									$this->T128[] = array(2, 2, 1, 4, 1, 1);           //61 : []]
									$this->T128[] = array(4, 3, 1, 1, 1, 1);           //62 : [^]
									$this->T128[] = array(1, 1, 1, 2, 2, 4);           //63 : [_]
									$this->T128[] = array(1, 1, 1, 4, 2, 2);           //64 : [`]
									$this->T128[] = array(1, 2, 1, 1, 2, 4);           //65 : [a]
									$this->T128[] = array(1, 2, 1, 4, 2, 1);           //66 : [b]
									$this->T128[] = array(1, 4, 1, 1, 2, 2);           //67 : [c]
									$this->T128[] = array(1, 4, 1, 2, 2, 1);           //68 : [d]
									$this->T128[] = array(1, 1, 2, 2, 1, 4);           //69 : [e]
									$this->T128[] = array(1, 1, 2, 4, 1, 2);           //70 : [f]
									$this->T128[] = array(1, 2, 2, 1, 1, 4);           //71 : [g]
									$this->T128[] = array(1, 2, 2, 4, 1, 1);           //72 : [h]
									$this->T128[] = array(1, 4, 2, 1, 1, 2);           //73 : [i]
									$this->T128[] = array(1, 4, 2, 2, 1, 1);           //74 : [j]
									$this->T128[] = array(2, 4, 1, 2, 1, 1);           //75 : [k]
									$this->T128[] = array(2, 2, 1, 1, 1, 4);           //76 : [l]
									$this->T128[] = array(4, 1, 3, 1, 1, 1);           //77 : [m]
									$this->T128[] = array(2, 4, 1, 1, 1, 2);           //78 : [n]
									$this->T128[] = array(1, 3, 4, 1, 1, 1);           //79 : [o]
									$this->T128[] = array(1, 1, 1, 2, 4, 2);           //80 : [p]
									$this->T128[] = array(1, 2, 1, 1, 4, 2);           //81 : [q]
									$this->T128[] = array(1, 2, 1, 2, 4, 1);           //82 : [r]
									$this->T128[] = array(1, 1, 4, 2, 1, 2);           //83 : [s]
									$this->T128[] = array(1, 2, 4, 1, 1, 2);           //84 : [t]
									$this->T128[] = array(1, 2, 4, 2, 1, 1);           //85 : [u]
									$this->T128[] = array(4, 1, 1, 2, 1, 2);           //86 : [v]
									$this->T128[] = array(4, 2, 1, 1, 1, 2);           //87 : [w]
									$this->T128[] = array(4, 2, 1, 2, 1, 1);           //88 : [x]
									$this->T128[] = array(2, 1, 2, 1, 4, 1);           //89 : [y]
									$this->T128[] = array(2, 1, 4, 1, 2, 1);           //90 : [z]
									$this->T128[] = array(4, 1, 2, 1, 2, 1);           //91 : [{]
									$this->T128[] = array(1, 1, 1, 1, 4, 3);           //92 : [|]
									$this->T128[] = array(1, 1, 1, 3, 4, 1);           //93 : [}]
									$this->T128[] = array(1, 3, 1, 1, 4, 1);           //94 : [~]
									$this->T128[] = array(1, 1, 4, 1, 1, 3);           //95 : [DEL]
									$this->T128[] = array(1, 1, 4, 3, 1, 1);           //96 : [FNC3]
									$this->T128[] = array(4, 1, 1, 1, 1, 3);           //97 : [FNC2]
									$this->T128[] = array(4, 1, 1, 3, 1, 1);           //98 : [SHIFT]
									$this->T128[] = array(1, 1, 3, 1, 4, 1);           //99 : [Cswap]
									$this->T128[] = array(1, 1, 4, 1, 3, 1);           //100 : [Bswap]                
									$this->T128[] = array(3, 1, 1, 1, 4, 1);           //101 : [Aswap]
									$this->T128[] = array(4, 1, 1, 1, 3, 1);           //102 : [FNC1]
									$this->T128[] = array(2, 1, 1, 4, 1, 2);           //103 : [Astart]
									$this->T128[] = array(2, 1, 1, 2, 1, 4);           //104 : [Bstart]
									$this->T128[] = array(2, 1, 1, 2, 3, 2);           //105 : [Cstart]
									$this->T128[] = array(2, 3, 3, 1, 1, 1);           //106 : [STOP]
									$this->T128[] = array(2, 1);                       //107 : [END BAR]

									for ($i = 32; $i <= 95; $i++) {                                            // jeux de caractères
										$this->ABCset .= chr($i);
									}
									$this->Aset = $this->ABCset;
									$this->Bset = $this->ABCset;
									for ($i = 0; $i <= 31; $i++) {
										$this->ABCset .= chr($i);
										$this->Aset .= chr($i);
									}
									for ($i = 96; $i <= 126; $i++) {
										$this->ABCset .= chr($i);
										$this->Bset .= chr($i);
									}
									$this->Cset="0123456789";

									for ($i=0; $i<96; $i++) {                                                  // convertisseurs des jeux A & B  
										@$this->SetFrom["A"] .= chr($i);
										@$this->SetFrom["B"] .= chr($i + 32);
										@$this->SetTo["A"] .= chr(($i < 32) ? $i+64 : $i-32);
										@$this->SetTo["B"] .= chr($i);
									}
								}

								//________________ Fonction encodage et dessin du code 128 _____________________
								function Code128($x, $y, $code, $w, $h) {
									$Aguid = "";                                                                      // Création des guides de choix ABC
									$Bguid = "";
									$Cguid = "";
									for ($i=0; $i < strlen($code); $i++) {
										$needle = substr($code,$i,1);
										$Aguid .= ((strpos($this->Aset,$needle)===false) ? "N" : "O"); 
										$Bguid .= ((strpos($this->Bset,$needle)===false) ? "N" : "O"); 
										$Cguid .= ((strpos($this->Cset,$needle)===false) ? "N" : "O");
									}

									$SminiC = "OOOO";
									$IminiC = 4;

									$crypt = "";
									while ($code > "") {
																													// BOUCLE PRINCIPALE DE CODAGE
										$i = strpos($Cguid,$SminiC);                                                // forçage du jeu C, si possible
										if ($i!==false) {
											$Aguid [$i] = "N";
											$Bguid [$i] = "N";
										}

										if (substr($Cguid,0,$IminiC) == $SminiC) {                                  // jeu C
											$crypt .= chr(($crypt > "") ? $this->JSwap["C"] : $this->JStart["C"]);  // début Cstart, sinon Cswap
											$made = strpos($Cguid,"N");                                             // étendu du set C
											if ($made === false) {
												$made = strlen($Cguid);
											}
											if (fmod($made,2)==1) {
												$made--;                                                            // seulement un nombre pair
											}
											for ($i=0; $i < $made; $i += 2) {
												$crypt .= chr(strval(substr($code,$i,2)));                          // conversion 2 par 2
											}
											$jeu = "C";
										} else {
											$madeA = strpos($Aguid,"N");                                            // étendu du set A
											if ($madeA === false) {
												$madeA = strlen($Aguid);
											}
											$madeB = strpos($Bguid,"N");                                            // étendu du set B
											if ($madeB === false) {
												$madeB = strlen($Bguid);
											}
											$made = (($madeA < $madeB) ? $madeB : $madeA );                         // étendu traitée
											$jeu = (($madeA < $madeB) ? "B" : "A" );                                // Jeu en cours

											$crypt .= chr(($crypt > "") ? $this->JSwap[$jeu] : $this->JStart[$jeu]); // début start, sinon swap

											$crypt .= strtr(substr($code, 0,$made), $this->SetFrom[$jeu], $this->SetTo[$jeu]); // conversion selon jeu

										}
										$code = substr($code,$made);                                           // raccourcir légende et guides de la zone traitée
										$Aguid = substr($Aguid,$made);
										$Bguid = substr($Bguid,$made);
										$Cguid = substr($Cguid,$made);
									}                                                                          // FIN BOUCLE PRINCIPALE

									$check = ord($crypt[0]);                                                   // calcul de la somme de contrôle
									for ($i=0; $i<strlen($crypt); $i++) {
										$check += (ord($crypt[$i]) * $i);
									}
									$check %= 103;

									$crypt .= chr($check) . chr(106) . chr(107);                               // Chaine Cryptée complète

									$i = (strlen($crypt) * 11) - 8;                                            // calcul de la largeur du module
									$modul = $w/$i;

									for ($i=0; $i<strlen($crypt); $i++) {                                      // BOUCLE D'IMPRESSION
										$c = $this->T128[ord($crypt[$i])];
										for ($j=0; $j<count($c); $j++) {
											$this->Rect($x,$y,$c[$j]*$modul,$h,"F");
											$x += ($c[$j++]+$c[$j])*$modul;
										}
									}
								}

						//////////////


								function IncludeJS($script) {
									$this->javascript=$script;
								}

								function _putjavascript() {
									$this->_newobj();
									$this->n_js=$this->n;
									$this->_out('<<');
									$this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
									$this->_out('>>');
									$this->_out('endobj');
									$this->_newobj();
									$this->_out('<<');
									$this->_out('/S /JavaScript');
									$this->_out('/JS '.$this->_textstring($this->javascript));
									$this->_out('>>');
									$this->_out('endobj');
								}

								function _putresources() {
									parent::_putresources();
									if (!empty($this->javascript)) {
										$this->_putjavascript();
									}
								}

								function _putcatalog() {
									parent::_putcatalog();
									if (!empty($this->javascript)) {
										$this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
									}
								}

								function AutoPrint($dialog=false)
								{
									//Open the print dialog or start printing immediately on the standard printer
									$param=($dialog ? 'true' : 'false');
									$script="print($param);";
									$this->IncludeJS($script);
								}

								function AutoPrintToPrinter($server, $printer, $dialog=false)
								{
									//Print on a shared printer (requires at least Acrobat 6)
									$script = "var pp = getPrintParams();";
									if($dialog)
										$script .= "pp.interactive = pp.constants.interactionLevel.full;";
									else
										$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
									$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
									$script .= "print(pp);";
									$this->IncludeJS($script);
								}
								///// fine estensione classa JS
								

									function Rotate($angle,$x=-1,$y=-1)
									{
										if($x==-1)
										$x=$this->x;
										if($y==-1)
										$y=$this->y;
										if($this->angle!=0)
										$this->_out('Q');
										$this->angle=$angle;
										if($angle!=0)
										{
											$angle*=M_PI/180;
											$c=cos($angle);
											$s=sin($angle);
											$cx=$x*$this->k;
											$cy=($this->h-$y)*$this->k;
											$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
										}
									}

									function _endpage(){
										if($this->angle!=0){
											$this->angle=0;
											$this->_out('Q');
										}
										parent::_endpage();
									}			
								
									function RotatedText($x,$y,$txt,$angle)
									{
										//Text rotated around its origin
										$this->Rotate($angle,$x,$y);
										$this->Text($x,$y,$txt);
										$this->Rotate(0);
									}

									function RotatedImage($file,$x,$y,$w,$h,$angle)
									{
										//Image rotated around its upper-left corner
										$this->Rotate($angle,$x,$y);
										$this->Image($file,$x,$y,$w,$h);
										$this->Rotate(0);
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
									
									
									
									function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0) {
										$font_angle+=90+$txt_angle;
										$txt_angle*=M_PI/180;
										$font_angle*=M_PI/180;
									
										$txt_dx=cos($txt_angle);
										$txt_dy=sin($txt_angle);
										$font_dx=cos($font_angle);
										$font_dy=sin($font_angle);
									
										$s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',$txt_dx,$txt_dy,$font_dx,$font_dy,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
										if ($this->ColorFlag)
											$s='q '.$this->TextColor.' '.$s.' Q';
										$this->_out($s);
									}

						}	

require_once('qrcode.class.php');	  
$id_print=uniqid();

$codice=$_POST['codice'];
$descr=$_POST['descr'];
$lotto=$_POST['lotto'];
$data_p=$_POST['data_p'];
$scadenza=$_POST['scadenza'];


//etichetta steril
if (isset($_POST['operazione'])) {
	if ($_POST['operazione']=="etic_steril") {
		$codice_materiale=$_POST['codice_materiale'];
		$lotto_materiale=$_POST['lotto_materiale'];
		$lotto_steril=$_POST['lotto_steril'];

		$pdf = new PDF('L', 'mm', 'piccola');
		$pdf->SetTextColor(0,0,0);  ////imposta testo nero


		$pdf->SetMargins(0,0,0);
		$pdf->AddPage();
		$pdf->AliasNbPages();
		$pdf->SetAuthor('Liofilchem');
		$pdf->SetCreator('Liofilchem');
		$pdf->SetTitle('Etichetta');


		$testo="Codice Materiale";
		$pdf->SetFont('Arial','',10);	
		$pdf->RotatedText(13,95,$testo,90);
		
		$pdf->SetFont('Arial','B',13);	
		$pdf->RotatedText(13,59,$codice_materiale,90);
		
		$testo="Lotto Materiale";
		$pdf->SetFont('Arial','',10);	
		$pdf->RotatedText(25,95,$testo,90);
		
		$pdf->SetFont('Arial','B',13);	
		$pdf->RotatedText(25,59,$lotto_materiale,90);


		$testo="Lotto Sterilizzazione";
		$pdf->SetFont('Arial','',10);	
		$pdf->RotatedText(37,95,$testo,90);
		
		$pdf->SetFont('Arial','B',13);	
		$pdf->RotatedText(37,59,$lotto_steril,90);


		$pdf->Output("label/steril.pdf","F");		
		exit;
	}
}
///////////////////


//etichetta piccola
$pdf = new PDF('P', 'mm', 'piccola');
$pdf->SetTextColor(0,0,0);  ////imposta testo nero


$pdf->SetMargins(0,0,0);
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetAuthor('Liofilchem');
$pdf->SetCreator('Liofilchem');
$pdf->SetTitle('Etichetta');


	

// set Barcode
$bc="$codice+$lotto";

$testo="Lotto $lotto";
$pdf->SetFont('Arial','B',39);	
$pdf->RotatedText(22,95,$codice,90);
$pdf->SetFont('Courier','B',13);	
$d1=substr($descr,0,10);
$d2=substr($descr,10);
$pdf->RotatedText(28,95,$d1,90);
$pdf->RotatedText(33,95,$d2,90);
if ($scadenza!="01-01-1980" && $scadenza!="1980-01-01") {
	//$scadenza="Exp. ".data_it($scadenza);
	$scad="Exp. ".$scadenza;
	$pdf->RotatedText(28,55,$scad,90);
}

$pdf->SetFont('Arial','B',12);	
$pdf->RotatedText(45,95,'Lotto',90);
$pdf->SetFont('Arial','B',37);	

$pdf->RotatedText(45,82,$lotto,90);
//$pdf->AutoPrint(false);   // per stampare direttamente


//A,C,B sets
$pdf->rotate(90);
$pdf->Code128(-95,1,$bc,75,8);		
$pdf->rotate(0);
$pdf->SetFont('Arial','B',5);
$pdf->RotatedText(11,65,$bc,90);

$qrcode = new QRcode($lotto, 'H'); // error level : L, M, Q, H
$qrcode->disableBorder();
$qrcode->displayFPDF($pdf,9,2,14);

$pdf->Output("label/small$id_print.pdf","F");


//etichetta grande
$pdf = new PDF('P', 'mm', 'grande');
$pdf->SetTextColor(0,0,0);  ////imposta testo nero

$pdf->SetMargins(0,0,0);
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetAuthor('Liofilchem');
$pdf->SetCreator('Liofilchem');
$pdf->SetTitle('Etichetta');





if ($scadenza!="01-01-1980" && $scadenza!="1980-01-01") {	
	//$scadenza="Exp. ".data_it($scadenza);
	$scad="Exp. ".$scadenza;
	$pdf->SetFont('Courier','B',12);
	$pdf->Cell(0,20,$scad,0,1,'C');		
}

$pdf->SetFont('Arial','B',64);	
$pdf->Cell(0,20,$codice,0,1,'C');	
$pdf->SetFont('Courier','B',20);	
$pdf->Ln(5);
$d1=substr($descr,0,25);
$d2=substr($descr,25);
$pdf->Cell(40,10,'',0,0);
$pdf->Cell(70,10,$d1,0,1,'C');
$pdf->Cell(40,10,'',0,0);
	
$pdf->Cell(70,10,$d2,0,1,'C');

$testo="Lot. $lotto";

$pdf->SetFont('Arial','B',45);	
$pdf->Ln(4);
$pdf->Cell(0,10,$testo,0,0,'C');



//A,C,B sets

$pdf->Code128(30,82,$bc,80,10);

//$pdf->SetXY(5,70);
$pdf->SetFont('Arial','B',10);	

$pdf->RotatedText(60,95,$bc,0);



$qrcode = new QRcode($lotto, 'H'); // error level : L, M, Q, H
$qrcode->disableBorder();
$qrcode->displayFPDF($pdf,4,25,18);

//$pdf->AutoPrint();   // per stampare direttamente
$pdf->Output("label/large$id_print.pdf","F");





//etichetta soluzioni
$pdf = new PDF('P', 'mm', 'piccola');
$pdf->SetTextColor(0,0,0);  ////imposta testo nero


$pdf->SetMargins(0,0,0);
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetAuthor('Liofilchem');
$pdf->SetCreator('Liofilchem');
$pdf->SetTitle('Etichetta');





	//A,C,B sets
$pdf->rotate(90);
$pdf->Code128(-95,1,$bc,75,7);		
$pdf->rotate(0);
$pdf->SetFont('Arial','B',4);
$pdf->RotatedText(10,65,$bc,90);

$testo="Lotto $lotto";

$pdf->SetFont('Arial','I',10);		
$pdf->RotatedText(13,100,"Codice",90);
$pdf->SetFont('Arial','B',10);		
$pdf->RotatedText(13,86,$codice,90);

$pdf->SetFont('Arial','I',10);		
$pdf->RotatedText(18,100,"Lotto",90);	
$pdf->SetFont('Arial','B',10);		
$pdf->RotatedText(18,89,$lotto,90);

$pdf->SetFont('Arial','I',8);		
$pdf->RotatedText(24,100,"Data",90);
$pdf->RotatedText(27,100,"preparazione",90);	
$pdf->SetFont('Arial','B',10);
$data_p=substr($lotto,2,2)."-".substr($lotto,0,2)."-20".substr($lotto,4,2);
$pdf->RotatedText(27,82,$data_p,90);

//$scadx=data_it($scadenza);		
$scadx=$scadenza;
if ($scadx=="1980-01-01" || $scadx=="01-01-1980")  $scadx=null;

$pdf->SetFont('Arial','I',10);		
$pdf->RotatedText(32,100,"Scadenza",90);	
$pdf->SetFont('Arial','B',10);		
$pdf->RotatedText(32,82,$scadx,90);	

$pdf->SetFont('Arial','I',10);		
$pdf->RotatedText(38,100,"Firma",90);	
$pdf->SetFont('Arial','B',10);		
$pdf->RotatedText(38,90,"_____________",90);	



$pdf->SetFont('Arial','I',10);		
$pdf->RotatedText(13,60,"Descrizione",90);	
$d1=substr($descr,0,18);
$d2=substr($descr,18);
$pdf->SetFont('Arial','B',10);		
$pdf->RotatedText(13,40,$d1,90);	
$pdf->RotatedText(16,40,$d2,90);	


$pdf->SetFont('Arial','I',10);
$pdf->RotatedText(24,60,"Data apertura",90);	
$pdf->SetFont('Arial','B',10);		
$pdf->RotatedText(24,38,"_____________",90);		


$pdf->SetFont('Arial','I',10);
$pdf->RotatedText(32,60,"Eliminare entro",90);	
$pdf->SetFont('Arial','B',10);
$pdf->RotatedText(32,36,"_____________",90);		

$pdf->SetFont('Arial','I',10);
$pdf->RotatedText(38,60,"Firma",90);	
$pdf->SetFont('Arial','B',10);
$pdf->RotatedText(38,48,"___________________",90);		



if ($scadx!="1980-01-01" && $scadx!="01-01-1980" && $scadx!="--" && $scadx!=null) $pdf->Output("label/solution$id_print.pdf","F");
echo $id_print;
	
	
function data_it($data)
{
  $array = explode("-", $data); 
  $data_it = $array[2]."-".$array[1]."-".$array[0]; 
  if ($data_it=='00-00-0000') $data_it='';
  return $data_it; 
}
	