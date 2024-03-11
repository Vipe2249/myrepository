function sendDataToServer(products) {

    $.ajax({
        type: 'POST',
        url: '../db/insert_data.php',
        data: { products: JSON.stringify(products) },
        success: function(response) {
            console.log('Data inserted successfully:', response);
        },
        error: function(xhr, status, error) {
            console.error('Error inserting data:', error);
        }
    });
}

function parseXML(xml) {
    const products = [];
    const batchSize = 100;
    let count = 0;
    $(xml).find('product').each(function() {
        const categories = $(this).find('categories').text();
        if (categories.includes("Components") && categories.includes("CPU") && (categories.includes("AMD CPU") || categories.includes("Intel CPU"))) {
            const sku = $(this).find('sku').text();
            const name = $(this).find('name').text();
            const price = parseFloat($(this).find('rrp_incl').text());
            const description = $(this).find('description').text();
            const imageUrl = $(this).find('featured_image').text();
            const jhbStock = parseFloat($(this).find('jhbstock').text());
            const cptStock = parseFloat($(this).find('cptstock').text());
            const stock = jhbStock + cptStock;
            const url = name.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');

            const product = {
                sku: sku,
                name: name,
                price: price,
                description: description,
                image_url: imageUrl,
                stock: stock,
                url: url
            };


            console.log('URL:', url);

            products.push(product);
        }
        count++;
        if (count % batchSize === 0) {
            sendDataToServer(products);
            products.length = 0;
        }
    });
    if (products.length > 0) {
        sendDataToServer(products);
    }
}




function readFile() {
    $.ajax({
        type: "GET",
        url: "../db/feedhandler.xml", 
        dataType: "xml",
        success: function(xml) {
            parseXML(xml);
        },
        error: function(xhr, status, error) {
            console.error('Error reading XML file:', error);
        }
    });
}

// Call the function to read the XML file
readFile();
