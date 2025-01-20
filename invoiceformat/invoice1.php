<?php
session_start();
require '../vendor/autoload.php';
require_once('../vendor/tecnickcom/tcpdf/tcpdf.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!isset($_GET['orderid'])) {
        throw new Exception('orderid not found');
    }
    echo "Testing Promise Only!!" . $_GET['orderid'];
    $pdf = new TCPDF('P', 'mm', 'A4');

    //add page
    $pdf->AddPage();


    $pdf->Cell(190,10,"This is first cell",1,1,'C');
    //code...
    $pdf->writeHTMLCell(190,0,'','','This is first type of the sentances',1,1);
} catch (\Throwable $th) {
    //throw $th;
    die('Error: ' . $th->getMessage());
}
?>
<html>

<head>
    <title>Simple invoice in PHP</title>
    <style type="text/css">
        body {
            font-family: Verdana;
        }

        div.invoice {
            border: 1px solid #ccc;
            padding: 10px;
            height: 740pt;
            width: 570pt;
        }

        div.company-address {
            border: 1px solid #ccc;
            float: left;
            width: 200pt;
        }

        div.invoice-details {
            border: 1px solid #ccc;
            float: right;
            width: 200pt;
        }

        div.customer-address {
            border: 1px solid #ccc;
            float: right;
            margin-bottom: 50px;
            margin-top: 100px;
            width: 200pt;
        }

        div.clear-fix {
            clear: both;
            float: none;
        }

        table {
            width: 100%;
        }

        th {
            text-align: left;
        }

        td {}

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="invoice">
        <div class="company-address">
            ACME ltd
            <br />
            489 Road Street
            <br />
            London, AF3Z 7BP
            <br />
        </div>

        <div class="invoice-details">
            Invoice N°: 564
            <br />
            Date: 24/01/2012
        </div>

        <div class="customer-address">
            To:
            <br />
            Mr. Bill Terence
            <br />
            123 Long Street
            <br />
            London, DC3P F3Z
            <br />
        </div>

        <div class="clear-fix"></div>
        <table border='1' cellspacing='0'>
            <tr>
                <th width=250>Description</th>
                <th width=80>Amount</th>
                <th width=100>Unit price</th>
                <th width=100>Total price</th>
            </tr>

            <?php
            $total = 0;
            $vat = 21;

            $articles = array(
                array("Motherboard", "Case", "RAM", "Hard Disk", "Monitor", "Installation"),
                array(1, 1, 2, 2, 1, 1),
                array(65, 80, 70, 125, 210, 30)
            );

            for ($a = 0; $a < 5; $a++) {
                $description = $articles[0][$a];
                $amount = $articles[1][$a];
                $unit_price = number_format($articles[2][$a], 2);
                $total_price = number_format($amount * $unit_price, 2);
                $total += $total_price;
                echo ("<tr>");
                echo ("<td>$description</td>");
                echo ("<td class='text-center'>$amount</td>");
                echo ("<td class='text-right'>€$unit_price</td>");
                echo ("<td class='text-right'>€$total_price</td>");
                echo ("</tr>");
            }

            echo ("<tr>");
            echo ("<td colspan='3' class='text-right'>Sub total</td>");
            echo ("<td class='text-right'>€" . number_format($total, 2) . "</td>");
            echo ("</tr>");
            echo ("<tr>");
            echo ("<td colspan='3' class='text-right'>VAT</td>");
            echo ("<td class='text-right'>€" . number_format(($total * $vat) / 100, 2) . "</td>");
            echo ("</tr>");
            echo ("<tr>");
            echo ("<td colspan='3' class='text-right'><b>TOTAL</b></td>");
            echo ("<td class='text-right'><b>€" . number_format(((($total * $vat) / 100) + $total), 2) . "</b></td>");
            echo ("</tr>");
            //output
            $pdf->Output();
            ?>
        </table>
    </div>
</body>

<!-- jquery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- jspdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
<!-- canvas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


</html>