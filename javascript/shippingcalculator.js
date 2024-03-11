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
            {
                submitted_length_cm: 42.5, // You might need to capture this data from the form if available
                submitted_width_cm: 38.5, // You might need to capture this data from the form if available
                submitted_height_cm: 5.5, // You might need to capture this data from the form if available
                submitted_weight_kg: 3 // You might need to capture this data from the form if available
            }
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
            url: 'process.php',
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

// Call getShippingRates initially
getShippingRates();

// Set interval to call getShippingRates every 5 seconds
setInterval(getShippingRates, 5000);

