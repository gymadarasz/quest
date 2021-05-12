<h1>New quest</h1>

<form method="POST" action="{$action}">
    <input type="hidden" name="csrf" value="{$__csrf}">
    <input class="form-control" type="text" name="name" value="" placeholder="Name" required />
    <button class="btn btn-primary" type="submit">{$button}</button>
</form>