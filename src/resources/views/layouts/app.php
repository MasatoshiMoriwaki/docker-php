
<!doctype html>
    <? include __DIR__ . '/../common/_head.php'?>
    <? include __DIR__ . '/../common/header.php'?>
<body>
    <?= msgDisplay($this->getMessage(MSG_TYPE_INFO)) ?>

    <div class="wrapper">
        <?= $_content ?>
    </div>
    <? include __DIR__ . '/../common/footer.php'?>
</body>
</html>