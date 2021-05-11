<h1>Contact list</h1>

<ul>
    <foreach $list as $item:>
        <flat $item />
        <li>
            Name: {$name}<br />
            Address: {$address}<br />
            Email: {$email}<br />
            Phone: {$phone}<br />
            <a href="{$__base}results?contact_id={$id}">See questionary results</a>
        </li>
    </foreach>
</ul>