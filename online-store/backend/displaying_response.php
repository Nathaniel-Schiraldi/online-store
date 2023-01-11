<?php
    /*Author: Nathaniel Schiraldi
    Student Number: 000855552
    Last Modified: November 18, 2022
    Description: Server Script handling the display of catalog, categories, and shopping cart (collects the data and performs pagination).*/

    session_start();
        
    // Sets the default page to 1.
    if(!isset($_SESSION['currPage'])) {
        $_SESSION['currPage'] = 1;
    }

    /**
     * Display catalog function.
     * Echos the catalog elements (title, table, and control buttons).
     * @param array $data The associatve array of catalog products.
     * @param boolean $showPageControls Flag determining if the page controls should display for the user. 
     * @param integer $page The current page the user is on.
     * @param integer $totalPages The total number of pages in the web application.
     * @return void
     */
    function displayCatalog($data, $showPageControls, $page, $totalPages) { 
        echo "<div class=\"col col-lg bg-light border border-4 border-info\">";
        echo "<h3 class=\"text-center text-primary mt-2\">Product Catalog</h3>";
        echo "<table class=\"table table-dark\">" .
            "<thead class=\"align-middle text-left\"><tr><th scope=\"col\">Product ID</th><th scope=\"col\">Product Name</th><th scope=\"col\">Price</th><th scope=\"col\">In Stock?</th></tr></thead><tbody>";
        $keys = array_keys($data);

        if ($showPageControls) {
            echo "<div class=\"text-center\">";
            // Display Next Page Control
            if ($page < $totalPages) {
                echo "<button class=\"addmargin2 btn btn-info btn-outline-dark\" onclick=\"nextPage()\">Next Page</button>";
            }
            // Display Previous Control
            if ($page > 1) {
                echo "<button class=\"addmargin1 btn btn-info btn-outline-dark\" onclick=\"prevPage()\">Previous Page</button>";
            }
            echo "</div>";
        }

        // Iterate through data.
        for ($i = 0; $i < count($data); $i++) {
            foreach ($data[$keys[$i]] as $key => $value) {

                // Display Product ID (with more details button)
                if ($key == "product_id") {
                    echo "<tr><td><button class=\"btn btn-danger\" onclick=\"moreDetails(" . $value . ")\">" . $value . "</button></td>";
                }

                // Display Product Name
                else if ($key == "product_name") {
                    echo "<td>" . $value . "</td>";
                }

                // Display Product Price
                else if ($key == "product_price") {
                    echo "<td>$" . $value . "</td>";
                }

                // Display Product Quantity
                else if ($key == "product_quantity") {
                    echo (($value) == 0) ? "<td class=\"text-danger\">Out of Stock</td></tr>" : "<td>" . $value  . "</td></tr>";
                }
            }
        }

        echo "</tbody></table></div>";
        displayShoppingCart();
    }

    /**
     * Display shopping cart function.
     * Echos the shopping cart elements (determines if there are items in the shopping cart).
     * @return void
     */
    function displayShoppingCart() {
        require "connect.php";

        echo "<div class=\"col col-lg-4 bg-light border border-4 border-start-0 border-info\">";
        echo "<h3 class=\"text-center text-primary mt-2\">Shopping Cart</h3>"; 
        echo "<table class=\"table table-warning\">";

        // Determines if there are any records in the session table.
        $cursor = $dbh->prepare('SELECT COUNT(*) FROM `session`');
        $cursor->execute();
        $data = $cursor->fetchAll(PDO::FETCH_ASSOC);
        $numberOfSelectedProducts = $data[0]["COUNT(*)"];
             
        // If there are records, display information involving the session records. 
        if ($numberOfSelectedProducts > 0) {

            // Join the session table on the catalogue table and find matching records.
            $cursor = $dbh->prepare('SELECT s.product_id, c.product_name, c.product_price FROM `session` s JOIN catalogue c ON s.product_id = c.product_id');
            $cursor->execute();
            $data = $cursor->fetchAll(PDO::FETCH_ASSOC);

            $keys = array_keys($data);

            $capture_product_id = 0;

            // Iterate through data.
            for ($i = 0; $i < count($data); $i++) {
                foreach ($data[$keys[$i]] as $key => $value) {
    
                    // Display Product ID.
                    if ($key == "product_id") {
                        echo "<tr><td>" . $value . "</td>";
                        $capture_product_id = $value;
                    }
    
                    // Display Product Name.
                    else if ($key == "product_name") {
                        echo "<td>" . $value . "</td>";
                    }
    
                    // Display Product Price.
                    // Generate remove from cart button (includes image for the button). 
                    else if ($key == "product_price") {
                        echo "<td>$" . $value . "</td><td><button onclick=\"removeFromCart(" . $capture_product_id . ")\"><img class=\"changeDimensions\" src=\"../images/removeproduct.png\" alt=\"Remove Product\"></button></td></tr>";
                    }
    
                }
            }

            // Determines the sum of all the products in the cart
            $cursor = $dbh->prepare('SELECT ROUND(SUM(product_price), 2) FROM `session` s JOIN catalogue c ON s.product_id = c.product_id');
            $cursor->execute();
            $data = $cursor->fetchAll(PDO::FETCH_ASSOC);
            echo "<tr><td>Total:</td><td>$" . $data[0]["ROUND(SUM(product_price), 2)"] . "</td></tr>";
        }

        else {
            echo "<tr><td>Total: </td><td>$0.00</td></tr>";
        }
        
        echo "</table><div class=\"text-center\"><button class=\"addmargin1 btn btn-primary\">Check Out</button></div></div>";
    }

    /**
     * Data Collection From regular Category function.
     * Gathers the category data required to be displayed depending on the page the user is on.  
     * @param string $category The category selected by the user (GPU, CPU, RAM, or ALL)
     * @return void
     */
    function dataCollectionFromRegularCategory($category) {
        require "connect.php";

        // Amount of items per page.
        $itemsPerPage = 7;

        // Determines the number of records/items present. 
        if ($category == "ALL") {
            $cursor = $dbh->prepare('SELECT COUNT(*) FROM catalogue');
        }
        else {
            $cursor = $dbh->prepare('SELECT COUNT(*) FROM catalogue WHERE product_category = :product_category');
            $cursor->bindParam('product_category', $category);
        }

        $cursor->execute();
        $totalItems = ($cursor->fetchAll(PDO::FETCH_ASSOC));

        // More than 7 items.
        if ($totalItems[0]["COUNT(*)"] > 7) {

            // Allow page controls.
            $showPageControls = true;

            // Determines the total pages for the web application.
            $totalPages = $totalItems[0]["COUNT(*)"] / $itemsPerPage;

            // Gets current page.
            $page = $_SESSION['currPage'];

            // Determines the offset for the query.
            // Page 1 Offset - 0
            // Page 2 Offset - 7
            // Page 3 Offset - 14
            $offset = ($page - 1) * $itemsPerPage;

            // Capture category.
            $_SESSION['currentCategory'] = $category;
        }
        else {

            // Set Defaults
            $_SESSION['currPage'] = 1;
            $showPageControls = false;
            $page = 1;
            $totalPages = 1;
            $offset = 0;
        }
     
        // Determines appropriate items based off the limit and offset for the all category.
        if ($category == "ALL") {
            $cursor = $dbh->prepare('SELECT product_id, product_name, product_description, product_price, product_quantity 
            FROM catalogue LIMIT :items_per_page OFFSET :offset');
        }

        // Determines appropriate items based off the limit and offset for a seperate product category.
        else {
            $cursor = $dbh->prepare('SELECT product_id, product_name, product_description, product_price, product_quantity 
            FROM catalogue
            WHERE product_category = :product_category
            LIMIT :items_per_page OFFSET :offset');
            $cursor->bindParam('product_category', $category);
        }

        $cursor->bindParam('items_per_page', $itemsPerPage, PDO::PARAM_INT);
        $cursor->bindParam('offset', $offset, PDO::PARAM_INT);
        $cursor->execute();
        $data = $cursor->fetchAll(PDO::FETCH_ASSOC);
        displayCatalog($data, $showPageControls, $page, $totalPages);
    }

    /**
     * Display categories function.
     * Echos the category elements (list, buttons, and onclick functionality).
     * @return void
     */
    function displayCategories() {
        require "connect.php";
        echo "<ul>";

        $cursor = $dbh->prepare('SELECT DISTINCT product_category FROM catalogue');
        $cursor->execute();
        $data = $cursor->fetchAll(PDO::FETCH_ASSOC);
        $keys = array_keys($data);

        for ($i = 0; $i < count($data); $i++) {
            foreach ($data[$keys[$i]] as $key => $value) {

                // Display Graphics Card Category.
                if ($value == "GPU") {
                    echo "<li><button class=\"btn btn-outline-info\" onclick=\"displayProducts('$value')\">Graphics Cards</button></li>";
                }

                // Display Processor Category.
                else if ($value == "CPU") {
                    echo "<li><button class=\"btn btn-outline-info\" onclick=\"displayProducts('$value')\">Processors</button></li>";
                }

                // Display Memory Category.
                else if ($value == "RAM") {
                    echo "<li><button class=\"btn btn-outline-info\" onclick=\"displayProducts('$value')\">Memory</button></li>";
                }

                // Display Additional Category when added.
                else {
                    echo "<li><button class=\"btn btn-outline-info\" onclick=\"displayProducts('$value')\">" . $value . "</button></li>";
                }
            }
        }

        echo "<li><button class=\"btn btn-outline-info\" onclick=\"displayProducts('ALL')\">Everything</button></li></ul>";
    }

    if (isset($_POST['displayCategories'])) {
        displayCategories();
    }

    // When the display request is sent data colletion is done then the catalog of products is displayed.
    if (isset($_POST['displayProducts'])) {

        // Reset current page to 1.
        $_SESSION['currPage'] = 1;

        $display = filter_input(INPUT_POST, 'displayProducts', FILTER_SANITIZE_SPECIAL_CHARS);
     
        if ($display == 'ALL') {
            dataCollectionFromRegularCategory('ALL');
        }
        else {
            dataCollectionFromRegularCategory($display);
        }
    }

    // Increments the current page.
    if (isset($_POST['nextPage'])) {
        $_SESSION['currPage']++;
        dataCollectionFromRegularCategory($_SESSION['currentCategory']);
    }

    // Decrements the current page.
    if (isset($_POST['prevPage'])) {
        $_SESSION['currPage']--;
        dataCollectionFromRegularCategory($_SESSION['currentCategory']);
    }
    
?>