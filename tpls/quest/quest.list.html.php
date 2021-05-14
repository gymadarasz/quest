<h1>Quest list</h1>

<if $this->session->get('user_admin'):>
    <a href="{$__base}quests/new">Create new quest</a>
</if>

<ul class="list-group">
    <foreach $list as $item:>
        <flat $item />
        <li class="list-group-item">
            <a href="{$__base}quests/view?id={$id}&user_ref={$user_ref}">{$name}</a>
            <br />
            <br />
            <input type="text" id="link_{$id}" value="{$__base}quests/view?id={$id}&user_ref={$user_ref}" />
            <a href="javasctipt:void(0);" onclick="copyLink('link_{$id}');">Copy link</a>
            <br />
            <if $this->session->get('user_admin'):>
                <a href="{$__base}quests/edit?id={$id}">Edit</a>|<a href="{$__base}quests/delete?id={$id}">Delete</a>
            </if>
        </li>
    </foreach>
</ul>

<script>
    function copyLink(elemId) {
        var copyText = document.getElementById(elemId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert("Copied to the clipboard: " + copyText.value);
    }
</script>