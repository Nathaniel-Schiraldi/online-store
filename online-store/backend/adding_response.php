<?php
    /*Author: Nathaniel Schiraldi
    Student Number: 000855552
    Last Modified: November 18, 2022
    Description: Server Script handling the add to cart functionality (inserts item and updates product catalog).*/

    /**
     * Add to cart function.
     * Echos confirmation that the product has been added to the cart.
     * @param integer $productID The product added to the cart.
     * @return void
     */
    function addToCart($productID) {
        echo "<div class=\"col col-lg bg-light border border-4 border-info\">";
        echo "<table class=\"table table-success\">" .
        "<thead class=\"align-middle text-center\"><tr><th scope=\"col\">Item Added to Cart Successfully!</th></tr></thead><tbody>";
        echo "</tbody></table></div>";
        
        insertItem($productID);
    }

    /**
     * Insert item function.
     * Inserts the product added to the session table.
     * @param integer $productID The product inserted.
     * @return void
     */
    function insertItem($productID) {
        require "connect.php";
        include_once "displaying_response.php";

        $user_session_id = session_id();

        // Query for information about the product (id, name, description, price).
        $cursor = $dbh->prepare('SELECT product_id, product_name, product_description, product_price FROM catalogue WHERE product_id = :id');
        $cursor->bindParam('id', $productID, PDO::PARAM_INT);
        $cursor->execute();
        $data = $cursor->fetchAll(PDO::FETCH_ASSOC);

        $session_data = json_encode($data);

        // Encodes the data in JSON format and inserts the data into the session_data field.
        $cursor = $dbh->prepare('INSERT INTO `session` VALUES (?, ?, ?)');
        $params = [$user_session_id, $productID, $session_data];
        $cursor->execute($params);
        
        updateProductCatalog($productID);
    }

    /**
     * Update product catalog function.
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

        $updated_product_quantity = $data[0]["product_quantity"] - 1;

        // Query to update the product quantity.
        $cursor = $dbh->prepare('UPDATE catalogue SET product_quantity = :pq WHERE product_id = :id');
        $cursor->bindParam('pq', $updated_product_quantity, PDO::PARAM_INT);
        $cursor->bindParam('id', $productID, PDO::PARAM_INT);
        $cursor->execute();
        displayShoppingCart();
    }

    // Adds the product selected to the cart.
    if (isset($_POST['addToCart'])) {
        $productID = filter_input(INPUT_POST, 'addToCart', FILTER_VALIDATE_INT);
        addToCart($productID);
    }
?>