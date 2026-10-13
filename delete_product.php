<?php
require_once __DIR__ . '/config.php';

$id = $_GET['id'];

$query = "DELETE FROM products WHERE id='$id'";
mysqli_query($conn, $query);

header("location: manage_product.php");
exit;
?>
