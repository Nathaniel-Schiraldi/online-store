/*Author: Nathaniel Schiraldi
Student Number: 000855552
Last Modified: November 18, 2022
Description: Scripts to dynamically generate a product catalogue and shopping cart using AJAX post requests (creating request and responding to it). Responds to events the user generates by various button presses (Add to Cart, Remove from Cart, Product Details, Previous Page, Next Page).*/

// Default setting displays GPU product page, category list, and shopping cart (creates the post requests when user loads page for the first time).
window.addEventListener('load', (event) => {
    displayCategories();
    displayProducts('GPU');
});

/**
 * Display shopping categories function.
 * Creates a post request with the body containing the action occurred (true).
 * Responds with the list of categories displayed. 
 */
function displayCategories() {
    var req = new Request("../backend/displaying_response.php", 
    {
        method: "post",
        type: "basic",
        headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"},
        body: 'displayCategories=true'
    });

    fetch(req)
        .then( response => {return response.text();})
        .then(doNavDisplay);
} 

/**
 * Display products function.
 * Creates a post request with the body containing the product type.
 * Responds with the catalogue of products.
 * Responds with shopping cart.
 * @param {string} productType Type of Product (GPU, CPU, RAM, or All) 
 */
function displayProducts(productType) {
    
    var req = new Request("../backend/displaying_response.php", 
    {
        method: "post",
        type: "basic",
        headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"},
        body: 'displayProducts=' + productType
    });

    fetch(req)
        .then( response => {return response.text();})
        .then(doDisplay);
}

/**
 * Add to cart function.
 * Creates a post request with the body containing the product ID (the product added by the user).
 * Responds with the product added to the shopping cart.
 * @param {number} productID The product ID being added to the cart.
 */
function addToCart(productID) {
    var req = new Request("../backend/adding_response.php", 
    {
        method: "post",
        type: "basic",
        headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"},
        body: "addToCart=" + productID
        
    });

    fetch(req)
        .then( response => {return response.text();})
        .then(doDisplay);
}

/**
 * Remove from cart function.
 * Creates a post request with the body containing the product ID (the product removed by the user).
 * Responds with the product removed from the shopping cart.
 * @param {number} productID The product ID being removed from the cart.
 */
function removeFromCart(productID) {
    var req = new Request("../backend/removing_response.php", 
    {
        method: "post",
        type: "basic",
        headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"},
        body: "removeFromCart=" + productID
        
    });

    fetch(req) 
        .then(itemRemovalConfirmation())
        .then( response => {return response.text();})
        .then(doDisplay);
}

/**
 * Next page function.
 * Creates a post request with the body containing the action occurred (true).
 * Responds with the next page of product items.
 */
function nextPage() {
    var req = new Request("../backend/displaying_response.php", 
    {
        method: "post",
        type: "basic",
        headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"},
        body: "nextPage=true"
    });

    fetch(req)
        .then( response => {return response.text();})
        .then(doDisplay);
}

/**
 * Previous page function.
 * Creates a post request with the body containing the action occurred (true).
 * Responds with the previous page of product items.
 */
function prevPage() {
    var req = new Request("../backend/displaying_response.php", 
    {
        method: "post",
        type: "basic",
        headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"},
        body: "prevPage=true"
    });

    fetch(req)
        .then( response => {return response.text();})
        .then(doDisplay);
}

/**
 * More details function.
 * Creates a post request with the body containing the product ID (the product requested for more information).
 * Responds with the extra details involved in the product description.
 * @param {number} productID The product ID requesting more details.
 */
function moreDetails(productID) {
    var req = new Request("../backend/product_details_response.php", 
    {
        method: "post",
        type: "basic",
        headers: {"Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"},
        body: "moreDetails=" + productID
    });

    fetch(req)
        .then( response => {return response.text();})
        .then(doDisplay);
}

/**
 * Do display function.
 * Displays the current data received by the reponse of the post request.
 * Sets the inner HTML of the div dataSection to the response of the post request.
 * @param {string} data The data string formatted as HTML being displayed.
 */
function doDisplay(data) {
    document.getElementById("dataSection").innerHTML=data;
}

/**
 * Do display function.
 * Displays the current data received by the reponse of the post request.
 * Sets the inner HTML of the nav categorySection to the response of the post request.
 * @param {string} data The data string formatted as HTML being displayed.
 */
function doNavDisplay(data) {
    document.getElementById("categorySection").innerHTML=data;
}
