<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{$title}</title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
    
        <style>
        .center-form {
            min-width: 400px;
            position: absolute;
            text-align: left;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /*font-size: 2.5rem*/
        }
        @media (max-width: 500px) {
            .center-form {
                min-width: 90%;
            }
        }
        </style>
    </head>
    <body>
        <include $__path . 'navigation.html.php' />
        <div class="container">
            <br />
            <include $__path . 'messages.html.php' />
            <include $__path . $page . '.html.php' />
        </div>
    </body>
</html>
