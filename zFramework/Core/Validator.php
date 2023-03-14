<?php

namespace zFramework\Core;

use zFramework\Core\Facades\Alerts;
use zFramework\Core\Facades\DB;
use zFramework\Core\Facades\Lang;
use zFramework\Core\Facades\Response;
use zFramework\Core\Helpers\Http;

class Validator
{
    /**
     * Validate a array
     * @param array $data
     * @param array $validate
     * @param array $attributeNames
     * @return string|array
     */
    public static function validate(array $data = null, array $validate = [], array $attributeNames = [], string $customRedirect = null)
    {
        if (!$data) $data = $_REQUEST;

        $errors = [];
        $statics = [];

        foreach ($validate as $dataKey => $validateArray) {
            $dataValue = @$data[$dataKey];

            $length = -1;
            if (empty($dataValue) && $dataValue != 0) {
                $type  = 'null';
            } elseif (is_numeric($dataValue) || $dataValue == "0") {
                $type = 'integer';
                $length = $dataValue;
            } elseif (is_string($dataValue)) {
                $type = 'string';
                $length = strlen($dataValue);
            } elseif (is_array($dataValue)) {
                $type = 'array';
                $length = count($dataValue);
            } elseif (is_object($dataValue)) {
                $type = 'object';
                $length = count((array) $dataValue);
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

                            if (isset($parameter[1])) $parameters[$parameter[0]] = $parameter[1];
                            else $parameters[] = $parameter[0];
                        }
                    }
                }

                $ok = false;
                switch ($key) {
                    case 'type':
                        if ($type == $val) $ok = true;
                        else $errorData = ['now-type' => $type, 'must-type' => $val];
                        break;

                    case 'email':
                        if (!$length && in_array('nullable', $data)) $ok = true;
                        elseif (filter_var($dataValue, FILTER_VALIDATE_EMAIL)) $ok = true;
                        break;

                    case 'required':
                        if ($length > 0 || strlen((string) $dataValue) > 0) $ok = true;
                        break;

                    case 'nullable':
                        $ok = true;
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

                    case 'exists':
                        $column = $parameters['key'] ?? $dataKey;
                        $exists = (new DB(@$parameters['db']))->table($val)->where($column, '=', $dataValue);

                        if ($ex = @$parameters['ex']) $exists->where('id', '!=', $ex);

                        $exists = $exists->first() ?? [];
                        if (count($exists)) {
                            $dataValue = $exists;
                            $ok = true;
                        }
                        break;

                    case 'unique':
                        $column = $parameters['key'] ?? $dataKey;
                        if (!(new DB(@$parameters['db']))->table($val)->where($column, '=', $dataValue)->count()) $ok = true;
                        break;

                    default:
                        $ok = true;
                }

                if ($ok) {
                    $statics[$dataKey] = $dataValue;
                    // $statics[$dataKey]['value'] = $dataValue;
                    // $statics[$dataKey]['length'] = $length;
                    // $statics[$dataKey]['type'] = $type;
                } else {
                    $errors[$dataKey][] = (Lang::get("validator.attributes.$dataKey") ?? ($attributeNames[$dataKey] ?? $dataKey)) . " " . Lang::get("validator.errors.$key", $errorData ?? []);
                    unset($data[$dataKey]);
                }

                // $statics[$dataKey]['validate'][$ok ? 'accept' : 'decline'][] = $key;
            }
        }

        if (count($errors)) {
            if (Http::isAjax()) abort(400, Response::json($errors));
            foreach ($errors as $key => $error_list) foreach ($error_list as $error) Alerts::danger($error);

            if (!$customRedirect) back();
            else redirect($customRedirect);
        }

        return $statics;
    }
}
