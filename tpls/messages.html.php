<flat $messages />

<if !empty($error):>
    <ul class="error" style="color:red;">
        <foreach $error as $message:>
            <li>{$message}</li>
        </foreach>
    </ul>
</if>

<if !empty($alert):>
    <ul class="alert" style="color:orange;">
        <foreach $alert as $message:>
            <li>{$message}</li>
        </foreach>
    </ul>
</if>

<if !empty($success):>
    <ul class="success" style="color:green;">
        <foreach $success as $message:>
            <li>{$message}</li>
        </foreach>
    </ul>
</if>

<if !empty($info):>
    <ul class="info" style="color:blue;">
        <foreach $info as $message:>
            <li>{$message}</li>
        </foreach>
    </ul>
</if>

<if !empty($debug):>
    <ul class="debug" style="color:lightgreen; background: black;">
        <foreach $debug as $message:>
            <li><pre>{$message}</pre></li>
        </foreach>
    </ul>
</if>