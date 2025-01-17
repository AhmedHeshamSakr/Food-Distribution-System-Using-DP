<?php
require_once '../../config/config.php';
require_once '../Controllers/Paypal_Checkout_Validate.php';

function renderPaymentView($currency, $itemNumber) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Make a Donation</title>
        <link rel="stylesheet" href="../../public/paymentStyle.css">
        <script src="https://www.paypal.com/sdk/js?client-id=<?php echo PAYPAL_SANDBOX ? PAYPAL_SANDBOX_CLIENT_ID : PAYPAL_SANDBOX_CLIENT_SECRET; ?>&currency=<?php echo $currency; ?>"></script>
    </head>
    <body>
        <div class="container">
            <h1>Make a Donation</h1>
            <div class="panel">
                <div class="overlay hidden">
                    <div class="overlay-content">
                        <img src="css/loading.gif" alt="Processing..." />
                    </div>
                </div>

                <div class="panel-heading">
                    <h3 class="panel-title">Enter Donation Amount</h3>
                    
                    <form id="donationForm">
                        <label for="donationAmount">Amount (<?php echo $currency; ?>):</label>
                        <input type="number" id="donationAmount" name="donationAmount" min="1" step="0.01" required>
                    </form>
                </div>

                <div class="panel-body">
                    <div id="paymentResponse" class="hidden"></div>
                    <div id="paypal-button-container"></div>
                </div>
            </div>
        </div>
        
        <script>
            

            const donationAmountInput = document.getElementById('donationAmount');

// Update the URL dynamically on input change
donationAmountInput.addEventListener('input', () => {
    const amount = parseFloat(donationAmountInput.value);
    if (!isNaN(amount) && amount > 0) {
        const url = new URL(window.location.href);
        url.searchParams.set('donationAmount', amount);
        history.replaceState(null, '', url.toString());
    }
});

// PayPal button logic
paypal.Buttons({
    createOrder: (data, actions) => {
        const amount = parseFloat(new URL(window.location.href).searchParams.get('donationAmount'));
        if (amount <= 0 || isNaN(amount)) {
            alert('Please enter a valid donation amount.');
            return;
        }
        
        // <?
        // $db = new database();
        // $controller = new PaymentController($db);
        // $controller->validatePayment();
        // ?>
        return actions.order.create({
            
            purchase_units: [{
                custom_id: "<?php echo $itemNumber; ?>",
                amount: {
                    currency_code: "<?php echo $currency; ?>",
                    value: amount
                }
            }]
        });
    },
    onApprove: (data, actions) => {
        return actions.order.capture().then(orderData => {
            console.log('Order approved:', orderData);

            // Ensure updated donationAmount is sent to the backend
            const urlParams = new URL(window.location.href).searchParams;
            const updatedAmount = urlParams.get('donationAmount') || donationAmountInput.value;

            fetch('../Controllers/Paypal_Checkout_Validate.php', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: encodeFormData({ donationAmount: updatedAmount })
            });
        });
    }
}).render('#paypal-button-container');

const encodeFormData = (data) => {
    const urlSearchParams = new URLSearchParams();
    for (const key in data) {
        urlSearchParams.append(key, data[key]);
    }
    return urlSearchParams.toString();
}



            
            const setProcessing = (isProcessing) => {
                if (isProcessing) {
                    document.querySelector(".overlay").classList.remove("hidden");
                } else {
                    document.querySelector(".overlay").classList.add("hidden");
                }
            }
        </script>
    </body>
    </html>
    <?php
}
