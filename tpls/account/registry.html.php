<flat $_POST />

<form method="POST" action="{$__base}registry">
    <input class="form-control" type="email" name="email" placeholder="Email" value="{@$email}">
    <input class="form-control" type="password" name="password" placeholder="Password" value="">
    <button class="btn btn-primary">Sign up</button>
</form>
<a href="{$__base}login">Login</a>