<h1>Quest list</h1>

<a href="{$__base}quests/new">Create new quest</a>

<ul>
    <foreach $list as $item:>
        <flat $item />
        <li><a href="{$__base}quests/view?id={$id}">{$name}</a>
        (<a href="{$__base}quests/edit?id={$id}">Edit</a>|<a href="{$__base}quests/delete?id={$id}">Delete</a>)</li>
    </foreach>
</ul>