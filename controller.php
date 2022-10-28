<?php
session_start();

// INSERT INVOICE DETAILS

if (!empty($_POST['name'])) {

    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $tax = $_POST['tax'];
    $nontax_amt = $unit_price * $quantity;
    if ($quantity !== "" && $unit_price !== "" && $tax !== "") {
        $tax_rate = (($tax / 100) * $unit_price);
        $taxable_price = $unit_price + $tax_rate;
        $total_price = $taxable_price * $quantity;
        $tax_amt = round($total_price, 2);
    }
    $invoiceno = "INV" . rand(1, 9999);
    $array = array('name' => $name, 'quantity' => $quantity, 'unit_price' => $unit_price, 'tax' => $tax, 'nontax_amt' => $nontax_amt, 'taxable_amt' => $tax_amt);
    $_SESSION['invoice'][] = $array;
    $_SESSION['invoice_no'] = $invoiceno;
    print json_encode($array);
}

// STORE DISCOUNT
if (!empty($_GET['discount'])) {
    $_SESSION['discount'] = $_GET['discount'];
}

// DELETE DATA FROM SESSION 
if (!empty($_POST['deleteid'])) {
    $deleteid = $_POST['deleteid'];
    $index = $deleteid - 1;
    unset($_SESSION['invoice'][$index]);
    print_r($_SESSION['invoice']);
}

// DESTROY SESSION DATA
if (!empty($_GET['unset_session'])) {

    unset($_SESSION['invoice']);
    unset($_SESSION['invoice_no']);
    unset($_SESSION['discount']);
}