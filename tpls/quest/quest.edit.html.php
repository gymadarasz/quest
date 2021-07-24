<h1>Edit quest</h1>

<form method="POST" action="{$action}">
    <input type="hidden" name="csrf" value="{$__csrf}">
    <input type="hidden" name="id" value="{$id}">

    <input class="form-control" type="text" name="name" value="{$name}" placeholder="Name" required />

    <button class="btn btn-primary" type="submit">{$button}</button>
</form>

<ul>
    <foreach $questions as $question:>
        <li>
            <form method="POST" action="{$__base}quests/question/update?quest_id={$id}&question_id=<?php echo (int)$question['id']?>">
                <input type="hidden" name="csrf" value="{$__csrf}">
                <input class="form-control" type="text" name="label" value="<?php echo $question['label']?>" placeholder="Question" required />
                <input class="form-control" type="number" name="ord" value="<?php echo $question['ord']?>" placeholder="Order" required />
                <button class="btn btn-primary" type="submit">Edit question</button>
            </form>
            <a href="{$__base}quest/question/delete?quest_id={$id}&question_id=<?php echo (int)$question['id']?>">(Delete)</a>
            <ul>
                <foreach $question['answers'] as $answer:>
                    <li>
                        <form method="POST" action="{$__base}quest/question/answer/update?quest_id={$id}&answer_id=<?php echo (int)$answer['id']?>">
                            <input type="hidden" name="csrf" value="{$__csrf}">
                            <input class="form-control" type="text" name="label" value="<?php echo $answer['label']?>" placeholder="Option" required />
                            <input class="form-control" type="number" name="ord" value="<?php echo $answer['ord']?>" placeholder="Order" required />
                            <button class="btn btn-primary" type="submit">Edit option</button>
                        </form>
                        <a href="{$__base}quest/question/answer/delete?quest_id={$id}&answer_id=<?php echo (int)$answer['id']?>">(Delete)</a>
                    </li>
                </foreach>
            </ul>
            <form method="POST" action="{$__base}quests/question/answer/create?quest_id={$id}&question_id=<?php echo (int)$question['id']?>">
                <input type="hidden" name="csrf" value="{$__csrf}">
                <input class="form-control" type="text" name="label" value="" placeholder="Option" required />
                <button class="btn btn-primary" type="submit">Add option</button>
            </form>
        </li>
    </foreach>
</ul>

<form method="POST" action="{$__base}quests/question/create?quest_id={$id}">
    <input type="hidden" name="csrf" value="{$__csrf}">
    <input class="form-control" type="text" name="label" value="" placeholder="Question" required />
    <button class="btn btn-primary" type="submit">Add question</button>
</form>