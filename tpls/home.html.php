<h1>Home</h1>

<if $user_subscribed:>
    Subscribed until: {$user_subscribed_until_at}
<else>
    Please subscribe
    <div>
        [PAYPAL]
    </div>
</if>
<br />
<a href="{$__base}delete-me">Delete my account</a>