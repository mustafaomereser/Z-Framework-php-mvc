<?php

namespace Core;

use Core\Facedas\Alerts;
use Core\Facedas\DB;
use Core\Facedas\Lang;
use Core\Facedas\Response;
use Core\Helpers\Http;

class Validator
{
    public static function validate(array $data = [], array $validate = [], array $attributeNames = [])
    {
        $errors = [];
        $statics = [];

        foreach ($validate as $dataKey => $validateArray) {
            $dataValue = @$data[$dataKey];

            $length = -1;
            if (empty($dataValue) && $dataValue != 0) {
                $type  = 'null';
            } elseif (is_numeric($dataValue) || $dataValue == 0) {
                $type = 'integer';
                $length = $dataValue;
            } elseif (is_string($dataValue)) {
                $type = 'string';
                $length = strlen($dataValue);
            } elseif (is_array($dataValue)) {
                $type = 'array';
                $length = count($dataValue);
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
                            $parameter = explode('=', trim($parameter));

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
                        else $errorData = ['now-type' => $val, 'must-type' => $type];
                        break;

                    case 'email':
                        if (filter_var($dataValue, FILTER_VALIDATE_EMAIL)) $ok = true;
                        break;

                    case 'required':
                        if ($length > -1) $ok = true;
                        break;

                    case 'max':
                        if ($val >= $length) $ok = true;
                        else $errorData = ['now-val' => $length, 'max-val' => $val];
                        break;

                    case 'min':
                        if ($length >= $val) $ok = true;
                        else $errorData = ['now-val' => $length, 'min-val' => $val];
                        break;

                    case 'same':
                        if ($dataValue === @$data[$val]) $ok = true;
                        else $errorData = ['attribute-name' => (Lang::get("validator.attributes.$val") ?? $val)];
                        break;

                    case 'unique':
                        $db = new DB(@$parameters['db']);
                        if (!$db->table($val)->where(($parameters['cl'] ?? $dataKey), '=', $dataValue)->count()) $ok = true;
                        break;
                }

                if ($ok) {
                    $statics[$dataKey] = $dataValue;
                    // $statics[$dataKey]['value'] = $dataValue;
                    // $statics[$dataKey]['length'] = $length;
                    // $statics[$dataKey]['type'] = $type;
                } else {
                    $errors[$dataKey][] = (Lang::get("validator.attributes.$dataKey") ?? $dataKey) . " " . Lang::get("validator.errors.$key", $errorData ?? []);
                    unset($data[$dataKey]);
                }

                // $statics[$dataKey]['validate'][$ok ? 'accept' : 'decline'][] = $key;
            }
        }

        if (count($errors)) {
            if (Http::isAjax()) die(Response::json($errors));
            foreach ($errors as $key => $error_list) foreach ($error_list as $error) Alerts::danger($error);
            back();
        }

        return $statics;
    }
}
