<ul>
    <if $this->session->get('user_id'):>
        <li><a href="{$__base}home">Home</a></li>
        <li><a href="{$__base}quests">Quests</a></li>
        <li><a href="{$__base}contacts">Contacts</a></li>
        <li><a href="{$__base}search">Search</a></li>
        <li><a href="{$__base}logout">Logout</a></li>
    <else>
        <li><a href="{$__base}login">Login</a></li>
    </if>
</ul>