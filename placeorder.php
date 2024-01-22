<!-- This is a page that customers get to when they click the place order button on the shopping cart page. 
    This page informs the customer that they've placed an order ie, added products to cart, proceeded to checkout. 
    Recall, the isset has been set in the cart page. For a customer to get to this page, the cart must not be empty and the customer must have clicked the placeholder button.
    --->

    <?= template_header('Place Order')?>

    <div class="placeorder content-wrapper">
        <h1>your order has been placed</h1>
        <p>Thank you for ordering with us. We'll contact you by email with your order details</p>
    </div>

    <?= template_footer()?>

