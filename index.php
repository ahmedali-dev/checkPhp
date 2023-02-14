<?php
require_once 'Check.php';

$emailExt = 'gmail.com,yahoo.com,hotmel.com';
$data = [
    'name.post' => 'type:string|required|min:8|max:30',
    'email.post' => 'required|max:50|type:email|emex:' . $emailExt,
    'pass.post' => 'required|min:8|max:20',
    'num.post' => 'type:number|required|min:8|max:20'
];


$fileemex = 'png,jpg,jpeg,gif';
$file = [
    'file.post' => 'required|size:3|emex:' . $fileemex,
    'mulfile.post' => 'required|size:15|emex:' . $fileemex
];

$inputs = Check::Input($data);
$files = Check::CheckFile($file);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>check form test</title>
</head>

<body>


<form action="" method="post" enctype="multipart/form-data">
    <div><input type="text" name="name" value="<?= $inputs->getValue('name') ?>">
        <p><?= $inputs->getError('name') ?></p>
    </div>
    <div><input type="email" name="email" value="<?= $inputs->getValue('email') ?>"></div>
    <p><?= $inputs->getError('email') ?></p>
    <div><input type="password" name="pass" value="<?= $inputs->getValue('pass') ?>"></div>
    <p><?= $inputs->getError('pass') ?></p>
    <div><input type="text" name="num" placeholder="number" pattern="[0-9]+" value="<?= $inputs->getValue('num') ?>">
    </div>
    <p><?= $inputs->getError('num') ?></p>
    <div><input type="file" name='file'></div>
    <p><?= $files->getError('file') ?></p>
    <div><input type="file" name="mulfile[]" multiple></div>
    <p><?= $files->getError('mulfile') ?></p>
    <div><input type="submit" name="btn"></div>
</form>


</body>

</html>