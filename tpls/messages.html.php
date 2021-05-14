<flat $messages />

<if !empty($error):>
    <foreach $error as $message:>
        <div class="alert alert-danger" role="alert">{$message}</div>
    </foreach>
</if>

<if !empty($alert):>
    <foreach $alert as $message:>
        <div class="alert alert-warning" role="alert">{$message}</div>
    </foreach>
</if>

<if !empty($success):>
    <foreach $success as $message:>
        <div class="alert alert-success" role="alert">{$message}</div>
    </foreach>
</if>

<if !empty($info):>
    <foreach $info as $message:>
        <div class="alert alert-info" role="alert">{$message}</div>
    </foreach>
</if>

<if !empty($debug):>
    <ul class="debug" style="color:lightgreen; background: black;">
        <foreach $debug as $message:>
            <li><pre>{$message}</pre></li>
        </foreach>
    </ul>
</if>