<?php
if (!session_id()) {
    session_start();
}
?>
<html>

<head>
    <title>Invoice Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>

<body id="reload-wrapper">
    <div class=" container mt-5">
        <div class="card">
            <div style="padding: 40px;">
                <h4 class="title mb-4">Invoice Generator</h4>
                <form action="" method="POST" id="invoiceform">
                    <table id="myTable" class="table order-list">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    #
                                </th>
                                <th class="text-center">
                                    Name
                                </th>
                                <th class="text-center">
                                    Quantity
                                </th>
                                <th class="text-center">
                                    Unit Price
                                </th>
                                <th class="text-center">
                                    Tax
                                </th>
                                <th class="text-center">
                                    Total
                                </th>
                                <th> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id='addr0'>
                                <td>
                                    1
                                </td>
                                <td>
                                    <input type=" text" name='name' placeholder='Item Name' class="form-control" />
                                </td>
                                <td>
                                    <input type="text" name='quantity' placeholder='Quantity' class="form-control"
                                        id="quantity1" onkeyup="gettotal('1');" />
                                </td>
                                <td>
                                    <input type="text" name='unit_price' placeholder='Price' class="form-control"
                                        id="unit_price1" onkeyup="gettotal('1');" />
                                </td>
                                <td>
                                    <select class="form-control" name="tax[]" id="tax1" onchange="gettotal('1');">
                                        <option selected hidden value="">Select Tax</option>
                                        <option value="0">0%</option>
                                        <option value="1">1%</option>
                                        <option value="5">5%</option>
                                        <option value="10">10%</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name='taxable_total' placeholder='Total' class="form-control"
                                        id="taxable_total1" onkeyup="gettotal();" />
                                </td>
                                <td><a class="deleteRow"></a></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: left;">
                                    <input type="button" name="submit" class="btn btn-lg " id="addrow"
                                        value="Save & Add Row" onclick="storeformdata();"
                                        style="background-color: antiquewhite; font-size: 17px;" />
                                </td>
                                <td colspan="3" style="text-align: right;">
                                    <input type="button" class="btn btn-lg" value="Generate Invoice"
                                        onclick="printinvoice();"
                                        style="background-color: antiquewhite; font-size: 17px;" />
                                </td>
                            </tr>
                            <tr>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <?php
    unset($_SESSION['invoice']);
    unset($_SESSION['invoice_no']);
    unset($_SESSION['discount']);
    ?>
    <script>
    function printinvoice() {
        window.location.href = "print.php";
    }

    // GET ROW TOTAL
    function gettotal(id) {
        var quantity = parseInt(document.getElementById('quantity' + id).value);
        var unit_price = parseInt(document.getElementById('unit_price' + id).value);
        var tax = document.getElementById('tax' + id).value;

        if (quantity !== "" && unit_price !== "" && tax !== "") {
            var tax_rate = ((tax / 100) * unit_price);
            var taxable_price = unit_price + tax_rate;
            var total_price = taxable_price * quantity;
            if (total_price) {
                document.getElementById('taxable_total' + id).value = total_price.toFixed(2);
            } else {
                document.getElementById('taxable_total' + id).value = "0.00";
            }
        }
    }

    // DELETE DATA
    function deleterow(id) {

        $.ajax({
            type: "POST",
            url: "controller.php",
            data: {
                deleteid: id
            },
            success: function(data) {
                alert(data)
            },
        });
    }
    </script>


    <script>
    // STORE FORM DATA
    function storeformdata() {

        $("form#invoiceform").find('input[name^="name"]').each(function() {
            name = $(this).val();
        });
        $("form#invoiceform").find('input[name^="quantity"]').each(function() {
            quantity = parseInt($(this).val());
        });
        $("form#invoiceform").find('input[name^="unit_price"]').each(function() {
            unit_price = parseInt($(this).val());
        });
        $("form#invoiceform").find('select[name^="tax"]').each(function() {
            tax = $(this).val();
        });

        if ($('#discount_amt').html()) {
            var discount = $('#discount_amt').html();
        } else {
            var discount = "";
        }

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "controller.php",
            data: {
                name: name,
                quantity: quantity,
                unit_price: unit_price,
                tax: tax,
                discount: discount
            },
            success: function(data) {},
        });
    }
    </script>

    <script>
    //  DYNAMIC TABLE
    $(document).ready(function() {
        var counter = 0;
        var i = 2;
        $("#addrow").on("click", function() {
            var newRow = $("<tr id='addr'" + i + "'>");
            var cols = "";
            cols += '<td>' + i + '</td>';
            cols +=
                '<td><input type="text" class="form-control" name="name" placeholder="Item Name"/></td>';
            cols +=
                '<td><input type="text" class="form-control" name="quantity" placeholder="Quantity" id="quantity' +
                i + '" onkeyup="gettotal(' + i + ');' +
                counter + '"/></td>';
            cols +=
                '<td><input type="text" class="form-control" name="unit_price" placeholder="Price" id="unit_price' +
                i + '"onkeyup="gettotal(' + i + ');' +
                counter + '"/></td>';
            cols +=
                '<td><select class="form-control" name="tax" id="tax' + i + '" onchange="gettotal(' +
                i + ');"> <option selected hidden > Select Tax </option>' +
                counter +
                '"<option value = "0" > 0 % </option> <option value = "1" > 1 % </option> <option value = "5" > 5 % </option> <option value = "10" > 10 % </option></select></td > ';
            cols +=
                '<td><input type="text" name="taxable_total" placeholder="Total" class="form-control" id ="taxable_total' +
                i + '"/> </td>';
            cols +=
                '<td><input type="button" class="ibtnDel btn btn-md btn-danger" onclick="deleterow(' +
                i + ')"  value="Delete"></td>';
            newRow.append(cols);
            $("table.order-list").append(newRow);
            i++
            counter++;
        });

        $("table.order-list").on("click", ".ibtnDel", function(event) {
            $(this).closest("tr").remove();
            counter -= 1
        });
    });
    </script>


</body>

</html>