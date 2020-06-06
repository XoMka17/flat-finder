<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php
    require_once 'Parser.php';

    $parser = new Parser();

    $parser->find();
?>

<div class="home">
    <div class="home__container">
        <div class="home__column">
            <table>
                <tbody id="olx">
                <?php echo $parser->getOlxInfo(); ?>
                </tbody>
            </table>
        </div>
        <div id="realty" class="home__column">
            <?php echo $parser->getRealtyInfo(); ?>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="js/custom.js"></script>

</body>
</html>

