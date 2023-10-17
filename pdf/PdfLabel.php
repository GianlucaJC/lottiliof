<?php

require_once('PDF_Label.php');

/**
 * Class to print labels in Avery or custom formats
 *
 * @author		Andrea Venturi
 * @copyright	Copyright (c) 2010 Andrea Venturi
 * @license		http://www.gnu.org/licenses/lgpl.html	GNU Lesser General Public License
 * @version		1.0
 *
 */
class PdfLabel extends PDF_Label
{
	/**
	 * @var	string	contiene alcuni formati di etichette
	 */
	public static $labelFormats = array(

	/* ETICHETTE 70 x 36 */
	'101700' => array(
	    'name'=>'101700',
	    'paper-size'=>'A4',
	    'metric'=>'mm',
	    'marginLeft'=>0,
	    'marginTop'=>4,
	    'NX'=>3,
	    'NY'=>8,
	    'SpaceX'=>0,
	    'SpaceY'=>0,
	    'width'=>70,
	    'height'=>36,
	    'font-size'=>8
	),
	/* ETICHETTE 70 x 37 */
	'101720' => array(
	    'name'=>'101720',
	    'paper-size'=>'A4',
	    'metric'=>'mm',
	    'marginLeft'=>0,
	    'marginTop'=>0,
	    'NX'=>3,
	    'NY'=>8,
	    'SpaceX'=>0,
	    'SpaceY'=>0,
	    'width'=>70,
	    'height'=>37,
	    'font-size'=>8
	),
	/* ETICHETTE 105 x 35 */
	'101800' => array(
	    'name'=>'101800',
	    'paper-size'=>'A4',
	    'metric'=>'mm',
	    'marginLeft'=>0,
	    'marginTop'=>8.5,
	    'NX'=>2,
	    'NY'=>8,
	    'SpaceX'=>0,
	    'SpaceY'=>0,
	    'width'=>105,
	    'height'=>35,
	    'font-size'=>8,
	/* ETICHETTE 105 x 35 */
	),
	'lory' => array(
	    'name'=>'lory',
	    'paper-size'=>'A4',
	    'metric'=>'mm',
	    'marginLeft'=>0,
	    'marginTop'=>8.5,
	    'NX'=>1,
	    'NY'=>1,
	    'SpaceX'=>0,
	    'SpaceY'=>0,
	    'width'=>209,
	    'height'=>297,
	    'font-size'=>35
	));

	/**
	 * Permette di impostare il numero di etichette da saltare prima della stampa.
	 * Utile quando si vuole stampare su un foglio non completo.
	 *
	 * @param	int	$num	numero di etichette da saltare prima dell'inizio
	 * @return	void
	 */
	public function skipLabels($num)
	{
		for($i=0; $i<$num; $i++)
		{
			$this->Add_PDF_Label('');
		}
	}
}