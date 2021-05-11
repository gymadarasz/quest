<h1>{$name}</h1>

<form method="POST" action="quest/quests/fill?quest_id=4">
    <input type="hidden" name="csrf" value="{$__csrf}">

    <ul>
        <foreach $questions as $question:>
            <li>
                <h3><encode $question['label'] /></h3>
                <ul>
                    <foreach $question['answers'] as $answer:>
                        <li>
                            <input type="radio" id="answer_<?php echo (int)$answer['id']?>" name="quest[<?php echo (int)$question['id']?>]" value="<?php echo (int)$answer['id']?>" required />
                            <label for="answer_<?php echo (int)$answer['id']?>"><encode $answer['label'] /></label>
                        </li>
                    </foreach>
                </ul>
            </li>
        </foreach>
    </ul>

    <input type="text" name="name" value="" placeholder="Name" required /><br />
    <input type="text" name="address" value="" placeholder="Address" /><br />
    <input type="text" name="email" value="" placeholder="Email" /><br />
    <input type="text" name="phone" value="" placeholder="Phone" /><br />

    <button type="submit">Send</button>
</form>