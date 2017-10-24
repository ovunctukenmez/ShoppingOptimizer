<?php
require_once("ShoppingOptimizer.php");

// items to purchase
$items = array();
$items[] = array('ATX Case', 'price' => 400);
$items[] = array('Motherboard', 'price' => 400);
$items[] = array('HDD', 'price' => 250);
$items[] = array('Ram', 'price' => 750);
$items[] = array('CPU', 'price' => 850);
$items[] = array('GPU', 'price' => 1350);

// discount coupons
$coupons = array();
$coupons[] = array('70 off 600 or more', 'discount' => 70, 'min' => 600);
$coupons[] = array('75 off 750 or more', 'discount' => 75, 'min' => 750);
$coupons[] = array('150 off 1750 or more', 'discount' => 150, 'min' => 1750);
$coupons[] = array('220 off 2200 or more', 'discount' => 220, 'min' => 2200);
$coupons[] = array('25 off 200 or more', 'discount' => 25, 'min' => 200);
$coupons[] = array('75 off 800 or more', 'discount' => 75, 'min' => 800);
$coupons[] = array('110 off 1300 or more', 'discount' => 110, 'min' => 1300);
$coupons[] = array('60 off 650 or more', 'discount' => 60, 'min' => 650);
$coupons[] = array('70 off 700 or more', 'discount' => 70, 'min' => 700);
$coupons[] = array('50 off 350 or more', 'discount' => 50, 'min' => 350);
$coupons[] = array('50 off 350 or more', 'discount' => 50, 'min' => 350);
$coupons[] = array('100 off 1111 or more', 'discount' => 100, 'min' => 1111);
$coupons[] = array('40 off 300 or more', 'discount' => 40, 'min' => 300);
$coupons[] = array('50 off 325 or more', 'discount' => 50, 'min' => 325);
$coupons[] = array('125 off 1500 or more', 'discount' => 125, 'min' => 1500);
$coupons[] = array('50 off 325 or more', 'discount' => 50, 'min' => 325);
$coupons[] = array('70 off 600 or more', 'discount' => 70, 'min' => 600);
$coupons[] = array('60 off 400 or more', 'discount' => 60, 'min' => 400);
$coupons[] = array('55 off 600 or more', 'discount' => 55, 'min' => 600);
$coupons[] = array('50 off 333 or more', 'discount' => 50, 'min' => 333);
$coupons[] = array('44 off 275 or more', 'discount' => 44, 'min' => 275);
$coupons[] = array('7 off 50 or more', 'discount' => 7, 'min' => 50);
$coupons[] = array('60 off 500 or more', 'discount' => 60, 'min' => 500);
$coupons[] = array('25 off 225 or more', 'discount' => 25, 'min' => 225);
$coupons[] = array('60 off 400 or more', 'discount' => 60, 'min' => 400);
$coupons[] = array('70 off 630 or more', 'discount' => 70, 'min' => 630);
$coupons[] = array('120 off 1250 or more', 'discount' => 120, 'min' => 1250);
$coupons[] = array('7 off 35 or more', 'discount' => 7, 'min' => 35);
$coupons[] = array('60 off 375 or more', 'discount' => 60, 'min' => 375);
$coupons[] = array('100 off 1000 or more', 'discount' => 100, 'min' => 1000);
$coupons[] = array('66 off 480 or more', 'discount' => 66, 'min' => 480);
$coupons[] = array('44 off 444 or more', 'discount' => 44, 'min' => 444);
$coupons[] = array('125 off 1500 or more', 'discount' => 125, 'min' => 1500);
$coupons[] = array('77 off 750 or more', 'discount' => 77, 'min' => 750);
$coupons[] = array('100 off 1200 or more', 'discount' => 100, 'min' => 1200);
$coupons[] = array('70 off 600 or more', 'discount' => 70, 'min' => 600);
$coupons[] = array('60 off 700 or more', 'discount' => 60, 'min' => 700);
$coupons[] = array('60 off 700 or more', 'discount' => 60, 'min' => 700);
$coupons[] = array('60 off 600 or more', 'discount' => 60, 'min' => 600);
$coupons[] = array('33 off 350 or more', 'discount' => 33, 'min' => 350);

$maximum_budget = null; // ie. 1500
// if the maximum budget is set (if it's not null), getDiscountedCarts() method 
// would also return shopping cards that contain only some of items from $items array

$shoppingOptimizer = new ShoppingOptimizer($items);
$shoppingOptimizer->setCoupons($coupons);
$shoppingOptimizer->setOnlyDistinctSum(true); // whether to return only the carts with distinct sum
$shoppingOptimizer->setMaximumBudget($maximum_budget); // removeMaximumBudget() has the same effect with setMaximumBudget(null)
//$shoppingOptimizer->setCartSortOrders('discount', 'item_count'); // cart ordering priorities
// on the class initialization setDefaultCartSortOrders() method is called.
// getAvailableCartSortOrders() method can be used to see available values,
// getCartSortOrders() can be used to get current order options. 

$discounted_carts = $shoppingOptimizer->getDiscountedCarts();

// table - begin
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Example</title>
	<!-- Bootstrap -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<table>
	<tr>
		<td>
<table border="1">
	<caption>Items</caption>
	<tr>
		<th>Item</th>
		<th>Price</th>
	</tr>
<?php
foreach ($items as $item){
?>
	<tr>
		<td><?php echo $item[0]; ?></td>
		<td><?php echo $item['price']; ?></td>
	</tr>
<?php
}
?>
</table>
		</td>
		
		<td valign="top">
<table border="1">
	<caption>Coupons</caption>
	<tr>
		<th>Count</th>
	</tr>
	<tr>
		<td><?php echo count($coupons); ?></td>
	</tr>
</table>
		</td>
		
		<td valign="top">
<table border="1">
	<caption>Budget</caption>
	<tr>
		<th>Maximum</th>
	</tr>
	<tr>
		<td><?php echo ($maximum_budget == null ? '-' : $maximum_budget); ?></td>
	</tr>
</table>
		</td>
	</tr>
</table>

<table border="1">
	<caption>Discounted Cart Options (<?php echo count($discounted_carts); ?>)</caption>
	<tr>
		<th>Total Discount</th>
		<th>Cart Sum</th>
		<th>Items</th>
		<th>Part Count</th>
		<th>Parts</th>
	</tr>
<?php
$_i = 0;
foreach ($discounted_carts as $cart){
?>
	<tr style="background-color: <?php echo (++$_i % 2) == 1 ? '#a3c2b3' : '#e9ecef;'; ?>">
		<td align="center"><?php echo $cart['discount']; ?></td>
		<td align="center"><?php echo $cart['sum']; ?></td>
		<td align="center">
<table border="1">
	<caption><?php echo $cart['item_count']; ?></caption>
	<tr>
		<th>Item</th>
		<th>Price</th>
	</tr>
<?php
foreach ($cart['parts'] as $part){
	foreach ($part['items'] as $item){
?>
	<tr>
		<td><?php echo $item[0]; ?></td>
		<td><?php echo $item['price']; ?></td>
	</tr>
<?php
	}
}
?>
</table>
		</td>
		<td align="center"><?php echo count($cart['parts']); ?></td>
		<td align="center">
<?php
foreach ($cart['parts'] as $index2 => $part){
?>
<table border="1">
	<caption>Part #<?php echo $index2 + 1; ?></caption>
	<tr>
		<th>Sum</th>
		<th>Discount</th>
		<th>Items</th>
		<th></th>
	</tr>
	
	<tr>
		<td><?php echo $part['sum']; ?></td>
		<td><?php echo $part['discount']; ?></td>
		<td>
<table border="1">
	<tr>
		<th>Item</th>
		<th>Price</th>
	</tr>
<?php
foreach ($part['items'] as $item){
?>
	<tr>
		<td><?php echo $item[0]; ?></td>
		<td><?php echo $item['price']; ?></td>
	</tr>
<?php
}
?>
</table>
		</td>
		<td><?php if (count($part['coupon']) > 0){ ?>
<table border="1">
	<tr>
		<th>Coupon</th>
		<th>Discount</th>
		<th>Min.</th>
	</tr>
	<tr>
		<td><?php echo $part['coupon'][0]; ?></td>
		<td><?php echo $part['coupon']['discount']; ?></td>
		<td><?php echo $part['coupon']['min']; ?></td>
	</tr>
</table>
		<?php } ?>
		</td>
	</tr>
	
</table>
<?php
}
?>
		
		</td>
	</tr>
<?php
}
?>
</table>

</body>
</html><?php // table - end
