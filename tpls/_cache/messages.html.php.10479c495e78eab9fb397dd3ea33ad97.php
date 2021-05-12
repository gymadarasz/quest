<?php foreach($messages  as $__key => $__value) $$__key = $__value; ?>

<?php if (!empty($error)): ?>
    <ul class="error" style="color:red;">
        <?php foreach ($error as $message): ?>
            <li><?php echo htmlentities($message); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($alert)): ?>
    <ul class="alert" style="color:orange;">
        <?php foreach ($alert as $message): ?>
            <li><?php echo htmlentities($message); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <ul class="success" style="color:green;">
        <?php foreach ($success as $message): ?>
            <li><?php echo htmlentities($message); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($info)): ?>
    <ul class="info" style="color:blue;">
        <?php foreach ($info as $message): ?>
            <li><?php echo htmlentities($message); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if (!empty($debug)): ?>
    <ul class="debug" style="color:lightgreen; background: black;">
        <?php foreach ($debug as $message): ?>
            <li><pre><?php echo htmlentities($message); ?></pre></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>