<?php
// Check to make sure the id parameter is specified in the URL
if (isset($_GET['id'])) {
    // Prepare statement and execute, prevents SQL injection
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Fetch the product from the database and return the result as an Array
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if the product exists (array is not empty)
    if (!$product) {
        // Simple error to display if the id for the product doesn't exists (array is empty)
        exit('Product does not exist!');
    }
} else {
    // Simple error to display if the id wasn't specified
    exit('Product does not exist!');
}

// The code above will check if the requested id variable (GET request) exists. If specified, the code will proceed to retrieve the product from the products table in our database.
// If the product doesn't exist in the database, the code will output a simple error, the exit() function will prevent further script execution and display the error
?>
<?=template_header('Product')?>

<div class="product content-wrapper">
    <img src="img/<?=$product['img']?>" width="500" height="500" alt="<?=$product['name']?>">
    <div>
        <h1 class="name"><?=$product['name']?></h1>
        <span class="price">
            &dollar;<?=$product['price']?>
            <?php if ($product['rrp'] > 0): ?>
            <span class="rrp">&dollar;<?=$product['rrp']?></span>
            <?php endif; ?>
        </span>
        <form action="index.php?page=cart" method="post">
            <input type="number" name="quantity" value="1" min="1" max="<?=$product['quantity']?>" placeholder="Quantity" required>
            <input type="hidden" name="product_id" value="<?=$product['id']?>">
            <input type="submit" value="Add To Cart">
        </form>
        <div class="description">
            <?=$product['desc']?>
        </div>
    </div>
</div>

<?=template_footer()?>

<!-- In the code above the form is created, and the action attribute is set to the shopping cart page (index.php?page=cart) along with the method set to post. The shopping cart page (cart.php) will add the product to the cart.

With the quantity form field, we can set a maximum value, which will reflect the product's quantity (retrieved from the products table). The product ID is also added to the form, as this is so, we know which product the customer added.

We don't need to include the product's name, description, etc, as we can get that data from the products table in our database with the product ID.
If you change the id parameter in the URL to, let's say, 2, it will show us a different product.
-->