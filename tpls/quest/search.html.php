<h1>Search</h1>

<form method="GET">
    <input class="form-control" type="text" name="keyword" value="<encode $keyword />">
    <button class="btn btn-primary" type="submit">Search</button>
</form>

<if @$contacts:>
    <h3>Contacts</h3>
    <ul class="list-group">
        <foreach $contacts as $contact:>
            <flat $contact />
            <li class="list-group-item">{$name}<a href="{$__base}results?contact_id={$contact_id}">(Details...)</a></li>
        </foreach>
    </ul>
</if>

<if @$quests:>
    <h3>Quests</h3>
    <ul class="list-group">
        <foreach $quests as $quest:>
            <flat $quest />
            <li class="list-group-item">{$name}<a href="{$__base}quests/view?id={$quest_id}&user_ref={$user_ref}">(Details...)</a></li>
        </foreach>
    </ul>
</if>