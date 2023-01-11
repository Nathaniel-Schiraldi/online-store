<?php
    /*Author: Nathaniel Schiraldi
    Student Number: 000855552
    Last Modified: November 18, 2022
    Description: Server Script handling the remove from cart functionality (updates product catalog and removes item).*/

    /**
     * Remove from cart function.
     * Echos confirmation that the product has been removed from the cart.
     * @param integer $productID The product removed from the cart.
     * @return void
     */
    function removeFromCart($productID) {
        echo "<div class=\"col col-lg bg-light border border-4 border-info\">";
        echo "<table class=\"table table-danger\">" .
        "<thead class=\"align-middle text-center\"><tr><th scope=\"col\">Item Removed from Cart Successfully!</th></tr></thead><tbody>";
        echo "</tbody></table></div>";

        removeItem($productID);
    }

    /**
     * Remove item function.
     * Removes the product added to the session table.
     * @param integer $productID The product inserted.
     * @return void
     */
    function removeItem($productID) {
        require "connect.php";
        
        $cursor = $dbh->prepare('DELETE FROM `session` WHERE product_id = :id LIMIT 1');
        $cursor->bindParam('id', $productID, PDO::PARAM_INT);
        $cursor->execute();

        updateProductCatalog($productID);
    }

    /**
     * Update product catalog function (2).
     * Updates the product catalog with the updated quantity for the product.
     * @param integer $productID The product updated.
     * @return void
     */
    function updateProductCatalog($productID) {
        require "connect.php";
        include_once "displaying_response.php";

        // Query to get the old product quantity.
        $cursor = $dbh->prepare('SELECT product_quantity FROM catalogue WHERE product_id = :id');
        $cursor->bindParam('id', $productID, PDO::PARAM_INT);
        $cursor->execute();
        $data = $cursor->fetchAll(PDO::FETCH_ASSOC);

        $updated_product_quantity = $data[0]["product_quantity"] + 1;

        // Query to update the product quantity.
        $cursor = $dbh->prepare('UPDATE catalogue SET product_quantity = :pq WHERE product_id = :id');
        $cursor->bindParam('pq', $updated_product_quantity, PDO::PARAM_INT);
        $cursor->bindParam('id', $productID, PDO::PARAM_INT);
        $cursor->execute();

        displayShoppingCart();
    }

    // Removes the product selected from the cart.
    if (isset($_POST['removeFromCart'])) {
        $productID = filter_input(INPUT_POST, 'removeFromCart', FILTER_VALIDATE_INT);
        removeFromCart($productID);
    }

?>