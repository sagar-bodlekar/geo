<?php
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

// Fetch the JSON data from AJAX
$jsonData = '{
  "status": "success",
  "order": {
    "id": "5",
    "order_date": "15 Jan 2025",
    "total_amount": "21340.00",
    "payment_status": "partial"
  },
  "party": {
    "name": "Samrajya Traders",
    "contact_person": "Sagar",
    "phone": "6264888385",
    "email": "sagar@gmail.com"
  },
  "items": [
    {"product_name": "Binding Wire", "quantity": "120.00", "unit_name": "Kilogram", "unit_price": "62.00", "total_price": "7440.00"},
    {"product_name": "Barbed Wire", "quantity": "50.00", "unit_name": "Kilogram", "unit_price": "62.00", "total_price": "3100.00"},
    {"product_name": "Tarpin Oil", "quantity": "200.00", "unit_name": "Piece", "unit_price": "17.00", "total_price": "3400.00"},
    {"product_name": "Paan Belcha", "quantity": "40.00", "unit_name": "Box", "unit_price": "185.00", "total_price": "7400.00"}
  ],
  "payments": [
    {"payment_date": "15 Jan 2025", "amount": "1340.00", "payment_mode": "Cash", "reference_no": "465432154321321314", "notes": "Through the payment by Nitesh"},
    {"payment_date": "15 Jan 2025", "amount": "2000.00", "payment_mode": "Cheque", "reference_no": "02000", "notes": "Given Cheque No. Mention our Reference No. Collect by Anjum Hardware\r\n"},
    {"payment_date": "15 Jan 2025", "amount": "5000.00", "payment_mode": "Upi", "reference_no": "65432165432121", "notes": "UPI Transfer by New Number Collect by Ajay Salesman"}
  ]
}';

$data = json_decode($jsonData, true);

// Create a new PDF document
$pdf = new TCPDF('P', 'mm', 'A4');
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Company');
$pdf->SetTitle('Invoice');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Add Invoice Header
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'TAX INVOICE', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(0, 10, "ABG TRADECORP PRIVATE LIMITED\nPlot No. 28, Saraf Brothers, Near Surya Wire\nBhanpuri Industrial Area, Raipur - 492221", 0, 'L');

// Order Details
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 10, "Invoice No: {$data['order']['id']}  Date: {$data['order']['order_date']}\nPayment Status: {$data['order']['payment_status']}", 0, 1, 'L');
$pdf->Ln(5);

// Party Information
$pdf->MultiCell(90, 10, "Billed To:\n{$data['party']['name']}\nContact: {$data['party']['contact_person']}\nPhone: {$data['party']['phone']}\nEmail: {$data['party']['email']}", 0, 'L');
$pdf->Ln(5);

// Item Table Header
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(10, 10, 'S.N.', 1, 0, 'C');
$pdf->Cell(60, 10, 'Product Name', 1, 0, 'C');
$pdf->Cell(20, 10, 'Quantity', 1, 0, 'C');
$pdf->Cell(20, 10, 'Unit', 1, 0, 'C');
$pdf->Cell(20, 10, 'Unit Price', 1, 0, 'C');
$pdf->Cell(30, 10, 'Total Price', 1, 1, 'C');

// Item Rows
$pdf->SetFont('helvetica', '', 10);
$total = 0;
foreach ($data['items'] as $index => $item) {
    $pdf->Cell(10, 10, $index + 1, 1, 0, 'C');
    $pdf->Cell(60, 10, $item['product_name'], 1, 0, 'L');
    $pdf->Cell(20, 10, $item['quantity'], 1, 0, 'C');
    $pdf->Cell(20, 10, $item['unit_name'], 1, 0, 'C');
    $pdf->Cell(20, 10, $item['unit_price'], 1, 0, 'C');
    $pdf->Cell(30, 10, $item['total_price'], 1, 1, 'R');
    $total += $item['total_price'];
}

// Total Section
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(130, 10, 'Total Amount', 1, 0, 'R');
$pdf->Cell(30, 10, number_format($total, 2), 1, 1, 'R');

// Payment History
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 10, 'Payment History', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 9);
foreach ($data['payments'] as $payment) {
    $pdf->MultiCell(0, 10, "Date: {$payment['payment_date']}\nAmount: {$payment['amount']}\nMode: {$payment['payment_mode']}\nRef No: {$payment['reference_no']}\nNotes: {$payment['notes']}", 1, 'L');
}

// Output PDF
$pdf->Output('invoice.pdf', 'I');
