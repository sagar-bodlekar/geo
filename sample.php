<tr>
    <td>
        <select class="form-control product-select" name="items[0][product_id]" required>
            <option value="">Select Product</option>
            <?php
            mysqli_data_seek($products_result, 0);
            while ($product = mysqli_fetch_assoc($products_result)) {
            ?>
                <option value="<?php echo $product['id']; ?>"
                    data-price="<?php echo $product['selling_price']; ?>"
                    data-unit-id="<?php echo $product['unit_id']; ?>">
                    <?php echo $product['name'] . ' (' . $product['sku'] . ')'; ?>
                </option>
            <?php } ?>
        </select>
    </td>
    <td>
        <input type="number" step="0.01" class="form-control quantity" name="items[0][quantity]" required>
    </td>
    <td>
        <select class="form-control unit-select" name="items[0][unit_id]" required>
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
    </td>
    <td>
        <input type="number" step="0.01" class="form-control price" name="items[0][unit_price]" required>
    </td>
    <td>
        <input type="number" step="0.01" class="form-control total" name="items[0][total_price]" readonly>
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
    </td>
</tr>






<tr>
    <td>
        <select class="form-control product-select" name="items[0][product_id]" required>
            <option value="">Select Product</option>
            <?php
            mysqli_data_seek($products_result, 0);
            while ($product = mysqli_fetch_assoc($products_result)) {
            ?>
                <option value="<?php echo $product['id']; ?>"
                    data-price="<?php echo $product['selling_price']; ?>"
                    data-unit-id="<?php echo $product['unit_id']; ?>">
                    <?php echo $product['name'] . ' (' . $product['sku'] . ')'; ?>
                </option>
            <?php } ?>
        </select>
    </td>
    <td>
        <input type="number" step="0.01" class="form-control quantity" name="items[0][quantity]" required>
    </td>
    <td>
        <select class="form-control unit-select" name="items[0][unit_id]" required>
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
    </td>
    <td>
        <input type="number" step="0.01" class="form-control price" name="items[0][unit_price]" required>
    </td>
    <td>
        <input type="number" step="0.01" class="form-control total" name="items[0][total_price]" readonly>
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fas fa-trash"></i></button>
    </td>
</tr>