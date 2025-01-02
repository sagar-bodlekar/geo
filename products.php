<?php
session_start();
include 'config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Sorting
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Search and Filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

$where_clause = '';
if (!empty($search)) {
    $where_clause = " WHERE (name LIKE '%$search%' OR sku LIKE '%$search%')";
}
if (!empty($category_filter)) {
    $where_clause .= empty($where_clause) ? " WHERE" : " AND";
    $where_clause .= " category = '$category_filter'";
}

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total FROM products $where_clause";
$count_result = mysqli_query($conn, $count_query);
$total_records = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_records / $records_per_page);

// Get products with pagination and sorting
$query = "SELECT p.*, u.name as unit_name, u.short_name as unit_short_name 
          FROM products p
          LEFT JOIN units u ON p.unit_id = u.id
          $where_clause 
          ORDER BY $sort_column $sort_order 
          LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $query);

// Get unique categories for filter
$categories_query = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''";
$categories_result = mysqli_query($conn, $categories_query);

// Get all units
$units_query = "SELECT id, name, short_name FROM units";
$units_result = mysqli_query($conn, $units_query);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Products</h1>
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addProductModal">
                        Add Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search..." value="<?php echo $search; ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="categoryFilter">
                                <option value="">All Categories</option>
                                <?php while ($category = mysqli_fetch_assoc($categories_result)) { ?>
                                    <option value="<?php echo $category['category']; ?>" <?php echo $category_filter == $category['category'] ? 'selected' : ''; ?>>
                                        <?php echo $category['category']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="?sort=name&order=<?php echo $sort_column == 'name' && $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo $search; ?>&category=<?php echo $category_filter; ?>">
                                            Name <?php echo $sort_column == 'name' ? ($sort_order == 'ASC' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?sort=sku&order=<?php echo $sort_column == 'sku' && $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo $search; ?>&category=<?php echo $category_filter; ?>">
                                            SKU <?php echo $sort_column == 'sku' ? ($sort_order == 'ASC' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?sort=category&order=<?php echo $sort_column == 'category' && $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo $search; ?>&category=<?php echo $category_filter; ?>">
                                            Category <?php echo $sort_column == 'category' ? ($sort_order == 'ASC' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    </th>
                                    <th>Unit</th>
                                    <th>
                                        <a href="?sort=purchase_price&order=<?php echo $sort_column == 'purchase_price' && $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo $search; ?>&category=<?php echo $category_filter; ?>">
                                            Purchase Price <?php echo $sort_column == 'purchase_price' ? ($sort_order == 'ASC' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?sort=selling_price&order=<?php echo $sort_column == 'selling_price' && $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo $search; ?>&category=<?php echo $category_filter; ?>">
                                            Selling Price <?php echo $sort_column == 'selling_price' ? ($sort_order == 'ASC' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    </th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['sku']; ?></td>
                                        <td><?php echo $row['category']; ?></td>
                                        <td><?php echo $row['unit_name'] . ' (' . $row['unit_short_name'] . ')'; ?></td>
                                        <td>₹<?php echo number_format($row['purchase_price'], 2); ?></td>
                                        <td>₹<?php echo number_format($row['selling_price'], 2); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-product" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo $row['name']; ?>"
                                                    data-sku="<?php echo $row['sku']; ?>"
                                                    data-category="<?php echo $row['category']; ?>"
                                                    data-unit="<?php echo $row['unit_id']; ?>"
                                                    data-purchase="<?php echo $row['purchase_price']; ?>"
                                                    data-selling="<?php echo $row['selling_price']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-product" data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p>Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $records_per_page, $total_records); ?> of <?php echo $total_records; ?> entries</p>
                        </div>
                        <div class="col-md-6">
                            <ul class="pagination justify-content-end">
                                <?php if ($page > 1) { ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo ($page - 1); ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&search=<?php echo $search; ?>&category=<?php echo $category_filter; ?>">Previous</a>
                                    </li>
                                <?php } ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&search=<?php echo $search; ?>&category=<?php echo $category_filter; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                                
                                <?php if ($page < $total_pages) { ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo ($page + 1); ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&search=<?php echo $search; ?>&category=<?php echo $category_filter; ?>">Next</a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addProductForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text" class="form-control" name="sku" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" class="form-control" name="category">
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select class="form-control" name="unit_id" required>
                            <option value="">Select Unit</option>
                            <?php 
                            mysqli_data_seek($units_result, 0);
                            while ($unit = mysqli_fetch_assoc($units_result)) { 
                            ?>
                                <option value="<?php echo $unit['id']; ?>">
                                    <?php echo $unit['name'] . ' (' . $unit['short_name'] . ')'; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Purchase Price</label>
                        <input type="number" step="0.01" class="form-control" name="purchase_price" required>
                    </div>
                    <div class="form-group">
                        <label>Selling Price</label>
                        <input type="number" step="0.01" class="form-control" name="selling_price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editProductForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text" class="form-control" name="sku" id="edit_sku" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" class="form-control" name="category" id="edit_category">
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select class="form-control" name="unit_id" id="edit_unit" required>
                            <option value="">Select Unit</option>
                            <?php 
                            mysqli_data_seek($units_result, 0);
                            while ($unit = mysqli_fetch_assoc($units_result)) { 
                            ?>
                                <option value="<?php echo $unit['id']; ?>">
                                    <?php echo $unit['name'] . ' (' . $unit['short_name'] . ')'; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Purchase Price</label>
                        <input type="number" step="0.01" class="form-control" name="purchase_price" id="edit_purchase_price" required>
                    </div>
                    <div class="form-group">
                        <label>Selling Price</label>
                        <input type="number" step="0.01" class="form-control" name="selling_price" id="edit_selling_price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // Add Product
    $('#addProductForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'ajax/product_add.php',
            data: $(this).serialize(),
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == 'success') {
                    $('#addProductModal').modal('hide');
                    location.reload();
                } else {
                    alert(data.message);
                }
            }
        });
    });

    // Edit Product - Populate Modal
    $('.edit-product').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var sku = $(this).data('sku');
        var category = $(this).data('category');
        var unit = $(this).data('unit');
        var purchase = $(this).data('purchase');
        var selling = $(this).data('selling');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_sku').val(sku);
        $('#edit_category').val(category);
        $('#edit_unit').val(unit);
        $('#edit_purchase_price').val(purchase);
        $('#edit_selling_price').val(selling);

        $('#editProductModal').modal('show');
    });

    // Update Product
    $('#editProductForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'ajax/product_update.php',
            data: $(this).serialize(),
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == 'success') {
                    $('#editProductModal').modal('hide');
                    location.reload();
                } else {
                    alert(data.message);
                }
            }
        });
    });

    // Delete Product
    $('.delete-product').click(function() {
        if (confirm('Are you sure you want to delete this product?')) {
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: 'ajax/product_delete.php',
                data: {id: id},
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.status == 'success') {
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                }
            });
        }
    });

    // Search and Filter
    $('#searchInput, #categoryFilter').on('change', function() {
        var search = $('#searchInput').val();
        var category = $('#categoryFilter').val();
        window.location.href = '?search=' + search + '&category=' + category;
    });
});
</script> 