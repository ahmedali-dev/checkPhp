<?php
define('br', "<br>");

class CheckInput
{
    public array $request = array();


    public function __construct($data)
    {
        $this->Inputs($data);
    }

    public function Inputs($data): CheckInput
    {
        $this->InitializeInput($data);
        $this->moreFilter();
        return $this;
    }

    private function InitializeInput($data): void
    {
        // request name => (name) if name found in $_REQUEST
        foreach ($data as $keys => $value) {
            $request_key = substr($keys, 0, strpos($keys, '.'));
            $request_method = substr($keys, strpos($keys, '.') + 1);
            $request_type = (function ($value) {
                $rules = explode("|", $value);
                foreach ($rules as $val) {

                    if (substr($val, 0, strpos($val, ":")) === 'type') {
                        return substr($val, strpos($val, ":") + 1);
                    }

                }
            })($value);

            $request_type = empty($request_type) ? 'string' : $request_type;
//            echo $request_type;
            //check method
            if (strtolower($_SERVER['REQUEST_METHOD']) !== $request_method) {
                continue;
            }

//            $this->div($request_key);
            if (!in_array($request_key, array_keys($_REQUEST))) {
                continue;
            }
            $this->Filter($request_type,
                $request_key,
                $value);

        }
    }

    private function Filter($type, $request_key, $rules): void
    {
        $value = $_REQUEST[$request_key];
        switch ($type) {
            case 'string':
                $value = $this->initFilter($value);
                $this->addRequest($request_key, $type, $value, $rules);
                break;
            case 'email':
                $value = $this->initFilter($value, FILTER_SANITIZE_EMAIL);
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addRequest($request_key, $type, $value, $rules, ucwords('email is not valid pls check your email'));


                } else {
                    if (!$this->getEMEX($rules, $value, '@')) {
                        $this->addRequest($request_key, $type, $value, $rules, ucwords('email is not allow to use'));
                    } else {
                        $this->addRequest($request_key, $type, $value, $rules);
                    }


                }

                break;
            case 'number':
                $value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                $this->addRequest($request_key, $type, $value, $rules);
                break;
        }
    }

    private function addRequest($request_key, $type, $value, $rules, $error = ''): void
    {
        $this->request[$request_key] = [
            'type' => $type,
            'value' => $value,
            'rules' => $rules,
            'error' => $error
        ];
    }

    private function getEMEX($rules, $value, $separator = '.', $rulsSearch = 'emex'): bool
    {
        $rules = explode("|", $rules);

        foreach ($rules as $rule) {
            if (substr($rule, 0, strpos($rule, ":")) === $rulsSearch) {
                $emex = explode(",", substr($rule, strpos($rule, ":") + 1));
                $array = explode($separator, $value);
                $value_emex = end($array);
                if (in_array($value_emex, $emex)) {
                    return true;
                } else {
                    return false;
                }

            }
        }

        return true;
    }

    public function initFilter($value, $filter = FILTER_SANITIZE_STRING): string
    {

        //remove javascript or andy script code
        $value = filter_var($value, $filter);
        // remove spaces
        $value = trim($value);
        //remove quoutes
        $value = htmlentities($value, ENT_QUOTES);

        return $value;
    }

    private function moreFilter(): void
    {
        foreach ($this->request as $key => $val) {
            $rules = $this->request[$key]['rules'];
            $search = function ($rules, $search) {
                return strpos($rules, $search) !== false;
            };
            if ($search($rules, 'min:')) {
                $this->Min($key);
            }

            if ($search($rules, 'max:')) {
                $this->Max($key);
            }

            if ($search($rules, 'required')) {
                $this->Required($key);
            }


        }
    }

    function Required($key): bool
    {
        // TODO: Implement Required() method.
        if (empty($this->request[$key]['value'])) {
            $this->request[$key]['error'] = ucwords('this failed is required');
//            $this->print($this->request[$key]);
        }
        return false;
    }

    private function checkLength($key, $msg = '', $type = 'min:'): bool
    {
        $rules = explode("|", $this->request[$key]['rules']);
        foreach ($rules as $rule) {
            if (strpos($rule, $type) !== false) {
                $array = explode(":", $rule);
                $number = end($array);
                if ($type === "min:") {
                    if (strlen($this->request[$key]['value']) < $number) {
                        $this->request[$key]['error'] = ucwords($msg);

                    }
                } else {
                    if (strlen($this->request[$key]['value']) > $number) {
                        $this->request[$key]['error'] = ucwords($msg);

                    }
                }

            }
        }
        return true;
    }

    function Min($key): bool
    {
        // TODO: Implement Min() method.
        return $this->checkLength($key, "the $key is too short");
    }

    function Max($key): bool
    {
        // TODO: Implement Max() method.
        return $this->checkLength($key, "the $key is too long", 'max:');

    }

    public function getError($key): string
    {
        return array_key_exists('name', $this->request) ? $this->request[$key]['error'] : '';
    }

    public function getValue($key): string
    {
        return array_key_exists('name', $this->request) ? $this->request[$key]['value'] : '';
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