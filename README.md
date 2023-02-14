# checkPhp

* how to use

### open terminal and write

```shell 
php -S localhost:8080 
```

### open any browser and write

```shell
http://localhost:8080 
```

1 import check php class

```php
    require_once "./Check.php"
```

### Can check input (text, number, email)

## Check `type` and `required` and `min&max` length

```php
$emailExt = 'gmail.com,yahoo.com,hotmel.com';
$data = [
    //request name.(request method) => validation
    'name.post' => 'type:string|required|min:8|max:30',
    // emex => email extension (gmail.com ...etc)
    'email.post' => 'required|max:50|type:email|emex:' . $emailExt,
    'pass.post' => 'required|min:8|max:20',
    'num.post' => 'type:number|required|min:8|max:20'
];
// run
$inputs = Check::Input($data);
```

### can check input (file) single or multiple

## Check file `emex` & `required` & `size` *(MB)*

```php
$fileemex = 'png,jpg,jpeg,gif';
$file = [
    // request name.(method) => validation
    //emex => file type (png,rar,exe) ..etc
    'file.post' => 'required|size:3|emex:' . $fileemex,
    'mulfile.post' => 'required|size:15|emex:' . $fileemex
];
// run
$files = Check::CheckFile($data);
```

## Get error

## `input (string, email,number)...ect`

```php
// geterror(request key) => name or ...etc
 $inputs->getError('name')
```

## `file input`

```php
$files->getError('mulfile')
```

