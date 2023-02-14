<?php
require_once 'CheckInput.php';
require_once 'CheckFile.php';

class Check
{

    public static function Input($data): CheckInput
    {
        return new CheckInput($data);
    }

    public static function CheckFile($data): CheckFile
    {
        return new CheckFile($data);
    }

}