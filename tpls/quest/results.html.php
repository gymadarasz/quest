<h1><encode $quest['name'] /></h1>

<ul>
    <foreach $results as $result:>
        <flat $result />
        <li>
            <strong>{$question_label}</strong><br />
            {$answer_label}
        </li>
    </foreach>
</ul>


<flat $contact />
Name: {$name}<br />
Address: {$address}<br />
Email: {$email}<br />
Phone: {$phone}<br />