<?php
/**
 * @author Ovunc Tukenmez <ovunct@live.com>
 * version 1.0.0 - 10/25/2017
 *
 * This class is used to find the most profitable ways to purchase items using 
 * cart discount coupons in one or multiple cart parts.
 */
class ShoppingOptimizer
{
	private $_original_items = array();
	private $_items = array();
	private $_temp_items = array();
	private $_coupons = array();
	private $_max_budget = null;
	private $_discounted_carts = array();
	private $_only_distinct_sum = false;
	private $_cart_sort_orders = array();
	private $_available_cart_sort_orders = array();
	
	public function __construct($items)
	{
		$this->_original_items = $items;
		$this->setItems($items);
		$this->setTempItems($items);
		$this->_available_cart_sort_orders = array('discount', 'parts', 'item_count', 'sum');
		$this->setDefaultCartSortOrders();
	}
	
	public function setItems($items){
		$this->_items = $items;
	}
	
	private function setTempItems($items){
		$this->_temp_items = $items;
	}
	
	private function resetTempItems(){
		$this->_temp_items = $this->_items;
	}
	
	public function resetItems(){
		$this->_items = $this->_original_items;
		$this->_temp_items = $this->_items;
	}
	
	public function setCoupons($coupons){
		$this->_coupons = $this->getSortedCoupons($coupons);
	}
	
	public function getCoupons(){
		return $this->_coupons;
	}
	
	public function setMaximumBudget($budget){
		$this->_max_budget = $budget;
	}
	
	public function removeMaximumBudget(){
		$this->_max_budget = null;
	}
	
	public function setOnlyDistinctSum($bool_value){
		$this->_only_distinct_sum = $bool_value;
	}
	
	public function setCartSortOrders($sort_order_array){
		$sort_order_array = array_intersect($sort_order_array, $this->_available_cart_sort_orders);
		
		$this->_cart_sort_orders = $sort_order_array;
	}
	
	public function setDefaultCartSortOrders(){
		$sort_orders = array();
		$sort_orders[] = 'discount';
		$sort_orders[] = 'item_count';
		$sort_orders[] = 'parts';
		
		$this->setCartSortOrders($sort_orders);
	}
	
	public function getAvailableCartSortOrders(){
		return $this->_cart_sort_orders;
	}
	
	public function getCartSortOrders(){
		return $this->_cart_sort_orders;
	}
	
	private function getDifferentPurchaseWays($item_count){
		$purchase_ways = array();
		
		for ($i = $item_count; $i > 0; $i--){
			$remaining_items = $item_count - $i;
			
			if ($remaining_items > 0){
				$sub_parts = $this->getDifferentPurchaseWays($remaining_items);

				foreach ($sub_parts as $sub_part){
					$parts = array();
					$parts[] = $i;
					
					foreach ($sub_part as $p){
						$parts[] = $p;
					}
					
					sort($parts);
					if (!in_array($parts, $purchase_ways)){
						$purchase_ways[] = $parts;
					}
				}
			}
			else{
				$purchase_ways[] = array($i);
			}
		}
		
		return $purchase_ways;
	}
	
	private function getPossibleCarts($items_per_part){
		$carts = array();
		
		foreach ($this->x_calculatePossibleCarts($items_per_part) as $value){
			$carts[] = $value;
		}
		
		return $carts;
	}
	
	private function x_calculatePossibleCarts($remaining_parts = array(), $elements = array()){
		
		$items_per_part = array_shift($remaining_parts);
		
		$remaining_items = array();
		foreach ($this->_items as $key => $value){
			if (in_array($value, $elements)){ continue; }
			
			$remaining_items[] = $value;
		}
		$this->setTempItems($remaining_items);
		
		if ($items_per_part == 1){
			$element = array_shift($remaining_items);
			$elements[] = $element;
			
			while(array_shift($remaining_parts) == 1){
				$elements[] = array_shift($remaining_items);
			}
			
			yield $elements;
		}
		else{
			foreach ($this->x_calculateCombinations($items_per_part) as $value){
				
				foreach ($value as $item_key => $item_value){
					$elements[] = $item_value;
				}
				
				if (count($remaining_parts) == 0){
					yield $elements;
				}
				else{
					foreach ($this->x_calculatePossibleCarts($remaining_parts, $elements) as $value2){
						yield $value2;
					}
				}
				
				foreach ($value as $item_key => $item_value){
					array_pop($elements);
				}
				
				$this->setTempItems($remaining_items);
			}
		}
	}
	
	private function x_calculateCombinations($length = 1, $position = 0, $elements = array()){
		
		$items_count = count($this->_temp_items);
		
		for ($i = $position; $i < $items_count; $i++){
			
			$elements[] = $this->_temp_items[$i];
			
			if (count($elements) == $length){
				yield $elements;
			}
			else{
				foreach ($this->x_calculateCombinations($length, $i + 1, $elements) as $value2){
					yield $value2;
				}
			}
			
			array_pop($elements);
		}
	}
	
	public function getDiscountedCarts(){
		$discounted_carts = array();
		
		$i3_start = ($this->_max_budget > 0 ? 1 : count($this->_items));
		
		$i3_end = count($this->_items);
		
		for ($i3 = $i3_start; $i3 <= $i3_end; $i3++){
			
			$purchase_ways = $this->getDifferentPurchaseWays($i3);
			
			foreach ($this->x_calculateCombinations($i3) as $value){
				$this->setItems($value);
				
				$sums = array();
				
				foreach ($purchase_ways as $key => $cart_parts){
					$possible_carts = $this->getPossibleCarts(array_reverse($cart_parts));
					
					foreach ($possible_carts as $possible_cart){
						$cart_sum = 0;
						$cart_discount = 0;
						
						$discounted_cart = array();
						$discounted_cart_parts = array();
						
						$_coupons = $this->getCoupons();
						
						$offset = 0;
						for ($i = 0; $i < count($cart_parts); $i++){
							$_part = array();
							$_part_items = array_slice($possible_cart, $offset, $cart_parts[$i]);
							$_part_sum = 0;
							$_part_discount = 0;
							$_part_coupon = array();
							$offset += $cart_parts[$i];
							
							foreach ($_part_items as $_item){
								$_part_sum += $_item['price'];
							}
							
							foreach ($_coupons as $key => $coupon){
								if ($coupon['min'] <= $_part_sum){
									$_part_coupon = $coupon;
									$_part_discount = $coupon['discount'];
									$_part_sum -= $coupon['discount'];
									unset($_coupons[$key]);
									break;
								}
							}
							
							$_part['items'] = $_part_items;
							$_part['sum'] = $_part_sum;
							$_part['coupon'] = $_part_coupon;
							$_part['discount'] = $_part_discount;
							
							$cart_sum += $_part_sum;
							$cart_discount += $_part_discount;
							
							$discounted_cart_parts[] = $_part;
						}
						
						if ($cart_discount == 0){
							continue;
						}
						
						if ($this->_only_distinct_sum == true){
							if (in_array($cart_sum, $sums)){
								continue;
							}
							else{
								$sums[] = $cart_sum;
							}
						}
						
						$discounted_cart['parts'] = $discounted_cart_parts;
						$discounted_cart['sum'] = $cart_sum;
						$discounted_cart['discount'] = $cart_discount;
						$discounted_cart['item_count'] = $i3;
						
						$discounted_carts[] = $discounted_cart;
					}
				}
				
				$this->resetItems();
			}
		}
		
		if ($this->_max_budget > 0){
			$discounted_carts = array_filter($discounted_carts, [$this, '_filter_carts']);
		}
		
		// sort
		uasort($discounted_carts, [$this, '_cmp_carts']);
		
		return $discounted_carts;
	}
	
	private function _filter_carts($var) {
		return ($var['sum'] <= $this->_max_budget);
	}
	
	private function _cmp_carts($a, $b) {
		$sort_orders = array();
		$sort_orders = $this->_cart_sort_orders;
		
		for ($i=0; $i<count($sort_orders); $i++){
			$sort_order = $sort_orders[$i];
			$value_a = ($sort_order == 'parts' ? count($a[$sort_order]) : $a[$sort_order]);
			$value_b = ($sort_order == 'parts' ? count($b[$sort_order]) : $b[$sort_order]);
			
			if ($value_a == $b){
				continue;
			}
			
			switch ($sort_order){
				case 'discount':
					return ($value_a > $value_b) ? -1 : 1;
				case 'parts':
					return ($value_a < $value_b) ? -1 : 1;
				case 'item_count':
					return ($value_a > $value_b) ? -1 : 1;
				case 'sum':
					return ($value_a < $value_b) ? -1 : 1;
			}
		}
		
		return 0;
	}
	
	private function getSortedCoupons($coupons)
	{
		uasort($coupons, [$this, '_cmp_coupons']);
		return $coupons;
	}
	
	private function _cmp_coupons($a, $b) {
		if ($a['discount'] == $b['discount']) {
			if ($a['min'] > $b['min']){
				return -1;
			}
			elseif ($a['min'] < $b['min']){
				return 1;
			}
			return 0;
		}
		return ($a['discount'] > $b['discount']) ? -1 : 1;
	}
	
}
