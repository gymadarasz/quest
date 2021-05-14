<h1>Home</h1>

<if $user_subscribed:>
    Subscribed until: {$user_subscribed_until_at}
<else>
    Please subscribe, only <?php echo $this->config::QUEST['paypal_checkout_amount']; ?> <?php echo $this->config::QUEST['paypal_checkout_currency']; ?>
    for <?php echo $this->config::QUEST['paypal_checkout_subscription_long']; ?> period.
    <br />
    <div>
        
        <script src="https://www.paypalobjects.com/api/checkout.js"></script>

        <div id="paypal-button"></div>

        <script>
        paypal.Button.render({
            env: '{$paypal_env}', //'sandbox', // Or 'production'
            commit: true, // Optional: show a 'Pay Now' button in the checkout flow
            style: {
                layout: 'vertical'
            },
            funding: {
                allowed: [ paypal.FUNDING.CARD ],
                disallowed: [ paypal.FUNDING.CREDIT ]
            },
            // Set up the payment:
            // 1. Add a payment callback
            payment: function(data, actions) {
            // 2. Make a request to your server
            return actions.request.post('{$paypal_create_payment_callback}')
                .then(function(res) {
                // 3. Return res.id from the response
                return res.id;
                });
            },
            // Execute the payment:
            // 1. Add an onAuthorize callback
            onAuthorize: function(data, actions) {
            // 2. Make a request to your server
            return actions.request.post('{$paypal_execute_payment_callback}', {
                paymentID: data.paymentID,
                payerID:   data.payerID
            })
                .then(function(res) {
                // 3. Show the buyer a confirmation message.
                document.location.href = '{$__base}home';
                });
            }
        }, '#paypal-button');
        </script>
        

    </div>
</if>
<br />
<a href="{$__base}delete-me" onclick="return confirm('You are attempting to delete your account. This could cause a loss of your subscription and other data. The operation can not be reverted. Are you sure?')">Delete my account</a>