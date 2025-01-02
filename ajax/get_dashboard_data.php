<?php
include '../config/database.php';

// Get report range from request
$report_range = isset($_GET['range']) ? $_GET['range'] : 'day';

// Get counts
$suppliers_query = "SELECT COUNT(*) as count FROM suppliers";
$suppliers_result = mysqli_query($conn, $suppliers_query);
$suppliers_count = mysqli_fetch_assoc($suppliers_result)['count'];

$parties_query = "SELECT COUNT(*) as count FROM parties";
$parties_result = mysqli_query($conn, $parties_query);
$parties_count = mysqli_fetch_assoc($parties_result)['count'];

$products_query = "SELECT COUNT(*) as count FROM products";
$products_result = mysqli_query($conn, $products_query);
$products_count = mysqli_fetch_assoc($products_result)['count'];

$sales_query = "SELECT COUNT(*) as count FROM sales_orders";
$sales_result = mysqli_query($conn, $sales_query);
$sales_count = mysqli_fetch_assoc($sales_result)['count'];

// Get expenses data for chart
$expenses_query = "SELECT category, SUM(amount) as total 
                  FROM expenses 
                  GROUP BY category";
$expenses_result = mysqli_query($conn, $expenses_query);
$expenses_data = [];
while ($row = mysqli_fetch_assoc($expenses_result)) {
    $expenses_data[] = [
        'category' => ucfirst($row['category']),
        'total' => (float)$row['total']
    ];
}

// Get best selling products with date range
$best_selling_query = "SELECT p.name, COUNT(*) as sales_count, SUM(soi.quantity) as total_quantity
                      FROM sales_order_items soi
                      LEFT JOIN products p ON soi.product_id = p.id
                      LEFT JOIN sales_orders so ON soi.sales_order_id = so.id
                      WHERE 1=1 ";

// Add date range condition
switch($report_range) {
    case 'week':
        $best_selling_query .= "AND so.order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'month':
        $best_selling_query .= "AND so.order_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        break;
    default: // day
        $best_selling_query .= "AND DATE(so.order_date) = CURDATE()";
}

$best_selling_query .= " GROUP BY soi.product_id
                        ORDER BY sales_count DESC
                        LIMIT 5";

$best_selling_result = mysqli_query($conn, $best_selling_query);
$best_selling_data = [];
while ($row = mysqli_fetch_assoc($best_selling_result)) {
    $best_selling_data[] = [
        'product' => $row['name'],
        'quantity' => (int)$row['total_quantity']
    ];
}

// Modify monthly data query based on range
$query = "";
switch($report_range) {
    case 'day':
        $query = "
            WITH RECURSIVE dates AS (
                SELECT CURDATE() - INTERVAL 6 DAY AS date
                UNION ALL
                SELECT date + INTERVAL 1 DAY
                FROM dates
                WHERE date < CURDATE()
            )
            SELECT 
                DATE_FORMAT(d.date, '%d %M') as label,
                COALESCE(SUM(s.total_amount), 0) as sales_amount,
                COALESCE(SUM(p.total_amount), 0) as purchase_amount
            FROM dates d
            LEFT JOIN sales_orders s ON DATE(s.order_date) = d.date
            LEFT JOIN purchase_orders p ON DATE(p.order_date) = d.date
            GROUP BY d.date
            ORDER BY d.date ASC";
        break;

    case 'week':
        $query = "
            WITH RECURSIVE weeks AS (
                SELECT 
                    DATE_SUB(CURDATE(), INTERVAL (WEEKDAY(CURDATE()) + 7 * 3) DAY) as start_date,
                    DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY) as end_date
                UNION ALL
                SELECT 
                    DATE_ADD(start_date, INTERVAL 7 DAY),
                    DATE_ADD(end_date, INTERVAL 7 DAY)
                FROM weeks
                WHERE start_date < CURDATE() - INTERVAL WEEKDAY(CURDATE()) DAY
            )
            SELECT 
                CONCAT('Week ', WEEK(w.start_date), ' (', DATE_FORMAT(w.start_date, '%d %b'), ' - ', DATE_FORMAT(w.end_date, '%d %b'), ')') as label,
                COALESCE(SUM(s.total_amount), 0) as sales_amount,
                COALESCE(SUM(p.total_amount), 0) as purchase_amount
            FROM weeks w
            LEFT JOIN sales_orders s ON DATE(s.order_date) BETWEEN w.start_date AND w.end_date
            LEFT JOIN purchase_orders p ON DATE(p.order_date) BETWEEN w.start_date AND w.end_date
            GROUP BY w.start_date, w.end_date
            ORDER BY w.start_date ASC
            LIMIT 4";
        break;

    case 'month':
        $query = "
            WITH RECURSIVE months AS (
                SELECT 
                    DATE_SUB(DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY), INTERVAL 5 MONTH) as date
                UNION ALL
                SELECT DATE_ADD(date, INTERVAL 1 MONTH)
                FROM months
                WHERE date < DATE_SUB(CURDATE(), INTERVAL DAYOFMONTH(CURDATE())-1 DAY)
            )
            SELECT 
                DATE_FORMAT(m.date, '%M %Y') as label,
                COALESCE(SUM(s.total_amount), 0) as sales_amount,
                COALESCE(SUM(p.total_amount), 0) as purchase_amount
            FROM months m
            LEFT JOIN sales_orders s ON 
                YEAR(s.order_date) = YEAR(m.date) AND 
                MONTH(s.order_date) = MONTH(m.date)
            LEFT JOIN purchase_orders p ON 
                YEAR(p.order_date) = YEAR(m.date) AND 
                MONTH(p.order_date) = MONTH(m.date)
            GROUP BY m.date
            ORDER BY m.date ASC";
        break;
}

$monthly_data_result = mysqli_query($conn, $query);
if (!$monthly_data_result) {
    error_log("Query Error: " . mysqli_error($conn));
    error_log("Query: " . $query);
}

$monthly_data = [];
while ($row = mysqli_fetch_assoc($monthly_data_result)) {
    $monthly_data[] = [
        'label' => $row['label'],
        'sales' => (float)$row['sales_amount'],
        'purchase' => (float)$row['purchase_amount']
    ];
}

// Get today's overview
$today_sales_query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM sales_orders 
                     WHERE DATE(created_at) = CURDATE()";
$today_sales_result = mysqli_query($conn, $today_sales_query);
$today_sales = mysqli_fetch_assoc($today_sales_result)['total'];

$today_purchase_query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                        FROM purchase_orders 
                        WHERE DATE(created_at) = CURDATE()";
$today_purchase_result = mysqli_query($conn, $today_purchase_query);
$today_purchase = mysqli_fetch_assoc($today_purchase_result)['total'];

// Get total sales and purchase
$total_sales_query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                     FROM sales_orders";
$total_sales_result = mysqli_query($conn, $total_sales_query);
$total_sales = mysqli_fetch_assoc($total_sales_result)['total'];

$total_purchase_query = "SELECT COALESCE(SUM(total_amount), 0) as total 
                        FROM purchase_orders";
$total_purchase_result = mysqli_query($conn, $total_purchase_query);
$total_purchase = mysqli_fetch_assoc($total_purchase_result)['total'];

// Return all data as JSON
$response = [
    'status' => 'success',
    'data' => [
        'counts' => [
            'suppliers' => $suppliers_count,
            'parties' => $parties_count,
            'products' => $products_count,
            'sales' => $sales_count
        ],
        'expenses' => $expenses_data,
        'best_selling' => $best_selling_data,
        'monthly_data' => $monthly_data,
        'today' => [
            'sales' => $today_sales,
            'purchase' => $today_purchase,
            'total_sales' => $total_sales,
            'total_purchase' => $total_purchase
        ],
        'range' => $report_range
    ]
];

echo json_encode($response); 