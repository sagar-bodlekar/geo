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
$where_clause = '';
if (!empty($search)) {
    $where_clause = " WHERE name LIKE '%$search%' OR contact_person LIKE '%$search%' OR phone LIKE '%$search%' OR email LIKE '%$search%'";
}

// Get total records for pagination
$total_query = "SELECT COUNT(*) as count FROM parties" . $where_clause;
$total_result = mysqli_query($conn, $total_query);
$total_records = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_records / $records_per_page);

// Get parties with pagination, sorting, and filtering
$query = "SELECT * FROM parties" . $where_clause . 
         " ORDER BY $sort_column $sort_order LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $query);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Parties</h1>
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
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPartyModal">
                                <i class="fas fa-plus"></i> Add Party
                            </button>
                        </div>
                        <div class="col-md-6">
                            <form action="" method="GET" class="form-inline float-right">
                                <input type="text" name="search" class="form-control mr-2" placeholder="Search..." value="<?php echo $search; ?>">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <a href="?sort=name&order=<?php echo $sort_column == 'name' && $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo $search; ?>">
                                            Name <?php echo $sort_column == 'name' ? ($sort_order == 'ASC' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?sort=contact_person&order=<?php echo $sort_column == 'contact_person' && $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo $search; ?>">
                                            Contact Person <?php echo $sort_column == 'contact_person' ? ($sort_order == 'ASC' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?sort=phone&order=<?php echo $sort_column == 'phone' && $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo $search; ?>">
                                            Phone <?php echo $sort_column == 'phone' ? ($sort_order == 'ASC' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    </th>
                                    <th>
                                        <a href="?sort=email&order=<?php echo $sort_column == 'email' && $sort_order == 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo $search; ?>">
                                            Email <?php echo $sort_column == 'email' ? ($sort_order == 'ASC' ? '↑' : '↓') : ''; ?>
                                        </a>
                                    </th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['contact_person']; ?></td>
                                        <td><?php echo $row['phone']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td><?php echo $row['address']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-party" 
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo $row['name']; ?>"
                                                    data-contact="<?php echo $row['contact_person']; ?>"
                                                    data-phone="<?php echo $row['phone']; ?>"
                                                    data-email="<?php echo $row['email']; ?>"
                                                    data-address="<?php echo $row['address']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-party" data-id="<?php echo $row['id']; ?>">
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
                                        <a class="page-link" href="?page=<?php echo ($page - 1); ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&search=<?php echo $search; ?>">Previous</a>
                                    </li>
                                <?php } ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php } ?>
                                
                                <?php if ($page < $total_pages) { ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo ($page + 1); ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&search=<?php echo $search; ?>">Next</a>
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

<!-- Add Party Modal -->
<div class="modal fade" id="addPartyModal" tabindex="-1" role="dialog" aria-labelledby="addPartyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPartyModalLabel">Add Party</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addPartyForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" class="form-control" name="contact_person">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" rows="3"></textarea>
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

<!-- Edit Party Modal -->
<div class="modal fade" id="editPartyModal" tabindex="-1" role="dialog" aria-labelledby="editPartyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPartyModalLabel">Edit Party</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPartyForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="form-group">
                        <label>Contact Person</label>
                        <input type="text" class="form-control" name="contact_person" id="edit_contact_person">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" id="edit_phone">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" id="edit_email">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" id="edit_address" rows="3"></textarea>
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
    // Add Party
    $('#addPartyForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'ajax/party_add.php',
            data: $(this).serialize(),
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == 'success') {
                    $('#addPartyModal').modal('hide');
                    location.reload();
                } else {
                    alert(data.message);
                }
            }
        });
    });

    // Edit Party - Populate Modal
    $('.edit-party').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var contact = $(this).data('contact');
        var phone = $(this).data('phone');
        var email = $(this).data('email');
        var address = $(this).data('address');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_contact_person').val(contact);
        $('#edit_phone').val(phone);
        $('#edit_email').val(email);
        $('#edit_address').val(address);

        $('#editPartyModal').modal('show');
    });

    // Update Party
    $('#editPartyForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'ajax/party_update.php',
            data: $(this).serialize(),
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status == 'success') {
                    $('#editPartyModal').modal('hide');
                    location.reload();
                } else {
                    alert(data.message);
                }
            }
        });
    });

    // Delete Party
    $('.delete-party').click(function() {
        if (confirm('Are you sure you want to delete this party?')) {
            var id = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: 'ajax/party_delete.php',
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
});
</script> 