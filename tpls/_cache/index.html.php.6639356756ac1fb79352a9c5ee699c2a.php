<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo htmlentities($title); ?></title>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
    
    </head>
    <body>
        <?php $__cacheFile = $this->getCachefile($__path . 'navigation.html.php' ); if (!file_exists($__cacheFile )) $this->process($__path . 'navigation.html.php' , get_defined_vars()); include $this->getCachefile($__path . 'navigation.html.php' ); ?>
        <div class="container">
            <?php $__cacheFile = $this->getCachefile($__path . 'messages.html.php' ); if (!file_exists($__cacheFile )) $this->process($__path . 'messages.html.php' , get_defined_vars()); include $this->getCachefile($__path . 'messages.html.php' ); ?>
            <?php $__cacheFile = $this->getCachefile($__path . $page . '.html.php' ); if (!file_exists($__cacheFile )) $this->process($__path . $page . '.html.php' , get_defined_vars()); include $this->getCachefile($__path . $page . '.html.php' ); ?>
        </div>
    </body>
</html>
