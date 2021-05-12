<form method="POST" action="{$__base}login">
    <input type="hidden" name="redirect" value="{@$redirect}">
    <input class="form-control" type="email" name="email" placeholder="Email">
    <input class="form-control" type="password" name="password" placeholder="Password">
    <button class="btn btn-primary">Login</button>
</form>
<a href="{$__base}registry">Sing up</a>
<a href="{$__base}forgot">Forgot password</a>