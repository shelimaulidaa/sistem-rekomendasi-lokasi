<?php
$content = file_get_contents('app/Http/Controllers/Manajer/ObservasiController.php');
$replacement = <<<PHP
    public function store(StoreObservasiRequest \$request)
    {
        \Illuminate\Support\Facades\Log::info('REQUEST DATA: ', \$request->all());
        \$data = \$request->validated();
PHP;

$content = str_replace(
    "    public function store(StoreObservasiRequest \$request)\n    {\n        \$data = \$request->validated();",
    $replacement,
    $content
);

file_put_contents('app/Http/Controllers/Manajer/ObservasiController.php', $content);
echo "Added log statement.";
