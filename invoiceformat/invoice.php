<?php
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

// Mock Data for demonstration
// $jsonData = '{
//   "status": "success",
//   "order": {
//     "id": "5",
//     "order_date": "15 Jan 2025",
//     "total_amount": "21340.00",
//     "payment_status": "partial"
//   },
//   "party": {
//     "name": "Samrajya Traders",
//     "contact_person": "Sagar",
//     "phone": "6264888385",
//     "email": "sagar@gmail.com"
//   },
//   "items": [
//     {"product_name": "Binding Wire", "quantity": "120.00", "unit_name": "Kilogram", "unit_price": "62.00", "total_price": "7440.00"},
//     {"product_name": "Barbed Wire", "quantity": "50.00", "unit_name": "Kilogram", "unit_price": "62.00", "total_price": "3100.00"},
//     {"product_name": "Tarpin Oil", "quantity": "200.00", "unit_name": "Piece", "unit_price": "17.00", "total_price": "3400.00"},
//     {"product_name": "Paan Belcha", "quantity": "40.00", "unit_name": "Box", "unit_price": "185.00", "total_price": "7400.00"}
//   ],
//   "payments": [
//     {"payment_date": "15 Jan 2025", "amount": "1340.00", "payment_mode": "Cash", "reference_no": "465432154321321314", "notes": "Through the payment by Nitesh"},
//     {"payment_date": "15 Jan 2025", "amount": "2000.00", "payment_mode": "Cheque", "reference_no": "02000", "notes": "Given Cheque No. Mention our Reference No. Collect by Anjum Hardware\r\n"},
//     {"payment_date": "15 Jan 2025", "amount": "5000.00", "payment_mode": "Upi", "reference_no": "65432165432121", "notes": "UPI Transfer by New Number Collect by Ajay Salesman"}
//   ]
// }';
// echo $_GET['id'];
$data = json_decode($_POST['jsonData'], true);
// if (isset($_POST['jsonData'])) {
var_dump($data);
// $data['order']['id']
// }
// if (isset($_POST['jsonData'])) {
//   // Decode JSON data
//   $data = json_decode($_POST['jsonData'], true);
// }
die;
// $data = json_decode($jsonData, true);

// Create new PDF document
$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Title Section
$html = "<h1 style='text-align:center;'>TAX INVOICE</h1>";
$html .= "<h3 style='text-align:right;'>Company Name</h3>";
// $html .= "<h4 style='text-align:center;'>Company Address</h4>";
// $html .= "<h4 style='text-align:center;'>GSTIN: No.098f098sdf</h4>";

// Top Right Section for Date and Invoice Number
$html .= "<table style='width:100%;'><tr>";
$html .= "<td>Date: " . $data['order']['order_date'] . "</td></tr>";
$html .= "<tr><td>Invoice No.: " . $data['order']['id'] . "</td></tr></table><br/>";
$html .= "Payment Status :" . $data['order']['payment_status'];
// Party Details
// $html .= "<table style='width:100%; margin-top:20px;'>";
// $html .= "<tr><th style='width:50%;'><b>Bill To:</b><br/>" . $data['party']['name'] . "<br/>Contact: " . $data['party']['contact_person'] . "<br/>Phone: " . $data['party']['phone'] . "<br/>Email: " . $data['party']['email'] . "</th></tr>";
// // $html .= "<th style='width:50%; text-align:right; line-height: 250%;'><b>Payment Status:</b> " . ucfirst($data['order']['payment_status']) . "</th>";
// $html .= "</table>";




// Items Table
$html .= "<br><br>";
$html .= "<table border='1' cellpadding='4' cellspacing='0' style='width:100%; margin-top:20px;'>";
$html .= "<tr style='background-color:#f2f2f2;'>
<th>S.N.</th><th>Products</th><th>Qty</th><th>Unit</th><th>Price</th><th>Total</th>
</tr>";

$sn = 1;
$totalamount = array_sum(array_column($data['items'], 'total_price'));
foreach ($data['items'] as $item) {
  $html .= "<tr>";
  $html .= "<td>$sn</td>";
  $html .= "<td>" . $item['product_name'] . "</td>";
  $html .= "<td>" . $item['quantity'] . "</td>";
  $html .= "<td>" . $item['unit_name'] . "</td>";
  $html .= "<td>" . $item['unit_price'] . "</td>";
  $html .= "<td style='border-top-color:#000000;border-top-width:1px;border-top-style:solid;'>" . $item['total_price'] . "</td>";
  $html .= "</tr>";
  $sn++;
}
$html .= "</table>";
$html .= "<mark><h4><i>Total Amount: " . $totalamount . "</i></h4></mark>";

// Footer Section for Payment History
$html .= "<h4 style='margin-top:30px;'>Payment History</h4>";
$html .= "<table border='1' cellpadding='4' cellspacing='0' style='width:100%;'>";
$html .= "<tr style='background-color:#f2f2f2;'>
<th>Date</th><th>Amount</th><th>Payment Mode</th><th>Reference No</th><th>Notes</th>
</tr>";
foreach ($data['payments'] as $payment) {
  $html .= "<tr>";
  $html .= "<td>" . $payment['payment_date'] . "</td>";
  $html .= "<td>" . $payment['amount'] . "</td>";
  $html .= "<td>" . $payment['payment_mode'] . "</td>";
  $html .= "<td>" . $payment['reference_no'] . "</td>";
  $html .= "<td>" . $payment['notes'] . "</td>";
  $html .= "</tr>";
}
$html .= "</table>";
$pdf->SetFont('helvetica', 'B', 10);
// $pdf->MultiCell(100, 10, 'Bill To:           '.$data['party']['name'].' '.$data['party']['contact_person'].' Phone: ' . $data['party']['phone'] .' Email: ' . $data['party']['email'], 1, 0, 'L');
$pdf->setFillColor(230, 230, 230);
$pdf->MultiCell(85, 15, 'Bill To:                                                                        Company Name: ' . $data['party']['name'] . ' Contact Person: ' . $data['party']['contact_person'] . ' Phone: ' . $data['party']['phone'] . ' Email: ' . $data['party']['email'], 1, 'L', 1, 0, '', '', true);
// $pdf->Cell(95, 10, ucfirst($data['order']['payment_status']), 1, 0, 'R');
$pdf->Ln(20);
// Output PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('invoice'.$invoe.'.pdf', 'D');
