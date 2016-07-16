## iLaCart The Laravel Shoppingcart

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

## Overview
Look at one of the following topics to learn more about iLaCart

* [Usage](#usage)
* [Collections](#collections)
* [Instances](#instances)
* [Models](#models)
* [Exceptions](#exceptions)
* [Events](#events)
* [Example](#example)

## Usage

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

// Batch method
Cart::insert(array(
  array('id' => '101', 'name' => 'Product name', 'slug' => 'product-name', 'image' => 'uploads/product-thumbnail.jpg', 'quantity' => 1, 'price' => 9.99, 'discount' => 0.00, 'tax' => 0.00,),
  array('id' => '102', 'name' => 'Product name 2', 'slug' => 'product-name-2', 'image' => 'uploads/product-thumbnail-2.jpg', 'quantity' => 1, 'price' => 9.99, 'discount' => 0.00, 'tax' => 0.00,  'options' => array('size' => 'large', 'color' => 'white'))
));
```

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
```

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

**Cart::contents()**

```php
/**
 * Get the cart content
 *
 * @return CartCollection
 */

Cart::contents();
```

**Cart::destroy()**

```php
/**
 * Empty the cart
 *
 * @return boolean
 */

Cart::destroy();
```

**Cart::total()**

```php
/**
 * Total amount of cart
 *
 * @return float
 */

Cart::total();
```

**Cart::subtotal()**

```php
/**
 * Sub total amount of cart
 *
 * @return float
 */

Cart::subtotal();
```

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

## Collections

As you might have seen, the `Cart::contents()` and `Cart::get()` methods both return a Collection, a `CartCollection` and a `CartRowCollection`.

These Collections extends the 'native' Laravel 4 Collection class, so all methods you know from this class can also be used on your shopping cart. With some addition to easily work with your carts content.

## Instances

Now the packages also supports multiple instances of the cart. The way this works is like this:

You can set the current instance of the cart with `Cart::instance('newInstance')`, at that moment, the active instance of the cart is `newInstance`, so when you add, remove or get the content of the cart, you work with the `newInstance` instance of the cart.
If you want to switch instances, you just call `Cart::instance('otherInstance')` again, and you're working with the `otherInstance` again.

So a little example:

```php
Cart::instance('shopping')->insert('101', 'Product name', 1, 9.99);

// Get the content of the 'shopping' cart
Cart::contents();

Cart::instance('wishlist')->insert('102', 'Product name 2', 1, 19.95, array('size' => 'medium'));

// Get the content of the 'wishlist' cart
Cart::contents();

// If you want to get the content of the 'shopping' cart again...
Cart::instance('shopping')->contents();

// And the count of the 'wishlist' cart again
Cart::instance('wishlist')->cartQuantity();
```

N.B. Keep in mind that the cart stays in the last set instance for as long as you don't set a different one during script execution.

N.B.2 The default cart instance is called `main`, so when you're not using instances,`Cart::content();` is the same as `Cart::instance('main')->content()`.

## Models
A new feature is associating a model with the items in the cart. Let's say you have a `Product` model in your application. With the new `associate()` method, you can tell the cart that an item in the cart, is associated to the `Product` model. 

That way you can access your model right from the `CartRowCollection`!

Here is an example:

```php
<?php 

/**
 * Let say we have a Product model that has a name and description.
 */

Cart::associate('Product')->insert('101', 'Product name', 1, 9.99, array('size' => 'large'));


foreach(Cart::contents() as $row)
{
	echo 'You have ' . $row->quantity . ' items of ' . $row->product->name . ' with description: "' . $row->product->description . '" in your cart.';
}
```

The key to access the model is the same as the model name you associated (lowercase).
The `associate()` method has a second optional parameter for specifying the model namespace.

## Exceptions
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

## Events

The cart also has events build in. There are five events available for you to listen for.

| Event                | Fired                                   |
| -------------------- | --------------------------------------- |
| cart.add($item)      | When a single item is added             |
| cart.batch($items)   | When a batch of items is added          |
| cart.update($rowId)  | When an item in the cart is updated     |
| cart.remove($rowId)  | When an item is removed from the cart   |
| cart.destroy()       | When the cart is destroyed              |

## Example

Below is a little example of how to list the cart content in a table:

```php
// Controller

Cart::insert('101', 'Product name', 1, 9.99);
Cart::insert('102', 'Product name 2', 2, 5.95, array('size' => 'large'));

// View

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
           	</td>
           	<td><input type="text" value="{{ $item->quantity }}"></td>
           	<td>${{ $item->price }} </td>
           	<td>${{ $item->subtotal }}</td>
       </tr>

   	@endforeach

   	</tbody>
</table>
```
