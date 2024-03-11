<?php
session_start();
require_once('../db/dbcon.php');

// Function to remove an item from the cart by SKU
function removeItemFromCart($sku) {
    if(isset($_SESSION['cart'][$sku])) {
        unset($_SESSION['cart'][$sku]);
    }
}

// Check if 'remove-from-cart' parameter is present in the URL
if(isset($_GET['remove-from-cart'])) {
    // Get the SKU of the product to remove from cart
    $product_sku = $_GET['remove-from-cart'];
    // Call the function to remove the item from the cart
    removeItemFromCart($product_sku);
}

$total_quantity = 0;
$total_price = 0;
$parcels = []; // Initialize the parcels array
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $sku => $item) {
        // Fetch additional attributes (L, W, H, KG) from the database based on SKU
        $query = "SELECT L, W, H, KG FROM Products WHERE SKU = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $sku);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $total_quantity += $item['quantity'];
        $total_price += ($item['quantity'] * $item['price']);

        // Create a new parcel for each quantity of the product
        for ($i = 0; $i < $item['quantity']; $i++) {
            $parcel = array(
                'submitted_length_cm' => $product['L'],
                'submitted_width_cm' => $product['W'],
                'submitted_height_cm' => $product['H'],
                'submitted_weight_kg' => $product['KG']
            );
            $parcels[] = $parcel; // Add the parcel to the parcels array
        }

        // Output fetched attributes (optional)
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="http://localhost/infinityware/styles/styles.css">
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="icon" href="../infinityware/images/infinitywareicon.png" type="image/x-icon"/>
</head>
<body>
<div class="page-content">
    <?php include("../header/header.php"); ?>
    <div class="container">
        <h1>Checkout</h1>
        <?php if(empty($_SESSION['cart'])) { ?>
            <p>Your cart is empty. Please add items to your cart before proceeding to checkout.</p>
        <?php } else { ?>
            <form action="https://sandbox.payfast.co.za/eng/process" method="post">
                <div class="checkout-body">
                    <div class="checkout-fields" style="display: flex; flex-direction: column;">
                        <strong>Details</strong>
                        <div class="first-and-last" style="width: 100%; display: flex;">
                            <input id="first-name" style="width: 50%; margin-right: 10px;" type="text" placeholder="First Name" required>
                            <input id="last-name" style="width: 50%;"type="text" placeholder="Last Name" required>
                        </div>
                        <div class="street-address" style="width: 100%;">
                            <strong>Street Address</strong>
                            <input id="deliveryStreetAddress" style="width: 100%;" type="text" placeholder="Street Address and house number" required>
                            <input id="deliveryApartment" style="width: 100%;" type="text" placeholder="Apartment, suite, etc (optional)">
                            <strong>City</strong>
                            <input id="deliveryCity" style="width: 100%;" type="text" placeholder="Town / City" required>
                            <select id="deliveryProvince" style="width: 100%;" name="" required>
                                <option>Select an option</option>
                                <option>Eastern Cape</option>
                                <option>Free State</option>
                                <option>Gauteng</option>
                                <option>KwaZulu-Natal</option>
                                <option>Limpopo</option>
                                <option>Mpumalanga</option>
                                <option>Northern Cape</option>
                                <option>North West</option>
                                <option>Western Cape</option>
                            </select>
                            <input id="deliveryPostalCode" style="width: 100%;" type="text" placeholder="Postal / Zip Code">
                        </div>
                        <div class="contact-details" style="width: 100%;">
                            <strong>Contact Details</strong>
                            <input type="text" style="width: 100%;" placeholder="Phone Number">
                            <input type="text" style="width: 100%;" placeholder="Email address" required>
                        </div>
                        <div class="order-notes" style="width: 100%; display: flex; flex-direction: column;">
                            <div class="label"><strong>Order Notes (optional)</strong></div>
                            <p>
                                <textarea name="" id="" cols="30" rows="10"></textarea>
                            </p>
                        </div>
                    </div>
                    <div class="checkout-summary" >
                        <strong>Order Summary</strong>
                        <table>
                            <tbody class="summary">
                                <tr>
                                    <th class="summary-title" style="text-align: left;">Subtotal</th>
                                    <td class="summary-subtotal" style="text-align: right;">R<?php echo htmlspecialchars($total_price, ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                                <tr>
                                    <th class="summary-title" style="text-align: left;">Shipping</th>
                                    <td class="summary-shipping" style="text-align: right;">R0.00</td>
                                </tr>
                                <tr>
                                    <th class="summary-title" style="text-align: left;">Total</th>
                                    <td class="summary-total" style="text-align: right; font-weight: bold;">R<?php echo htmlspecialchars($total_price, ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="disclaimer" style="display: flex; align-content: flex-start;">
                            <input type="checkbox" name="" id="" required><span class="small">I hereby consent to providing my personal information inputted in this form to be used for delivery of the service</span>
                        </div>
                        <div class="placeorder" style="width: 100%;">
                            <button type="submit" class="placeorder">Place Order</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php } ?>
    </div>
</div>
<?php include("../header/footer.php");?>
<script>    
    function getFormData() {
        return {
            collection_address: {
                type: "business",
                company: "uAfrica.com",
                street_address: "1188 Lois Avenue",
                local_area: "Menlyn",
                city: "Pretoria",
                zone: "Gauteng",
                country: "ZA",
                code: "0181",
                lat: -25.7863272,
                lng: 28.277583
            },
            delivery_address: {
                type: "business",
                company: "uAfrica.com",
                street_address: document.getElementById("deliveryStreetAddress").value,
                local_area: "Menlyn",
                city: document.getElementById("deliveryCity").value,
                zone: document.getElementById("deliveryProvince").value,
                country: "ZA",
                code: document.getElementById("deliveryPostalCode").value,
            },
            parcels: [
                <?php 
                foreach ($parcels as $parcel) {
                    echo "{\n";
                    echo "    submitted_length_cm: " . htmlspecialchars($parcel['submitted_length_cm'], ENT_QUOTES, 'UTF-8') . ",\n";
                    echo "    submitted_width_cm: " . htmlspecialchars($parcel['submitted_width_cm'], ENT_QUOTES, 'UTF-8') . ",\n";
                    echo "    submitted_height_cm: " . htmlspecialchars($parcel['submitted_height_cm'], ENT_QUOTES, 'UTF-8') . ",\n";
                    echo "    submitted_weight_kg: " . htmlspecialchars($parcel['submitted_weight_kg'], ENT_QUOTES, 'UTF-8') . "\n";
                    echo "},\n";
                }
                ?>
            ],
        };
    }

    function getShippingRates() {
        const formData = getFormData();
        const apiUrl = "https://api.shiplogic.com/v2/rates";
        const bearerToken = "a601d99c75fc4c64b5a64288f97d52b4"; // Change this to your actual bearer token

        fetch(apiUrl, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${bearerToken}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            // Handle the response data here
            console.log(data);

            // Extract the rate from the response
            const rate = data.rates[0].rate; // Assuming there's only one rate in the array
            document.querySelector(".summary-shipping").innerHTML = `R${rate}`
            
            // Now you can use the 'rate' variable as needed
            console.log("Rate:", rate);
            
            // Send the rate value via AJAX to process.php
            $.ajax({
                type: 'POST',
                url: 'http://localhost/infinityware/php/process.php',
                data: {variable: rate},
                success: function(response){
                    $('.placeorder').html(`${response}`);
                }
            });
        })
        
        .catch(error => {
            // Handle errors here
            console.error("Error:", error);
        });
    }

    // Call getShippingRates initially
    getShippingRates();

    // Set interval to call getShippingRates every 5 seconds
    setInterval(getShippingRates, 1000);

    function getTotalRates() {
        const formData = getFormData();
        const apiUrl = "https://api.shiplogic.com/v2/rates";
        const bearerToken = "a601d99c75fc4c64b5a64288f97d52b4"; // Change this to your actual bearer token

        fetch(apiUrl, {
            method: "POST",
            headers: {
                "Authorization": `Bearer ${bearerToken}`,
                "Content-Type": "application/json"
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            // Handle the response data here
            console.log(data);

            // Extract the rate from the response
            const rate = data.rates[0].rate; // Assuming there's only one rate in the array
            document.querySelector(".summary-shipping").innerHTML = `R${rate}`
            
            // Now you can use the 'rate' variable as needed
            console.log("Rate:", rate);
            
            // Send the rate value via AJAX to process.php
            $.ajax({
                type: 'POST',
                url: '/infinityware/php/total.php',
                data: {variable: rate},
                success: function(response){
                    $('.summary-total').html(`R${response}`);
                }
            });
        })
        
        .catch(error => {
            // Handle errors here
            console.error("Error:", error);
        });

    }

    // Call getTotalRates initially
    getTotalRates();

    // Set interval to call getTotalRates every 5 seconds
    setInterval(getTotalRates, 1000);
</script>
</body>
</html>
