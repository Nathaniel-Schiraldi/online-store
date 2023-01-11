<?php
    /*Author: Nathaniel Schiraldi
    Student Number: 000855552
    Last Modified: November 18, 2022
    Description: Server Script handling advanced product details (collects the product details).*/

    /**
     * Data Collection From Product Details function.
     * Gathers details regarding the product selected (for further details).
     * @param array $productID The product selected.
     * @return void
     */
    function dataCollectionFromProductDetails($productID) {
        require "connect.php";
        
        $cursor = $dbh->prepare('SELECT product_id, product_name, product_description, product_price, product_quantity 
        FROM catalogue
        WHERE product_id = :id');
        $cursor->bindParam('id', $productID, PDO::PARAM_INT);
        $cursor->execute();
        $data = $cursor->fetchAll(PDO::FETCH_ASSOC);
        displayProductDetails($data);
    }

    /**
     * Display product details function.
     * Echos further details on the product.
     * @param array $data The associatve array containg the product details.
     * @return void
     */
    function displayProductDetails($data) {
        include_once "displaying_response.php";
        echo "<div class=\"col col-lg bg-light border border-4 border-info\">";
        echo "<table class=\"table table-dark mt-3\">";
        $keys = array_keys($data);

        // Iterate through data. 
        for ($i = 0; $i < count($data); $i++) {
            foreach ($data[$keys[$i]] as $key => $value) {

                // Display Product ID.
                if ($key == "product_id") {
                    echo "<tr><td>Product Code</td><td class=\"text-left\">" . $value . "</td></tr>";
                }

                // Display Product Name.
                else if ($key == "product_name") {
                    echo "<tr><td>Product Name</td><td class=\"text-left\">" . $value . "</td></tr>";
                }

                // Display Product Description.
                else if ($key == "product_description") {
                    echo "<tr><td>Product Description</td><td class=\"text-left\">" . $value . "</td></tr>";
                }

                // Display Product Price.
                else if ($key == "product_price") {
                    echo "<tr><td>Product Price</td><td class=\"text-left\">$". $value . "</td></tr>";
                }

                // Display Product Quantity.
                // If Product Quantity greater than 0 generate add to cart button.
                else if ($key == "product_quantity") {
                    echo ($value == 0) ? "<td>Product Availability</td><td class=\"text-danger text-left\">Out of Stock</td></tr>" : "<tr><td>Product Availability</td><td class=\"text-left\"><button class=\"btn btn-primary\" onclick=\"addToCart(" . $data[0]["product_id"] . ")\">Add to Cart</button></td></tr>";
                }
            }
        }
        echo "</tbody></table></div>";
        displayShoppingCart();
    }

    // Retrieves further details on the product selected. 
    if (isset($_POST['moreDetails'])) {
        $productID = filter_input(INPUT_POST, 'moreDetails', FILTER_VALIDATE_INT);
        dataCollectionFromProductDetails($productID);
    }
?>