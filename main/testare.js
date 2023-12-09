var itemQuantities = {};
var priceMap = {};
var miniMenuVisible = false;
var orderData = [];
function increment(id, name, price) {
    var input = document.getElementById(id);
    var quantity = parseInt(input.value) + 1;
    input.value = quantity;
    addToMiniMenu(name, price, quantity);
}

function decrement(id, name, price) {
    var input = document.getElementById(id);
    var quantity = parseInt(input.value);
    if (quantity > 0) {
        input.value = quantity - 1;
        if (quantity - 1 === 0) {
            removeFromMiniMenu(name);
        } else {
            addToMiniMenu(name, price, quantity - 1);
        }
    }
}

function addToMiniMenu(name, price, quantity) {
    if (itemQuantities.hasOwnProperty(name)) {
        // Item already exists, update the quantity and total price
        var existingQuantity = itemQuantities[name];
        var newQuantity = quantity - existingQuantity;
        itemQuantities[name] = quantity;
        var totalPrice = (parseFloat(price) * quantity).toFixed(2);
        priceMap[name] = totalPrice;
        updateMiniMenu();
        addItemToList();
    } else {
        // Item doesn't exist, add it to the quantities and price map
        itemQuantities[name] = quantity;
        var totalPrice = (parseFloat(price) * quantity).toFixed(2);
        priceMap[name] = totalPrice;
        updateMiniMenu();
        addItemToList();
    }

    if (!miniMenuVisible) {
        toggleMiniMenuVisibility(); // Show the mini-menu if it's not visible
    }
}

function removeFromMiniMenu(name) {
    if (itemQuantities.hasOwnProperty(name)) {
        delete itemQuantities[name]; // Remove the item from quantities
        delete priceMap[name]; // Remove the item from the priceMap
        updateMiniMenu();
    }
    removeItemFromList();
}

function updateMiniMenu() {
    var miniMenuList = document.getElementById('miniMenuList');
    miniMenuList.innerHTML = ''; // Clear the mini menu

    var totalPrice = 0; // Variable to store the total price

    for (var itemName in itemQuantities) {
        var itemQuantity = itemQuantities[itemName];
        var itemPrice = getPrice(itemName); // Get the price for the item
        var itemTotalPrice = itemQuantity * itemPrice; // Calculate the total price for the item

        var item = document.createElement('li');
        item.classList.add('mini-menu-item');

        var itemDetails = document.createElement('div');
        itemDetails.classList.add('item-details');

        var nameElement = document.createElement('span');
        nameElement.classList.add('item-name');
        nameElement.textContent = itemQuantity + 'x ' + itemName;
        itemDetails.appendChild(nameElement);

        var priceElement = document.createElement('span');
        priceElement.classList.add('item-price');
        priceElement.textContent = '$' + itemTotalPrice.toFixed(2);
        itemDetails.appendChild(priceElement);

        item.appendChild(itemDetails);

        miniMenuList.appendChild(item);

        totalPrice += itemTotalPrice; // Add the item's total price to the overall total
    }

    var totalElement = document.createElement('li');
    totalElement.classList.add('mini-menu-item', 'total-item');

    var totalPriceElement = document.createElement('span');
    totalPriceElement.classList.add('total-price');
    totalPriceElement.textContent = '$' + totalPrice.toFixed(2);

    var totalLabelElement = document.createElement('span');
    totalLabelElement.textContent = 'Total: ';

    totalElement.appendChild(totalLabelElement);
    totalElement.appendChild(totalPriceElement);

    miniMenuList.appendChild(totalElement);
}



function getPrice(name) {
    var menuItems = document.getElementsByClassName('menu-item');
    for (var i = 0; i < menuItems.length; i++) {
        var menuItemName = menuItems[i].getElementsByClassName('menu-item-name')[0].textContent;
        if (menuItemName === name) {
            var priceString = menuItems[i].getElementsByClassName('menu-item-price')[0].textContent;
            var price = parseFloat(priceString.replace('$', ''));
            return price;
        }
    }
    return 0;
}

function toggleMiniMenuVisibility() {
    var miniMenu = document.getElementById('miniMenuList');
    var showButton = document.getElementById('showMiniMenuButton');
    var submitButton = document.getElementById('submitButton');

    miniMenuVisible = !miniMenuVisible;

    if (miniMenuVisible) {
        miniMenu.style.display = 'block';
        showButton.textContent = 'Hide Order';
        submitButton.style.display = 'block';  // Show the submit button
    } else {
        miniMenu.style.display = 'none';
        showButton.textContent = 'Show Order';
        submitButton.style.display = 'none';  // Hide the submit button
    }

    // Adjust the position of the submit button
    setTimeout(function() {
        var listHeight = miniMenu.clientHeight;
        submitButton.style.marginTop = (listHeight + 10) + 'px';
    }, 0);
}
function addItemToList() {
    var miniMenu = document.getElementById('miniMenuList');
    var submitButton = document.getElementById('submitButton');
    var listHeight = miniMenu.clientHeight;
    submitButton.style.marginTop = (listHeight + 10) + 'px';
}

// function removeItemFromList() {
//     var miniMenu = document.getElementById('miniMenuList');
//     var submitButton = document.getElementById('submitButton');
//     var listHeight = miniMenu.clientHeight;
//     submitButton.style.marginTop = (listHeight + 10) + 'px';
// }
//
// function submitOrder() {
//     var orderData = [];
//
//     for (var itemName in itemQuantities) {
//         var itemQuantity = itemQuantities[itemName];
//         var itemPrice = getPrice(itemName);
//         var itemTotalPrice = parseFloat(itemPrice) * itemQuantity;
//
//         orderData.push({
//             name: itemName,
//             quantity: itemQuantity,
//             totalPrice: itemTotalPrice.toFixed(2)
//         });
//     }
//
//
//     var customItemValue = document.getElementById('customItemInput').value.trim();
//     var customItemValue2 = document.getElementById('customItemInput2').value.trim();
//
//
//     var orderString = 'Order Summary:\n\n';
//
//     for (var i = 0; i < orderData.length; i++) {
//         var item = orderData[i];
//         var itemName = item.name;
//         var itemQuantity = item.quantity;
//         var itemTotalPrice = item.totalPrice;
//
//         orderString += 'Name'+itemName+' idMasa: '+customItemValue2+' Quantity: ' +itemQuantity + ' descriere:'+customItemValue+'status: ' +'processing' +'\n';
//     }
//
//     // Display the order summary in a pop-up window
//     var popupWindow = window.open("", "Order Summary", "width=400,height=300");
//     popupWindow.document.body.innerText = orderString;
// }
function submitOrder() {
    // Display "Please wait for the waiter to come" pop-up window
    var popupWindow = window.open("", "Waiter Notification", "width=400,height=200");
    popupWindow.document.write("<h1>Please wait for the waiter to come</h1>");

    var orderData = [];

    for (var itemName in itemQuantities) {
        var itemQuantity = itemQuantities[itemName];
        var itemPrice = getPrice(itemName);
        var itemTotalPrice = parseFloat(itemPrice) * itemQuantity;

        orderData.push({
            name: itemName,
            quantity: itemQuantity,
            totalPrice: itemTotalPrice.toFixed(2)
        });
    }

    var customItemValue = document.getElementById('customItemInput').value.trim();
    var customItemValue2 = document.getElementById('customItemInput2').value.trim();

    var orderSummary = [];

    for (var i = 0; i < orderData.length; i++) {
        var item = orderData[i];
        var itemName = item.name;
        var itemQuantity = item.quantity;
        var itemTotalPrice = item.totalPrice;

        var itemSummary = {
            name: itemName,
            quantity: itemQuantity,
            description: customItemValue,
            idMasa: customItemValue2,
            status: 'processing'
        };

        orderSummary.push(itemSummary);
    }

    // Send the order summary to the PHP script
    fetch('submit_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderSummary)
    })
        .then(response => {
            // Handle the response from the PHP script
            if (response.ok) {
                popupWindow.open();
                return response.text();
            } else {
                throw new Error('Failed to submit the order');
            }
        })
        .then(data => {
            console.log('Server response:', data);
            // Close the "Please wait for the waiter to come" pop-up window after 1 minute

        })
        .catch(error => {
            console.error('An error occurred:', error);

        });

    // Log the order data in the console
    console.log('Order data:', orderSummary);
}






function getOrderSummaryString(orderSummary) {
    var orderString = 'Order Summary:\n\n';
    for (var i = 0; i < orderSummary.length; i++) {
        var item = orderSummary[i];
        var itemName = item.name;
        var itemQuantity = item.quantity;
        var itemDescription = item.description;
        var itemMasaId = item.idMasa;
        var itemStatus = item.status;

        orderString += 'Name: ' + itemName + '\n';
        orderString += 'Quantity: ' + itemQuantity + '\n';
        orderString += 'Description: ' + itemDescription + '\n';
        orderString += 'idMasa: ' + itemMasaId + '\n';
        orderString += 'Status: ' + itemStatus + '\n';
        orderString += '\n';
    }
    return orderString;
}



function addCustomItem() {
    var itemNameInput = document.getElementById('customItemInput');
    var itemName = itemNameInput.value.trim();

    if (itemName === "") {
        return; // Don't add the item if the name is empty
    }

    // Display the custom item in the pop-up window
    var customItemText = document.createElement('p');
    customItemText.textContent = itemName;
    var popupWindow = window.open("", "Custom Item", "width=400,height=200");
    popupWindow.document.body.appendChild(customItemText);

    // Clear the item name input field
    itemNameInput.value = "";
}



