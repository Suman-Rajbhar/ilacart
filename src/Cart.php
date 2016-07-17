<?php
/**
 * Created by PhpStorm.
 * User: Lutfor Rahman
 * Email: contact.lutforrahman@gmail.com
 * Web: www.lutforrahman.com
 * GitHub: https://github.com/contactlutforrahman
 * Packagist: https://packagist.org/users/lutforrahman/
 * Date: 7/16/2016
 * Time: 12:16 PM
 */

namespace Lutforrahman\iLaCart;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;

class Cart
{

    /**
     * Session class instance
     *
     * @var Illuminate\Session\SessionManager
     */
    protected $session;

    /**
     * Event class instance
     *
     * @var Illuminate\Events\Dispatcher
     */
    protected $event;

    /**
     * Current cart instance
     *
     * @var string
     */
    protected $instance;

    /**
     * The Eloquent model a cart is associated with
     *
     * @var string
     */
    protected $associatedModel;

    /**
     * An optional namespace for the associated model
     *
     * @var string
     */
    protected $associatedModelNamespace;

    /**
     * Constructor
     *
     * @param Illuminate\Session\SessionManager $session Session class instance
     * @param \Illuminate\Contracts\Events\Dispatcher $event Event class instance
     */
    public function __construct($session, Dispatcher $event)
    {
        $this->session = $session;
        $this->event = $event;

        $this->instance = 'main';
    }

    /**
     * Set the current cart instance
     *
     * @param  string $instance Cart instance name
     * @return Lutforrahman\iLaCart\Cart
     */
    public function instance($instance = null)
    {
        if (empty($instance)) throw new Exceptions\iLaCartInstanceException;

        $this->instance = $instance;

        // Return self so the method is chainable
        return $this;
    }

    /**
     * Set the associated model
     *
     * @param  string $modelName The name of the model
     * @param  string $modelNamespace The namespace of the model
     * @return void
     */
    public function associate($modelName, $modelNamespace = null)
    {
        $this->associatedModel = $modelName;
        $this->associatedModelNamespace = $modelNamespace;

        if (!class_exists($modelNamespace . '\\' . $modelName)) throw new Exceptions\iLaCartUnknownModelException;

        // Return self so the method is chainable
        return $this;
    }

    /**
     * Add a row to the cart
     * @param string|array $id Unique ID of the item|Item formated as array|Array of items
     * @param string $sku Unique SKU of the item
     * @param string $name Name of the item
     * @param string $slug Slug of the item
     * @param string $image Image of the item
     * @param string $description Description of the item
     * @param int $quantity Item quantity to add to the cart
     * @param float $price Price of one item
     * @param float $discount Discount amount of one item
     * @param float $tax Tax amount of one item
     * @param array $options Array of additional options, such as 'size' or 'color'
     */
    public function insert($id, $sku, $name = null, $slug = null, $image = null, $description = null, $quantity = null, $price = null, $discount = null, $tax = null, array $options = [])
    {
        // If the first parameter is an array we need to call the insert() function again
        if (is_array($id)) {
            // And if it's not only an array, but a multidimensional array, we need to
            // recursively call the insert function
            if ($this->is_multi($id)) {
                // Fire the cart.batch event
                $this->event->fire('cart.batch', $id);

                foreach ($id as $item) {
                    $options = array_get($item, 'options', []);
                    $this->insertRow($item['id'], $item['sku'], $item['name'], $item['slug'], $item['image'], $item['description'], $item['quantity'], $item['price'], $this->discountResolve($item), $item['tax'], $options);
                }

                // Fire the cart.batched event
                $this->event->fire('cart.batched', $id);

                return null;
            }

            $options = array_get($id, 'options', []);

            // Fire the cart.insert event
            $this->event->fire('cart.insert', array_merge($id, ['options' => $options]));

            $result = $this->insertRow($id['id'], $id['sku'], $id['name'], $id['slug'], $id['image'], $id['description'], $id['quantity'], $id['price'], $this->discountResolve($id), $id['tax'], $options);

            // Fire the cart.inserted event
            $this->event->fire('cart.inserted', array_merge($id, ['options' => $options]));

            return $result;
        }

        // Fire the cart.insert event
        $this->event->fire('cart.insert', compact('id', 'sku', 'name', 'slug', 'image', 'description', 'quantity', 'price', 'tax', 'options'));

        $result = $this->insertRow($id, $name, $slug, $image, $description, $quantity, $price, $discount, $tax, $options);

        // Fire the cart.inserted event
        $this->event->fire('cart.inserted', compact('id', 'sku', 'name', 'slug', 'image', 'description', 'quantity', 'price', 'tax', 'options'));

        return $result;
    }

    /**
     * @param $data
     * @return null
     */
    public function discountResolve($data)
    {
        if (isset($data['discount']))
            return $data['discount'];
        else
            return null;
    }

    /**
     * Update the quantity of one row of the cart
     *
     * @param  string $rowId The rowid of the item you want to update
     * @param  integer|array $attribute New quantity of the item|Array of attributes to update
     * @return boolean
     */
    public function update($rowId, $attribute)
    {
        if (!$this->hasRowId($rowId)) throw new Exceptions\iLaCartInvalidRowIDException;

        if (is_array($attribute)) {
            // Fire the cart.update event
            $this->event->fire('cart.update', $rowId);

            $result = $this->updateAttribute($rowId, $attribute);

            // Fire the cart.updated event
            $this->event->fire('cart.updated', $rowId);

            return $result;
        }

        // Fire the cart.update event
        $this->event->fire('cart.update', $rowId);

        $result = $this->updateQuantity($rowId, $attribute);

        // Fire the cart.updated event
        $this->event->fire('cart.updated', $rowId);

        return $result;
    }

    /**
     * Remove a row from the cart
     *
     * @param  string $rowId The rowid of the item
     * @return boolean
     */
    public function remove($rowId)
    {
        if (!$this->hasRowId($rowId)) throw new Exceptions\iLaCartInvalidRowIDException;

        $cart = $this->getContent();

        // Fire the cart.remove event
        $this->event->fire('cart.remove', $rowId);

        $cart->forget($rowId);

        // Fire the cart.removed event
        $this->event->fire('cart.removed', $rowId);

        return $this->updateCart($cart);
    }

    /**
     * Get a row of the cart by its ID
     *
     * @param  string $rowId The ID of the row to fetch
     * @return Lutforrahman\iLaCart\CartCollection
     */
    public function get($rowId)
    {
        $cart = $this->getContent();

        return ($cart->has($rowId)) ? $cart->get($rowId) : NULL;
    }

    /**
     * Get the cart content
     *
     * @return Lutforrahman\iLaCart\CartRowCollection
     */
    public function contents()
    {
        $cart = $this->getContent();

        return (empty($cart)) ? NULL : $cart;
    }

    /**
     * Empty the cart
     *
     * @return boolean
     */
    public function destroy()
    {
        // Fire the cart.destroy event
        $this->event->fire('cart.destroy');

        $result = $this->updateCart(NULL);

        // Fire the cart.destroyed event
        $this->event->fire('cart.destroyed');

        return $result;
    }


    /**
     * Get the number of items in the cart
     *
     * @param  boolean $totalItems Get all the items (when false, will return the number of rows)
     * @return int
     */
    public function cartQuantity($totalItems = true)
    {
        $cart = $this->getContent();

        if (!$totalItems) {
            return $cart->count();
        }

        $count = 0;

        foreach ($cart AS $row) {
            $count += $row->quantity;
        }

        return $count;
    }

    /**
     * Search if the cart has a item
     *
     * @param  array $search An array with the item ID and optional options
     * @return array|boolean
     */
    public function search(array $search)
    {
        if (empty($search)) return false;

        foreach ($this->getContent() as $item) {
            $found = $item->search($search);

            if ($found) {
                $rows[] = $item->rowid;
            }
        }

        return (empty($rows)) ? false : $rows;
    }

    /**
     * insert row to the cart
     *
     * @param string $id Unique ID of the item
     * @param string $sku Unique SKU of the item
     * @param string $name Name of the item
     * @param string $slug Slug of the item
     * @param string $image Image of the item
     * @param string $description Description of the item
     * @param int $quantity Item quantity to insert to the cart
     * @param float $price Price of one item
     * @param float $discount Discount amount of one item
     * @param float $tax Tax amount of one item
     * @param array $options Array of additional options, such as 'size' or 'color'
     */
    protected function insertRow($id, $sku, $name, $slug, $image, $description, $quantity, $price, $discount, $tax, array $options = [])
    {
        if (empty($id) || empty($name) || empty($quantity) || !isset($price)) {
            throw new Exceptions\iLaCartInvalidItemException;
        }

        if (!is_numeric($quantity)) {
            throw new Exceptions\iLaCartInvalidQuantityException;
        }

        if (!is_numeric($price)) {
            throw new Exceptions\iLaCartInvalidPriceException;
        }

        if (!is_numeric($discount)) {
            throw new Exceptions\iLaCartInvalidDiscountException;
        }

        if (!is_numeric($tax)) {
            throw new Exceptions\iLaCartInvalidTaxException;
        }

        $cart = $this->getContent();
        $rowId = $this->generateRowId($id, $options);

        if ($cart->has($rowId)) {
            $row = $cart->get($rowId);
            $cart = $this->updateRow($rowId, ['quantity' => $row->quantity + $quantity]);
        } else {
            $cart = $this->createRow($rowId, $id, $ - ($quantity * $discount), $name, $slug, $image, $description, $quantity, $price, $discount, $tax, $options);
        }

        $this->updateCart($cart);
        $this->setTotal();
        $this->setSubTotal();
        $this->setDiscount();
        return null;
    }

    /**
     * Generate a unique id for the new row
     *
     * @param  string $id Unique ID of the item
     * @param  array $options Array of additional options, such as 'size' or 'color'
     * @return boolean
     */
    protected function generateRowId($id, $options)
    {
        ksort($options);

        return md5($id . serialize($options));
    }

    /**
     * Check if a rowid exists in the current cart instance
     *
     * @param  string $id Unique ID of the item
     * @return boolean
     */
    protected function hasRowId($rowId)
    {
        return $this->getContent()->has($rowId);
    }

    /**
     * Update the cart
     *
     * @param  Lutforrahman\iLaCart\CartCollection $cart The new cart content
     * @return void
     */
    protected function updateCart($cart)
    {
        $this->session->put($this->getInstance(), $cart);
        $this->session->save();
        return null;
    }

    /**
     * Get the carts content, if there is no cart content set yet, return a new empty Collection
     *
     * @return Lutforrahman\iLaCart\CartCollection
     */
    protected function getContent()
    {
        $content = ($this->session->has($this->getInstance())) ? $this->session->get($this->getInstance()) : new CartCollection;

        return $content;
    }

    /**
     * Get the current cart instance
     *
     * @return string
     */
    protected function getInstance()
    {
        return 'cart.' . $this->instance;
    }

    /**
     * Update a row if the rowId already exists
     *
     * @param  string $rowId The ID of the row to update
     * @param  integer $quantity The quantity to insert to the row
     * @return Lutforrahman\iLaCart\CartCollection
     */
    protected function updateRow($rowId, $attributes)
    {
        $cart = $this->getContent();

        $row = $cart->get($rowId);

        foreach ($attributes as $key => $value) {
            if ($key == 'options') {
                $options = $row->options->merge($value);
                $row->put($key, $options);
            } else {
                $row->put($key, $value);
            }
        }

        if (!is_null(array_keys($attributes, ['quantity', 'price']))) {
            $row->put('total', $row->quantity * $row->price);
            $row->put('total_discount', $row->quantity * $row->discount);
            $row->put('subtotal', ($row->quantity * $row->price) - ($row->quantity * $row->discount));
        }

        $cart->put($rowId, $row);

        $this->setTotal();
        $this->setSubTotal();
        $this->setDiscount();

        return $cart;
    }

    /**
     * Create a new row Object
     *
     * @param  string $rowId The ID of the new row
     * @param  string $id Unique ID of the item
     * @param  string $sku Unique SKU of the item
     * @param  string $name Name of the item
     * @param  string $slug Slug of the item
     * @param  string $image Image of the item
     * @param  string $description Description of the item
     * @param  int $quantity Item quantity to insert to the cart
     * @param  float $price Price of one item
     * @param  float $discount Discount of one item
     * @param  float $tax Tax of one item
     * @param  array $options Array of additional options, such as 'size' or 'color'
     * @return Lutforrahman\iLaCart\CartCollection
     */
    protected function createRow($rowId, $id, $sku, $name, $slug, $image, $description, $quantity, $price, $discount, $tax, $options)
    {
        $cart = $this->getContent();

        $newRow = new CartRowCollection([
            'rowid' => $rowId,
            'id' => $id,
            'sku' => $sku,
            'name' => $name,
            'slug' => $slug,
            'image' => $image,
            'description' => $description,
            'quantity' => $quantity,
            'price' => $price,
            'discount' => $discount,
            'tax' => $tax,
            'options' => new CartRowOptionsCollection($options),
            'total' => $quantity * $price  - ($quantity * $discount),
            'total_discount' => $quantity * $discount,
            'subtotal' => ($quantity * $price) - ($quantity * $discount),
        ], $this->associatedModel, $this->associatedModelNamespace);

        $cart->put($rowId, $newRow);

        return $cart;
    }

    /**
     * Update the quantity of a row
     *
     * @param  string $rowId The ID of the row
     * @param  int $quantity The quantity to insert
     * @return Lutforrahman\iLaCart\CartCollection
     */
    protected function updateQuantity($rowId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->remove($rowId);
        }

        return $this->updateRow($rowId, ['quantity' => $quantity]);
    }

    /**
     * Update an attribute of the row
     *
     * @param string $rowId The ID of the row
     * @param array $attributes An array of attributes to update
     * @return Lutforrahman\iLaCart\CartCollection
     */
    protected function updateAttribute($rowId, $attributes)
    {
        return $this->updateRow($rowId, $attributes);
    }

    /**
     * Check if the array is a multidimensional array
     *
     * @param  array $array The array to check
     * @return boolean
     */
    protected function is_multi(array $array)
    {
        return is_array(head($array));
    }

    /**
     * @param $amount
     * @return bool
     */
    protected function setCustomDiscount($amount)
    {
        $cart = $this->getContent();

        if (!$cart->isEmpty() && is_numeric($amount)) {
            $cart->custom_discount = floatval($amount);
            $this->setSubTotal();
            $this->updateCart($cart);
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function customDiscount()
    {
        return $this->getContent()->custom_discount;
    }

    /**
     * @return bool
     */
    public function setDiscount()
    {
        $cart = $this->getContent();

        if ($cart->isEmpty()) {
            return false;
        }

        $discount = 0;
        foreach ($cart AS $row) {
            $discount += $row->total_discount;
        }

        $cart->discount = floatval($discount);
        $this->updateCart($cart);

        return true;
    }

    /**
     * @return mixed
     */
    public function discount()
    {
        return $this->getContent()->discount;
    }

    /**
     * @return mixed
     */
    protected function setTotal()
    {
        $cart = $this->getContent();

        if ($cart->isEmpty()) {
            return false;
        }

        $total = 0;
        foreach ($cart AS $row) {
            $total += $row->total;
        }

        $cart->total = floatval($total);
        $this->updateCart($cart);

        return true;
    }

    /**
     * @return mixed
     */
    public function total()
    {
        return $this->getContent()->total;
    }

    /**
     * @return mixed
     */
    protected function setSubTotal()
    {
        $cart = $this->getContent();

        if ($cart->isEmpty()) {
            return false;
        }

        $subtotal = 0;
        foreach ($cart AS $row) {
            $subtotal += $row->subtotal;
        }

        $cart->subtotal = floatval($subtotal - $this->customDiscount());
        $this->updateCart($cart);

        return true;
    }

    /**
     * @return mixed
     */
    public function subtotal()
    {
        return $this->getContent()->subtotal;
    }

}