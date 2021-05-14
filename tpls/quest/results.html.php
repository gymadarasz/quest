<h1><encode $quest['name'] /></h1>

<flat $contact />
Name: {$name}<br />
Address: {$address}<br />
Email: {$email}<br />
Phone: {$phone}<br />
<br />

<ul class="list-group">
    <foreach $results as $result:>
        <flat $result />
        <li class="list-group-item">
            <strong>{$question_label}</strong><br />
            {$answer_label}
        </li>
    </foreach>
</ul>

