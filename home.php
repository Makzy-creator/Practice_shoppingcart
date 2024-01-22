<!-- //The home page is the first page my users will see. For this page, I can add a featured image and text along with a list of 4 recently added products. -->

<?php
// Get the 4 most recently added products
$stmt = $pdo->prepare('SELECT * FROM products ORDER BY date_added DESC LIMIT 4');
$stmt->execute();
$recently_added_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

//The above code will execute an SQL query to retrieve the four most recently added products from our database. All we have to do with this query is ORDER BY the date_added column and limit the number of results by 4. Pretty straightforward, right? And then subsequently store the result in the $recently_added_products variable as an associated array.
?>

<!-- The code syntax below will create a basic home page template. The code will iterate the $recently_added_products array variable and populate them accordingly. The RRP price will be included but only if the value is greater than 0. -->
<?=template_header('Home')?>

<div class="featured">
    <h2>Gadgets</h2>
    <p>Essential gadgets for everyday use</p>
</div>
<div class="recentlyadded content-wrapper">
    <h2>Recently Added Products</h2>
    <div class="products">
        <?php foreach ($recently_added_products as $product): ?>
        <a href="index.php?page=product&id=<?=$product['id']?>" class="product">
            <img src="img/<?=$product['img']?>" width="200" height="200" alt="<?=$product['name']?>">
            <span class="name"><?=$product['name']?></span>
            <span class="price">
                &dollar;<?=$product['price']?>
                <?php if ($product['rrp'] > 0): ?>
                <span class="rrp">&dollar;<?=$product['rrp']?></span>
                <?php endif; ?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<?=template_footer()?>


