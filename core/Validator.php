<?php

namespace Core;

class Validator
{
    public static function validate(array $data = [], array $validate = [], array $attributeNames = [])
    {
        $errors = [];
        $statics = [];

        foreach ($validate as $dataKey => $validateArray) {
            $value = @$data[$dataKey];

            $length = -1;
            if (empty($value)) {
                $type  = 'null';
            } elseif (is_numeric($value)) {
                $type = 'integer';
                $length = $value;
            } elseif (is_string($value)) {
                $type = 'string';
                $length = strlen($value);
            } elseif (is_array($value)) {
                $type = 'array';
                $length = count($value);
            }

            foreach ($validateArray as $validate) {
                $e = explode(':', $validate);
                $key = $e[0];

                if (isset($e[1])) {
                    $_ = explode(' ', $e[1]);
                    $val = $_[0];

                    if (!empty($_[1])) {
                        $arr_parameter = @explode(',', $_[1]);
                        $parameters = [];
                        foreach ($arr_parameter as $parameter) {
                            $parameter = explode('=', $parameter);

                            if (isset($parameter[1]))
                                $parameters[$parameter[0]] = $parameter[1];
                            else
                                $parameters[] = $parameter[0];
                        }
                    }
                }

                $ok = false;
                switch ($key) {
                    case 'type':
                        if ($type == $val) $ok = true;
                        break;

                    case 'email':
                        if (filter_var($value, FILTER_VALIDATE_EMAIL)) $ok = true;
                        break;

                    case 'required':
                        if (!empty($value)) $ok = true;
                        break;

                    case 'max':
                        if ($val >= $length) $ok = true;
                        break;

                    case 'min':
                        if ($length >= $val) $ok = true;
                        break;

                    case 'same':
                        if ($value === @$data[$val]) $ok = true;
                        break;
                }

                if ($ok) {
                    $statics[$dataKey]['value'] = $value;
                    $statics[$dataKey]['length'] = $length;
                    $statics[$dataKey]['type'] = $type;
                } else {
                    $errors[$dataKey][] = $key;
                    unset($data[$dataKey]);
                }

                $statics[$dataKey]['validate'][$ok ? 'accept' : 'decline'][] = $key;
            }
        }

        return $statics;
    }
}
