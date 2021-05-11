<form method="POST" action="{$__base}login">
    <input type="hidden" name="redirect" value="{@$redirect}">
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="password" placeholder="Password">
    <button>Login</button>
</form>
<a href="{$__base}registry">Sing up</a>
<a href="{$__base}forgot">Forgot password</a>