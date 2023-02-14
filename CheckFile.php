<?php
//define('br', "<br>");

class CheckFile
{
    public array $request = array();
    private int $mega = 1048576;

    public function __construct($data)
    {
        $this->InputFiles($data);
    }


    public function InputFiles($data): CheckFile
    {
        $this->InitFiles($data);
        $this->runRules();
        return $this;
    }

    private function InitFiles($data): void
    {
        foreach ($data as $keys => $value) {
            $request_key = substr($keys, 0, strpos($keys, '.'));
            $request_method = substr($keys, strpos($keys, '.') + 1);

            //check method
            if (strtolower($_SERVER['REQUEST_METHOD']) !== $request_method) {
                continue;
            }


//            $this->div($request_key);
            if (!in_array($request_key, array_keys($_FILES))) {
                continue;
            }

            $this->addFiles(
                $request_key,
                $value);

        }
    }

    private function addFiles($request_key, $rules): void
    {
        $files = $_FILES[$request_key];
        //check file => single or multiple file
        // single => array (name => string)
        // multiple => array (name => array)

        //single
        if (!is_array($files['name'])) {
            $this->request['single.' . $request_key] = ['file' => $files,
                'rules' => $rules
            ];
        } else {
            $this->request[$request_key] = ['file' => $files,
                'rules' => $rules
            ];
        }
    }

    private function runRules(): void
    {
        foreach ($this->request as $key => $value) {
            if (strpos($key, 'single.') !== false) {
                $this->handlersingleFile($key);
            } else {
                $this->handlerFiles($key);
            }
        }
    }

    private function handlerFiles($key, $type = 'multiple'): void
    {
        $rules = $this->request[$key]['rules'];
        if (strpos($rules, 'size') !== false) {
            $this->size($key, $type);
        }
        if (strpos($rules, 'emex') !== false) {
            $this->emex($key, $type);
        }
        if (strpos($rules, 'required') !== false) {
            $this->required($key, $type);
        }

    }

    private function handlersingleFile($key): void
    {
        $rules = $this->request[$key]['rules'];
        if (strpos($rules, 'size') !== false) {
            $this->size($key);
        }
        if (strpos($rules, 'emex') !== false) {
            $this->emex($key);
        }
        if (strpos($rules, 'required') !== false) {
            $this->required($key);
        }

    }

    private function required($key, $type = 'single'): void
    {
        if ($type === 'single') {
            if (empty($this->request[$key]['file']['name'])) {
                $this->request[$key]['file']['error'] = ucwords("the " . substr($key, strpos($key, ".") + 1) . " is required");
            }
            return;
        }
        if (empty($this->request[$key]['file']['name'][0])) {
            $this->request[$key]['file']['error'][0] = ucwords("the " . $key . " is required");
        }
    }

    private function size($key, $type = 'single'): void
    {
        if ($type === 'single') {
            $rules_array = explode("|", $this->request[$key]['rules']);
            foreach ($rules_array as $rule) {
                if (strpos($rule, 'size:') !== false) {
                    $size = substr($rule, strpos($rule, ":") + 1);
                    $sizeB = $size * $this->mega;
                    if ($this->request[$key]['file']['size'] > $sizeB) {
                        $this->request[$key]['file']['error'] = ucwords("the " . substr($key, strpos($key, ".") + 1) . " is too large max size {$size}MB");
                        break;
                    }
                }
            }

            return;
        }

        foreach ($this->request[$key]['file']['size'] as $item => $value) {
            $rules_array = explode("|", $this->request[$key]['rules']);
            foreach ($rules_array as $rule) {
                if (strpos($rule, 'size:') !== false) {
                    $size = substr($rule, strpos($rule, ":") + 1);
                    $sizeB = $size * $this->mega;
                    if ($value > $sizeB) {
                        $this->request[$key]['file']['error'][$item] = ucwords("the " . $key . " is too large max size {$size}MB");
                        break;
                    }
                }
            }
        }
    }

    private function emex($key, $type = 'single'): void
    {
        if ($type === 'single') {
            $rules_array = explode("|", $this->request[$key]['rules']);
            foreach ($rules_array as $rule) {
                if (strpos($rule, 'emex:') !== false) {
                    $emex = substr($rule, strpos($rule, ":") + 1);
                    $emex_array = explode(",", $emex);
                    $name_array = explode('.', $this->request[$key]['file']['name']);
                    $file_emex = end($name_array);

                    if (!in_array($file_emex, $emex_array)) {
                        $this->request[$key]['file']['error'] = ucwords("the " . substr($key, strpos($key, ".") + 1) . " isn't allow");
                    }

                    break;
                }
            }

            return;
        }

        foreach ($this->request[$key]['file']['name'] as $item => $value) {
            $rules_array = explode("|", $this->request[$key]['rules']);
            foreach ($rules_array as $rule) {
                if (strpos($rule, 'emex:') !== false) {
                    $emex = substr($rule, strpos($rule, ":") + 1);
                    $emex_array = explode(",", $emex);
                    $name_array = explode('.', $value);
                    $file_emex = end($name_array);

                    if (!in_array($file_emex, $emex_array)) {
                        $this->request[$key]['file']['error'][$item] = ucwords("the " . $key . " isn't allow");
                    }
                    break;
                }

            }
        }
    }

    public function getError($key): string|array
    {
        if (in_array('single.' . $key, array_keys($this->request))) {
            if (!empty($this->request['single.' . $key]['file']['error'])) {
                return $this->request['single.' . $key]['file']['error'];
            }
        } else if (in_array($key, array_keys($this->request))) {
            $errors = '';
//            $this->print($this->request[$key]['file']);
            foreach ($this->request[$key]['file']['error'] as $idx => $error) {
                if (!empty($error)) {
                    $errors .= substr($this->request[$key]['file']['name'][$idx], 0, 20) .
                        " {$error} <br>";
                }
            }
            return $errors;
        }

        return '';
    }

    public function print($r)
    {
        echo "<pre>";
        var_dump($r);
        echo "</pre>";
    }

    public function div($value)
    {
        echo "<div>$value</div>";
    }
}