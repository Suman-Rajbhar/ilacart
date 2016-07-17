[![Latest Stable Version](https://poser.pugx.org/lutforrahman/ilacart/v/stable)](https://packagist.org/packages/lutforrahman/ilacart)
[![Total Downloads](https://poser.pugx.org/lutforrahman/ilacart/downloads)](https://packagist.org/packages/lutforrahman/ilacart)
[![Latest Unstable Version](https://poser.pugx.org/lutforrahman/ilacart/v/unstable)](https://packagist.org/packages/lutforrahman/ilacart)
[![License](https://poser.pugx.org/lutforrahman/ilacart/license)](https://packagist.org/packages/lutforrahman/ilacart)


# iLaCart The Laravel Shoppingcart

A simple shoppingcart implementation for Laravel >=5.

## Installation

Install the package through [Composer](http://getcomposer.org/). Edit your project's `composer.json` file by adding:


### Laravel 5

```php
"require": {
	"laravel/framework": "5.0.*",
	"lutforrahman/ilacart": "1.0.3"
}
```

Next, run the Composer update command from the Terminal:

    composer update
	
	OR
	
	composer require lutforrahman/ilacart

Now all you have to do is add the service provider of the package and alias the package. To do this open your `app/config/app.php` file.

Add a new line to the `service providers` array:

	Lutforrahman\iLaCart\ShoppingcartServiceProvider::class

And finally add a new line to the `aliases` array:

	'Cart'            => Lutforrahman\iLaCart\Facades\Cart::class,

Now you're ready to start using the shoppingcart in your application.

## Documentation

Look at one of the following topics to learn more about iLaCart


### Add to Cart

The shoppingcart gives you the following methods to use:


**Cart::insert()**

```php
	/**
     * Add a row to the cart
     * @param string|array $id Unique ID of the item|Item formated as array|Array of items
     * @param string $name Name of the item
     * @param string $slug Slug of the item
     * @param string $image Image of the item
     * @param int $quantity Item quantity to add to the cart
     * @param float $price Price of one item
     * @param float $discount Discount amount of one item
     * @param float $tax Tax amount of one item
     * @param array $options Array of additional options, such as 'size' or 'color'
     */
 

// Basic form
Cart::insert('101', 'Product name', 'product-name', 'uploads/product-thumbnail.jpg', 1, 9.99, 0.00, 0.00, array('size' => 'large', 'color' => 'white'));

// Array form
Cart::insert(array('id' => '101', 'name' => 'Product name', 'slug' => 'product-name', 'image' => 'uploads/product-thumbnail.jpg', 'quantity' => 1, 'price' => 9.99, 'discount' => 0.00, 'tax' => 0.00, 'options' => array('size' => 'large')));

OR

$product = Product::find($id);
$item = [
	'id' => $product->id,
	'name' => $product->name,
	'slug' => $product->slug,
	'image' => $product->thumbnail,
	'quantity' => $quantity > 0 ? $quantity : 1,
	'price' => $product->price,
	'discount' => $product->discount_amount,
	'tax' => 0,
	'options' => array('size' => 'XL', 'color' => 'Red')
];
Cart::insert($item);
		
		
// Batch method
Cart::insert(array(
  array('id' => '101', 'name' => 'Product name', 'slug' => 'product-name', 'image' => 'uploads/product-thumbnail.jpg', 'quantity' => 1, 'price' => 9.99, 'discount' => 0.00, 'tax' => 0.00,),
  array('id' => '102', 'name' => 'Product name 2', 'slug' => 'product-name-2', 'image' => 'uploads/product-thumbnail-2.jpg', 'quantity' => 1, 'price' => 9.99, 'discount' => 0.00, 'tax' => 0.00,  'options' => array('size' => 'large', 'color' => 'white'))
));


OR

$product = Product::find($id);
$item = [
	'id' => $product->id,
	'name' => $product->name,
	'slug' => $product->slug,
	'image' => $product->thumbnail,
	'quantity' => $quantity > 0 ? $quantity : 1,
	'price' => $product->price,
	'discount' => $product->discount_amount,
	'tax' => 0,
	'options' => array('size' => 'XL', 'color' => 'Red')
];

$product2 = Product::find($id2);
$item2 = [
	'id' => $product2->id,
	'name' => $product2->name,
	'slug' => $product2->slug,
	'image' => $product2->thumbnail,
	'quantity' => $product2 > 0 ? $quantity : 1,
	'price' => $product2->price,
	'discount' => $product2->discount_amount,
	'tax' => 0,
	'options' => array('size' => 'XL', 'color' => 'Red')
];

Cart::insert(array($item, $item2));
		
	
```


### Update Cart


**Cart::update()**

```php
/**
 * Update the quantity of one row of the cart
 *
 * @param  string        $rowId       The rowid of the item you want to update
 * @param  integer|Array $attribute   New quantity of the item|Array of attributes to update
 * @return boolean
 */
 $rowId = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

Cart::update($rowId, 2);

OR

Cart::update($rowId, array('name' => 'Product name'));

OR

$product = Product::find($id);

$item = [
	'id' => $product->id,
	'name' => $product->name,
	'slug' => $product->slug,
	'image' => $product->thumbnail,
	'quantity' => $quantity > 0 ? $quantity : 1,
	'price' => $product->price,
	'discount' => $product->discount_amount,
	'tax' => 0,
	'options' => array('size' => 'XL', 'color' => 'Red')
];
Cart::update($rowId, $item);
		
```

### Remove an Item from Cart


**Cart::remove()**

```php
/**
 * Remove a row from the cart
 *
 * @param  string  $rowId The rowid of the item
 * @return boolean
 */

 $rowId = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

Cart::remove($rowId);
```


### Get a single Item from Cart


**Cart::get()**

```php
/**
 * Get a row of the cart by its ID
 *
 * @param  string $rowId The ID of the row to fetch
 * @return CartRowCollection
 */

$rowId = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

Cart::get($rowId);
```


### Get all Items from Cart


**Cart::contents()**

```php
/**
 * Get the cart content
 *
 * @return CartCollection
 */

Cart::contents();
```


### Empty Cart [ remove all items from cart]


**Cart::destroy()**

```php
/**
 * Empty the cart
 *
 * @return boolean
 */

Cart::destroy();
```


### Get total amount of added Items in Cart


**Cart::total()**

```php
/**
 * Total amount of cart
 *
 * @return float
 */

Cart::total();
```


### [Subtotal] Get total amount of an added Item in Cart [single item with quantity > 1]


**Cart::subtotal()**

```php
/**
 * Sub total amount of cart
 *
 * @return float
 */

Cart::subtotal();
```


### Get total discount amount of items added in Cart


**Cart::discount()**

```php
/**
 * Discount of cart
 *
 * @return float
 */

Cart::discount();
```


**Cart::setCustomDiscount(5.00)**

```php
/**
 * @param $amount
 * @return bool
 */

Cart::setCustomDiscount(5.00);
```


**Cart::customDiscount()**

```php
/**
 * Custom discount of cart
 *
 * @return float
 */

 
Cart::customDiscount();
```


### Get total quantity of a single item added in Cart

**Cart::cartQuantity()**

```php
/**
 * Get the number of items in the cart
 *
 * @param  boolean $totalItems Get all the items (when false, will return the number of rows)
 * @return int
 */

 Cart::cartQuantity();      // Total items
 Cart::cartQuantity(false); // Total rows
```

**Cart::search()**

```php
/**
 * Search if the cart has a item
 *
 * @param  Array  $search An array with the item ID and optional options
 * @return Array|boolean
 */

 Cart::search(array('id' => 1, 'options' => array('size' => 'L'))); // Returns an array of rowid(s) of found item(s) or false on failure
```


### Show Cart contents

```php
foreach(Cart::contents() as $row)
{
	echo 'You have ' . $row->quantity . ' items of ' . $row->product->name . ' with description: "' . $row->product->description . '" in your cart.';
}
```


### Exceptions
The Cart package will throw exceptions if something goes wrong. This way it's easier to debug your code using the Cart package or to handle the error based on the type of exceptions. The Cart packages can throw the following exceptions:

| Exception                             | Reason                                                                           |
| ------------------------------------- | --------------------------------------------------------------------------------- |
| *iLaCartInstanceException*       		| When no instance is passed to the instance() method                              |
| *iLaCartInvalidItemException*		    | When a new product misses one of it's arguments (`id`, `name`, `quantity`, `price`, `tax`)   |
| *iLaCartInvalidDiscountException*   	| When a non-numeric discount is passed                                               |
| *iLaCartInvalidPriceException*	    | When a non-numeric price is passed                                               |
| *iLaCartInvalidQuantityException*     | When a non-numeric quantity is passed                                            |
| *iLaCartInvalidRowIDException*   		| When the `$rowId` that got passed doesn't exists in the current cart             |
| *iLaCartInvalidTaxException*   		| When a non-numeric tax is passed                                 |
| *iLaCartUnknownModelException*	    | When an unknown model is associated to a cart row                                |



## Example

// Controller

```php


	/**
     * Display the specified resource.
     *
     * @param int $id
     * @param int $quantity
     * @param string $size
     * @param string $color
     * @return \Illuminate\Http\Response
     */
    public function storeCart($id, $quantity, $size, $color)
    {
        $product = Product::find($id);
        $item = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'image' => $product->thumbnail,
            'quantity' => $quantity > 0 ? $quantity : 1,
            'price' => $product->price,
            'discount' => $product->discount_amount,
            'tax' => 0,
			'options' => ['size' => $size, 'color' => $color]
        ];
        Cart::insert($item);
        $items = Cart::contents();
        $quantity = Cart::cartQuantity();
        $total = Cart::total();
		return view('home', ['items' => $items, 'quantity' => $quantity, 'total' => $total]);
    }
```

// View
```php
<table>
   	<thead>
       	<tr>
           	<th>Product</th>
           	<th>Quantity</th>
           	<th>Item Price</th>
           	<th>Subtotal</th>
       	</tr>
   	</thead>

   	<tbody>

   	@foreach(Cart::contents() as $item) :?>

       	<tr>
           	<td>
               	<p><strong>{{ $item->name }} </strong></p>
               	<p>{{ $item->options->has('size') ? $item->options->size : '' }} </p>
               	<p>{{ $item->options->has('color') ? $item->options->color : '' }} </p>
           	</td>
           	<td><input type="text" value="{{ $item->quantity }}"></td>
           	<td>${{ $item->price }} </td>
           	<td>${{ $item->subtotal }}</td>
       </tr>

   	@endforeach

   	</tbody>
</table>
```