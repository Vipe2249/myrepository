<?php
session_start();
require_once('../db/dbcon.php');

$total_quantity = 0;
if(isset($_SESSION['cart'])) {
    foreach($_SESSION['cart'] as $item) {
        $total_quantity += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking</title>
    <link rel="stylesheet" href="/infinityware/styles/styles.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/207b037cfb.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../infinityware/images/infinitywareicon.png" type="image/x-icon"/>
    <style>
    </style>
</head>
<body>

<?php include("../header/header.php");?>
<div class="page-content">
    <div class="container">
        <h1>Tracking</h1>
        <div class="tracking-page">
            <div class="shipment-tracking">
                <form class="tracking-input" action="" method="get">
                    <input type="text" placeholder="Tracking Reference">
                    <button class="track-order" type="submit">Submit</button>
                </form>
            </div>

            <div class="tracking-results" style="display: none;">
                <div id="shipment-info"></div>
                <div id="tracking-events"></div>
                <div id="pod-images-container"></div>

                <div class="tracking-results-progress">
                    <div class="innerbar" style="border-radius: 100px; padding: 10px; background-color: #0077B6; display: flex; position: relative; z-index: -2;">
                        <div class="progress-icon" style="display: flex">
                        <div class="icon">
                        <i class="fa-solid fa-check" style="color: white; background-color: black; padding: 10px; border-radius: 50px; overflow: hidden;"></i>
                        </div>
                        <div class="completed" style="position: absolute;">
                        <div class="checkmark">
                        <i class="fa-solid fa-check"></i>
                        </div>
                        </div>
                        </div>
                        
                        <div class="bar" style="height: 20px; background-color: white; width: calc(10%); position: absolute; z-index: -1; left: 1%; border-bottom-left-radius: 10px; border-top-left-radius: 10px;"></div>
                        
                        <div class="progress-icon" style="display: flex">
                        <div class="icon">
                        <i class="fa-solid fa-truck" style="color: white; background-color: black; padding: 10px; border-radius: 50px; overflow: hidden;"></i>
                        </div>
                        <div class="completed" style="position: absolute;">
                        <div class="checkmark">
                        <i class="fa-solid fa-check"></i>
                        </div>
                        </div>
                        </div>
                        <div class="bar" style="height: 20px; background-color: white; width: 20%; position: absolute; z-index: -1; left: 10%;"></div>

                        <div class="progress-icon" style="display: flex">
                        <div class="icon">
                        <i class="fa-solid fa-warehouse" style="color: white; background-color: black; padding: 10px; border-radius: 50px; overflow: hidden;"></i>
                        </div>
                        <div class="completed" style="position: absolute;">
                        <div class="checkmark">
                        <i class="fa-solid fa-check"></i>
                        </div>
                        </div>
                        </div>
                        <div class="bar" style="height: 20px; background-color: white; width: 20%; position: absolute; z-index: -1; left: 30%;"></div>

                        <div class="progress-icon" style="display: flex">
                        <div class="icon">
                        <i class="fa-solid fa-truck" style="color: white; background-color: black; padding: 10px; border-radius: 50px; overflow: hidden;"></i>
                        </div>
                        <div class="completed" style="position: absolute;">
                        <div class="checkmark">
                        <i class="fa-solid fa-check"></i>
                        </div>
                        </div>
                        </div>
                        <div class="bar" style="height: 20px; background-color: white; width: 20%; position: absolute; z-index: -1; left: 50%;"></div>

                        <div class="progress-icon" style="display: flex">
                        <div class="icon">
                        <i class="fa-solid fa-box" style="color: white; background-color: black; padding: 10px; border-radius: 50px; overflow: hidden;"></i>
                        </div>
                        <div class="completed" style="position: absolute;">
                        <div class="checkmark">
                        <i class="fa-solid fa-check"></i>
                        </div>
                        </div>
                        </div>
                        <div class="bar" style="height: 20px; background-color: white; width: 30%; position: absolute; z-index: -1; left: 69%; border-bottom-right-radius: 10px; border-top-right-radius: 10px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../header/footer.php");?>

<script>

    function getShipmentTracking(trackingReference, providerId) {
        const endpoint = `https://api.shiplogic.com/v2/tracking/shipments?tracking_reference=${trackingReference}&provider_id=${providerId}`;

        fetch(endpoint)
            .then(response => response.json())
            .then(data => {

                handleShipmentTracking(data);
            })
            .catch(error => {
                console.error('Error fetching shipment tracking:', error);
            });
    }


    function handleShipmentTracking(trackingData) {

        if (trackingData.shipments && trackingData.shipments.length > 0) {

            const shipment = trackingData.shipments[0];

            const shipmentInfoContainer = document.getElementById('shipment-info');
            shipmentInfoContainer.innerHTML = `
                <p>Shipment ID: ${shipment.shipment_id}</p>
                <p>Tracking Reference: ${shipment.short_tracking_reference}</p>
                <p>Status: ${shipment.status}</p>
            `;

            if (shipment.tracking_events && shipment.tracking_events.length > 0) {
            const trackingEventsContainer = document.getElementById('tracking-events');
            trackingEventsContainer.innerHTML = '<h3>Tracking Events:</h3>';
            
            shipment.tracking_events.forEach(event => {
                const eventElement = document.createElement('div');
                eventElement.innerHTML = `
                    <p>Event ID: ${event.id}</p>
                    <p>Date: ${event.date}</p>
                    <p>Status: ${event.status}</p>
                    <p>Message: ${event.message}</p>
                    <hr>
                `;
                trackingEventsContainer.appendChild(eventElement);
            });
        } else {
            console.log("No tracking events found for this shipment.");
        }
        

        if (shipment.tracking_events) {
            shipment.tracking_events.forEach(event => {
                if (event.message === "POD files captured" && event.data && event.data.images) {
                    const podImagesContainer = document.getElementById('pod-images-container');
                    event.data.images.forEach(image => {
                        const imageElement = document.createElement('img');
                        imageElement.src = image;
                        podImagesContainer.appendChild(imageElement);
                    });
                }
            });
        }


            if (shipment.tracking_events && shipment.tracking_events.length > 0) {
                const latestEvent = shipment.tracking_events[0]; 
                const eventStatus = latestEvent.status; 


                const bars = document.querySelectorAll('.innerbar > .bar');

                const checkmark = document.querySelectorAll('.checkmark');


                bars.forEach(bar => {
                    bar.style.backgroundColor = 'white';
                });


                switch (eventStatus) {
                    case 'submitted':
                        bars[0].style.backgroundColor = 'black';
                        checkmark[0].style.visibility = 'unset'; 
                        break;
                    case 'collected':
                        bars[1].style.backgroundColor = 'black';
                        bars[0].style.backgroundColor = 'black';
                        checkmark[0].style.visibility = 'unset';
                        checkmark[1].style.visibility = 'unset';
                        break;
                    case 'at-destination-hub':
                        bars[2].style.backgroundColor = 'black';
                        bars[1].style.backgroundColor = 'black';
                        bars[0].style.backgroundColor = 'black';
                        checkmark[0].style.visibility = 'unset';
                        checkmark[1].style.visibility = 'unset';
                        checkmark[2].style.visibility = 'unset';
                        break;
                    case 'out-for-delivery':
                        bars[3].style.backgroundColor = 'black';
                        bars[2].style.backgroundColor = 'black';
                        bars[1].style.backgroundColor = 'black';
                        bars[0].style.backgroundColor = 'black';
                        checkmark[0].style.visibility = 'unset';
                        checkmark[1].style.visibility = 'unset';
                        checkmark[2].style.visibility = 'unset';
                        checkmark[3].style.visibility = 'unset';
                        break;
                    case 'delivered':
                        bars[4].style.backgroundColor = 'black';
                        bars[3].style.backgroundColor = 'black';
                        bars[2].style.backgroundColor = 'black';
                        bars[1].style.backgroundColor = 'black';
                        bars[0].style.backgroundColor = 'black';
                        checkmark[0].style.visibility = 'unset';
                        checkmark[1].style.visibility = 'unset';
                        checkmark[2].style.visibility = 'unset';
                        checkmark[3].style.visibility = 'unset';
                        checkmark[4].style.visibility = 'unset';
                        break;
                    default:
                        break;
                }
            } else {
                console.log("No tracking events found for this shipment.");
            }
            

            document.querySelector('.tracking-results').style.display = 'unset';
        } else {
            console.log("No shipments found for the provided tracking reference.");
        }
    }


    document.addEventListener('DOMContentLoaded', function() {

        const form = document.querySelector('.shipment-tracking form');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const input = document.querySelector('.tracking-input input');
            const trackingReference = input.value;
            const providerId = '10'; 
            getShipmentTracking(trackingReference, providerId);
        });
    });
</script>

</body>
</html>
