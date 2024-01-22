<!-- The shopping cart page is where the customer will be able to see a list of their products which has been added to the shopping cart. They will have the ability to remove products and update the quantities.
In the code below I make use of the PHP session variables. I can use PHP sessions to remember the shopping cart products. For example, when a customer navigates to another page, the shopping cart will still contain the products previously added until the session expires (usually around 30 minutes).

The code below will check if a product was added to the cart. Check the product.php file, you can see that I created an HTML form. I am checking for those form values. If the product exists, proceed to verify the product by selecting it from my products table in my database. I wouldn't want customers manipulating the system and adding non-existent products.

The session variable cart will be an associated array of products, and with this array, we can add multiple products to the shopping cart. The array key will be the product ID, and the value will be the quantity. If a product already exists in the shopping cart, all we have to do is update the quantity.
-->

<?php
// If the user clicked the add to cart button on the product page we can check for the form data for that product. Below the 'if isset' is saying if the value of the product_id and quantity is set IE declared, and if the values are numeric, do the following. Hence the 'if isset()' function checks that the value of a variable is not NULL.
if (isset($_POST['product_id'], $_POST['quantity']) && is_numeric($_POST['product_id']) && is_numeric($_POST['quantity'])) {
    // Set the post variables so we easily identify them, also make sure they are integer
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    // Prepare the SQL statement, we basically are checking if the product exists in our database
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?'); //parameterized query where '?' acts as the placeholder for the actual values
    $stmt->execute([$_POST['product_id']]); //execute statement where the actual values are then provided to prevent SQL Injection

    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if ($product && $quantity > 0) {
        // Product exists in database, now we can create/update the session variable for the cart
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) { //ie, if cart session is not empty and it's an array check for if the array key exits in the session cart
            if (array_key_exists($product_id, $_SESSION['cart'])) { //ie, if the product_id exits in the cart session already, update the quantity.
                // Product exists in cart so just update the quantity
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                // Product is not in cart so add it
                $_SESSION['cart'][$product_id] = $quantity;
            }
        } else {
            // There are no products in cart, this will add the first product to cart
            $_SESSION['cart'] = array($product_id => $quantity);
        }
    }
    // Prevent form resubmission...
    header('location: index.php?page=cart');
    exit;
}

// Remove product from cart, check for the URL param "remove", this is the product id, make sure it's a number and check if it's in the cart
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
    //unset or remove the product_id whose param is now 'remove' from cart
    unset($_SESSION['cart'][$_GET['remove']]);
}

//ON THE shopping cart page, the customer will have the ability to remove a product from the cart. When the button is clicked, we can use a GET request to determine which product to remove. For example, if we have a product with the ID 1, the following URL will remove it from the shopping cart: http://localhost/shoppingcart/index.php?page=cart&remove=1.


// Update product quantities in cart if the user clicks the "Update" button on the shopping cart page
if (isset($_POST['update']) && isset($_SESSION['cart'])) {
    // Loop through the post data so we can update the quantities for every product in cart
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'quantity') !== false && is_numeric($v)) {
            $id = str_replace('quantity-', '', $k);
            $quantity = (int)$v;
            // Always do checks and validation
            if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                // Update new quantity
                $_SESSION['cart'][$id] = $quantity;
            }
        }
    }
    // Prevent form resubmission...
    header('location: index.php?page=cart');
    exit;
}

//The code above will iterate the products in the shopping cart and update the quantities. The customer will have the ability to change the quantities on the shopping cart page. The Update button has the name of update, as this is how the code will know when to update the quantities using a POST request.

//PLACE ORDER PAGE
// Send the user to the place order page if they click the Place Order button, also the cart should not be empty
if (isset($_POST['placeorder']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    header('Location: index.php?page=placeorder');
    exit;
}


// Check the session variable for products in cart
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$products = array();
$subtotal = 0.00;
// If there are products in cart
if ($products_in_cart) {
    // There are products in the cart so we need to select those products from the database
    // Products in cart array to question mark string array, we need the SQL statement to include IN (?,?,?,...etc)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id IN (' . $array_to_question_marks . ')');
    // We only need the array keys, not the values, the keys are the id's of the products
    $stmt->execute(array_keys($products_in_cart));
    // Fetch the products from the database and return the result as an Array
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Calculate the subtotal
    foreach ($products as $product) {
        $subtotal += (float)$product['price'] * (int)$products_in_cart[$product['id']];
    }
}
//IN THE CODE ABOVE, If there are products in the shopping cart, retrieve those products from our products table, along with the following column name: product name, description, image, and price, as before we didn't store this information in our session variable.

//We also calculate the subtotal by iterating the products and multiplying the price by the quantity.
?>

<?= template_header('Cart')?>

<div class="cart content-wrapper">
    <h1>Shopping Cart</h1>
    <form action="index.php?page=cart" method="post">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Product</td>
                    <td>Price</td>
                    <td>Quantity</td>
                    <td>Total</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">You have no products added in your Shopping Cart</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="img">
                        <a href="index.php?page=product&id=<?=$product['id']?>">
                            <img src="img/<?=$product['img']?>" width="50" height="50" alt="<?=$product['name']?>">
                        </a>
                    </td>
                    <td>
                        <a href="index.php?page=product&id=<?=$product['id']?>"><?=$product['name']?></a>
                        <br>
                        <a href="index.php?page=cart&remove=<?=$product['id']?>" class="remove">Remove</a>
                    </td>
                    <td class="price">&dollar;<?=$product['price']?></td>
                    <td class="quantity">
                        <input type="number" name="quantity-<?=$product['id']?>" value="<?=$products_in_cart[$product['id']]?>" min="1" max="<?=$product['quantity']?>" placeholder="Quantity" required>
                    </td>
                    <td class="price">&dollar;<?=$product['price'] * $products_in_cart[$product['id']]?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="subtotal">
            <span class="text">Subtotal</span>
            <span class="price">&dollar;<?=$subtotal?></span>
        </div>
        <div class="buttons">
            <input type="submit" value="Update" name="update">
            <input type="submit" value="Place Order" name="placeorder">
        </div>
    </form>
</div>

<?=template_footer()?>