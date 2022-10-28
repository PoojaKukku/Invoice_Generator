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

<body>
    <div class="container mt-5">
        <div class="card">
            <div class="col-12" id="printableArea">
                <div class="row mx-5 mt-5">
                    <div class="col-md-8">
                        <p>Fingent Global Solutions</p>
                        <p>Pooja O.K</p>
                    </div>
                    <div class="col-md-4" style="text-align: right;">
                        <p>Invoice no: <b>
                                <?php if (isset($_SESSION['invoice'])) {
                                    echo $_SESSION['invoice_no'];
                                }
                                ?></b></p>
                        <p>Date: <?php echo date('d-m-y'); ?></p>
                    </div>
                </div>
                <table class="table table-responsive mt-4 mb-4">
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
                        </tr>
                    </thead>
                    <?php
                    if (isset($_SESSION['invoice'])) {
                    ?>
                    <tbody>
                        <?php
                            $count = 1;
                            $notaxtsum = 0;
                            $taxtsum = 0;
                            foreach ($_SESSION['invoice'] as $items => $values) {
                            ?>
                        <tr class="text-center">
                            <td>
                                <?php echo $count++; ?>
                            </td>
                            <td style="text-align: left;">
                                <?php echo $values['name']; ?>
                            </td>
                            <td>
                                <?php echo $values['quantity']; ?>
                            </td>
                            <td>
                                <?php echo $values['unit_price']; ?>
                            </td>
                            <td>
                                <?php echo $values['tax'] . "%"; ?>
                            </td>
                            <td>
                                <?php echo "$" . $values['taxable_amt'];
                                        $notaxtsum += $values['nontax_amt'];
                                        $taxtsum += $values['taxable_amt']; ?>
                            </td>
                        </tr>
                        <?php
                            }
                            ?>
                        <tr>
                            <td colspan="5" style="text-align: right;"><i>Non-Taxable Amount:</i></td>
                            <td class="text-center">
                                <?php echo "$" . $notaxtsum;  ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: right;"><i>Taxable Amount:</i></td>
                            <td class="text-center">
                                <b><?php echo "$"; ?><span id="tax_tot"><?php echo $taxtsum; ?></span></b>
                            </td>
                        </tr>
                        <tr id="discountinput">
                            <td colspan="4" style="text-align: right;"><i>Discount:</i></td>
                            <td colspan="2" class="text-center">
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" name='discount' id="discount" placeholder='Discount'
                                            class="form-control" />
                                    </div>
                                    <div class="col-6">
                                        <select class="form-control" name="unit" id="unit" onchange="getdiscount();">
                                            <option selected hidden>Select Unit</option>
                                            <option value="%">%</option>
                                            <option value="$">$</option>
                                        </select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr id="discount_div" style="display:none;">
                            <td colspan="5" style="text-align: right;"><i>Discount Amount:</i></td>
                            <td class="text-center">
                                <b id="discount_amt"></b>
                            </td>
                        </tr>
                        <?php if (!empty($_SESSION['discount'])) { ?>
                        <tr>
                            <td colspan="5" style="text-align: right;"><i>Discount Amount:</i></td>
                            <td style="font-weight:600;">
                                <b><?php echo "$" . $_SESSION['discount']; ?></b>
                            </td>
                        </tr>
                        <?php  } ?>
                        <tr>
                    </tbody>
                    <?php } ?>
                </table>
            </div>
            <div class="mb-3 mx-5" style="text-align: right;">
                <button class="btn btn-lg" onclick="printinvoice();"
                    style="background-color: antiquewhite; font-size: 17px;" />Print Invoice</button>
            </div>
        </div>
    </div>

    <script>
    // GET DISCOUNT
    function getdiscount() {
        var discount = parseInt(document.getElementById('discount').value);
        var unit = document.getElementById('unit').value;
        var old_price = $('#tax_tot').html();
        if (unit == '%') {
            if (discount > 100) {
                alert("Discount cannot be greater than 100%");
                $('#discount_amt').html('');
            } else {
                $('#discount_div').show();
                var discount_value = (old_price / 100) * discount;
                var discount_amt = (old_price - discount_value).toFixed(2);
                $('#discount_amt').html(discount_amt);
            }
        } else if (unit == '$') {
            $('#discount_div').show();
            var discount_amt = (old_price - discount).toFixed(2);
            $('#discount_amt').html(discount_amt);
        } else {}
        if (discount_amt) {
            $.ajax({
                type: "GET",
                contentType: "application/json",
                url: "controller.php",
                data: {
                    discount: discount_amt,
                },
                success: function(data) {}
            });
        }
    }

    // PRINT INVOICE
    function printinvoice() {
        document.getElementById('discountinput').style.display = 'none';
        var printContents = document.getElementById('printableArea').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        var done = setTimeout(function() {
            window.print();
            document.body.innerHTML = originalContents;
        }, 10);
        if (done) {
            $.ajax({
                type: "GET",
                contentType: "application/json",
                url: "controller.php",
                data: {
                    unset_session: 'true',
                },
                success: function(data) {
                    setTimeout(
                        function() {
                            //  window.location.reload();
                            window.location.href = "index.php";
                        }, 600);
                }
            });
        }
    }
    </script>
</body>

</html>