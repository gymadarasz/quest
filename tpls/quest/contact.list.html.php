<h1>Contact list</h1>

<ul class="list-group">
    <foreach $list as $item:>
        <flat $item />
        <li class="list-group-item">
            Name: {$name}<br />
            Address: {$address}<br />
            Email: {$email}<br />
            Phone: {$phone}<br />
            <a href="{$__base}results?contact_id={$id}">See questionary results</a>
            <br />
            <br />
        </li>
    </foreach>
</ul>