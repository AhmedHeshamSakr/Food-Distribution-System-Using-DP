<?php
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

            paypal.Buttons({
                createOrder: (data, actions) => {
                    const amount = parseFloat(donationAmountInput.value);
                    if (amount <= 0 || isNaN(amount)) {
                        alert('Please enter a valid donation amount.');
                        return;
                    }

                    return actions.order.create({
                        "purchase_units": [{
                            "custom_id": "<?php echo $itemNumber; ?>",
                            "description": "Donation",
                            "amount": {
                                "currency_code": "<?php echo $currency; ?>",
                                "value": amount,
                                "breakdown": {
                                    "item_total": {
                                        "currency_code": "<?php echo $currency; ?>",
                                        "value": amount
                                    }
                                }
                            },
                            "items": [
                                {
                                    "name": "Donation",
                                    "description": "Donation",
                                    "unit_amount": {
                                        "currency_code": "<?php echo $currency; ?>",
                                        "value": amount
                                    },
                                    "quantity": "1",
                                    "category": "DIGITAL_GOODS"
                                }
                            ]
                        }]
                    });
                },
                onApprove: (data, actions) => {
                    return actions.order.capture().then(function (orderData) {
                        setProcessing(true);

                        var postData = { 
                            paypal_order_check: 1, 
                            order_id: orderData.id, 
                            donation_amount: parseFloat(donationAmountInput.value) 
                        };
                        
                        fetch('payment_validate.php', {
                            method: 'POST',
                            headers: { 'Accept': 'application/json' },
                            body: encodeFormData(postData)
                        })
                        .then((response) => response.json())
                        .then((result) => {
                            if (result.status == 1) {
                                window.location.href = "payment-status.php?checkout_ref_id=" + result.ref_id;
                            } else {
                                const messageContainer = document.querySelector("#paymentResponse");
                                messageContainer.classList.remove("hidden");
                                messageContainer.textContent = result.msg;

                                setTimeout(function () {
                                    messageContainer.classList.add("hidden");
                                    messageContainer.textContent = "";
                                }, 5000);
                            }
                            setProcessing(false);
                        })
                        .catch(error => console.log(error));
                    });
                }
            }).render('#paypal-button-container');

            const encodeFormData = (data) => {
                var form_data = new FormData();
                for (var key in data) {
                    form_data.append(key, data[key]);
                }
                return form_data;
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