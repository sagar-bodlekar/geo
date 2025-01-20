<?php
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

class MYPDF extends TCPDF
{
	public function Header()
	{
		$data = json_decode($_GET['jsonData'], true);
		$invoe = 'T' . date('y') . date('m') . str_pad($data['order']['id'], 3, '0', STR_PAD_LEFT);
		$image_file = 'http://localhost/geo/img/favicon/android-chrome-512x512.png';
		$this->Image($image_file, 10, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 20);
		// Title
		$this->Cell(0, 15, 'Estimate', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 10);
		// Right Side Text
		$this->Cell(0, 10, 'Date :' . date('d/m/y'), 0, false, 'R');
		$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 10);
		$this->Ln(4.5);
		$this->Cell(0, 10, 'Invoice :' . $invoe, 0, false, 'R');
	}
	public function Footer()
	{

		// 2) Right Side
		$this->SetY(-15);
		$this->SetFont(PDF_FONT_NAME_MAIN, 'I', 8);
		$this->Cell(0, 10, 'Authorised Signatory', 0, false, 'R');
	}
	public function CreateTextBox($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L')
	{
		$this->SetXY($x + 20, $y); // 20 = margin left
		$this->SetFont(PDF_FONT_NAME_MAIN, $fontstyle, $fontsize);
		$this->Cell($width, $height, $textval, 0, false, $align);
	}
}
$data = json_decode($_GET['jsonData'], true);
// if (isset($_POST['jsonData'])) {
// var_dump($data);
// echo $_GET['jsonData'];
// die;
$invoe = 'T' . date('y') . date('m') . str_pad($data['order']['id'], 3, '0', STR_PAD_LEFT);
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
/**
 * Set information about that 
 */
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sagar Samrajya');
$pdf->SetTitle('Invoice Format');
$pdf->SetSubject('Order Summery');
$pdf->SetKeywords('TCPDF, PDF, Order Summery, Payment summery of Order');

// add a page
$pdf->AddPage();

// create address box
$pdf->CreateTextBox($data['party']['name'], 0, 35, 80, 10, 10, 'B');
$pdf->CreateTextBox($data['party']['contact_person'], 0, 39, 80, 10, 10);
$pdf->CreateTextBox($data['party']['phone'], 0, 43, 80, 10, 10);
$pdf->CreateTextBox($data['party']['email'], 0, 47, 80, 10, 10);
$pdf->CreateTextBox($data['party']['address'], 0, 51, 80, 10, 10);

// invoice title / number
$pdf->CreateTextBox('Invoice', 0, 53, 120, 20, 10, 'I');
$pdf->CreateTextBox('#' . $invoe, 0, 58, 120, 20, 16, 'I');

// payment status, order date
$pdf->CreateTextBox('Order Date: ' . $data['order']['order_date'], 0, 59, 0, 10, 10, 'B', 'R');
$pdf->CreateTextBox('Payment Status: ' . $data['order']['payment_status'], 0, 63, 0, 10, 10, 'I', 'R');

// list headers
// $pdf->CreateTextBox('Quantity', 0, 120, 20, 10, 10, 'B', 'C');
// $pdf->CreateTextBox('Product or service', 20, 120, 90, 10, 10, 'B');
// $pdf->CreateTextBox('Price', 110, 120, 30, 10, 10, 'B', 'R');
// $pdf->CreateTextBox('Amount', 140, 120, 30, 10, 10, 'B', 'R');

// $pdf->Line(20, 129, 195, 129);


/**
 * Table Head Ready To do
 */
$pdf->CreateTextBox('Sno.', 0, 70, 20, 10, 10, 'B', 'C'); //Sno.
$pdf->CreateTextBox('Product', 20, 70, 90, 10, 10, 'B'); //Product
$pdf->CreateTextBox('Qty.', 70, 70, 30, 10, 10, 'B', 'C'); //Qty
$pdf->CreateTextBox('Unit', 90, 70, 30, 10, 10, 'B', 'C'); //Unit
$pdf->CreateTextBox('Price', 110, 70, 30, 10, 10, 'B', 'R'); //Price
$pdf->CreateTextBox('Amt.', 130, 70, 40, 10, 10, 'B', 'R'); //Amt.

$pdf->Line(20, 79, 195, 79);

$orders = $data['items'];

$currY = 78;
$total = 0;
$n = 1;
foreach ($orders as $row) {
	$pdf->CreateTextBox($n, 0, $currY, 20, 10, 10, '', 'C'); //Sno.
	$pdf->CreateTextBox($row['product_name'] . '(' . $row['sku'] . ')', 20, $currY, 90, 10, 10, '', 'L'); //Product
	$pdf->CreateTextBox($row['quantity'], 70, $currY, 30, 10, 10, '', 'C'); //Qty
	$pdf->CreateTextBox($row['unit_name'], 90, $currY, 30, 10, 10, '', 'C'); //Unit
	$pdf->CreateTextBox($row['unit_price'], 110, $currY, 50, 10, 10, '', 'C'); //Price
	$pdf->CreateTextBox($row['total_price'], 130, $currY, 40, 10, 10, '', 'R'); //Amt
	$currY = $currY + 5;
	// $total = $total + $amount;
	$n++;
}
$pdf->Line(20, $currY + 4, 195, $currY + 4);

// some example data
// $orders[] = array('quant' => 5, 'descr' => '.com domain registration', 'price' => 9.95);
// $orders[] = array('quant' => 3, 'descr' => '.net domain name renewal', 'price' => 11.95);
// $orders[] = array('quant' => 1, 'descr' => 'SSL certificate 256-Byte encryption', 'price' => 99.95);
// $orders[] = array('quant' => 1, 'descr' => '25GB VPS Hosting, 200GB Bandwidth', 'price' => 19.95);

// $currY = 128;
// $total = 0;
// foreach ($orders as $row) {
// 	$pdf->CreateTextBox($row['quant'], 0, $currY, 20, 10, 10, '', 'C');
// 	$pdf->CreateTextBox($row['descr'], 20, $currY, 90, 10, 10, '');
// 	$pdf->CreateTextBox('$' . $row['price'], 110, $currY, 30, 10, 10, '', 'R');
// 	$amount = $row['quant'] * $row['price'];
// 	$pdf->CreateTextBox('$' . $amount, 140, $currY, 30, 10, 10, '', 'R');
// 	$currY = $currY + 5;
// 	$total = $total + $amount;
// }
// $pdf->Line(20, $currY + 4, 195, $currY + 4);

// output the total row
$pdf->CreateTextBox('Total', 20, $currY + 5, 125, 10, 10, 'B', 'R');
$pdf->CreateTextBox('$' . number_format($data['order']['total_amount'], 2, '.', ''), 140, $currY + 5, 30, 10, 10, 'B', 'R');

// some payment instructions or information
// $pdf->setXY(20, $currY + 30);
// $pdf->SetFont(PDF_FONT_NAME_MAIN, '', 10);
// $pdf->MultiCell(175, 10, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
// Vestibulum sagittis venenatis urna, in pellentesque ipsum pulvinar eu. In nec nulla libero, eu sagittis diam. Aenean egestas pharetra urna, et tristique metus egestas nec. Aliquam erat volutpat. Fusce pretium dapibus tellus.', 0, 'L', 0, 1, '', '', true, null, true);

//Close and output PDF document
$pdf->Output('invoice' . $invoe . '.pdf', 'D');
