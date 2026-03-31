<?php

namespace App\Helpers;
use DateTime;
class Validator
{
    private $data;
    private $currentField;
    private $currentAlias;
    public $errorMessages = [];
    private $next = true;
    private $responseMessages = [
        "required" => "{field} est obligatoire.",
        "alpha" => "{field} doit contenir uniquement des lettres.",
        "alpha_num" => "{field} doit contenir uniquement des lettres et des chiffres.",
        "numeric" => "{field} doit contenir uniquement des chiffres.",
        "email" => "{field} n'est pas valide.",
        "max_len" => "{field} est trop long.",
        "min_len" => "{field} est trop court.",
        "max_val" => "{field} est trop élevé.",
        "min_val" => "{field} est trop faible.",
        "enum" => "{field} n'est pas valide.",
        "equals" => "{field} ne correspond pas.",
        "must_contain" => "{field} doit contenir {chars}.",
        "match" => "{field} n'est pas valide.",
        "date" => "{field} n'est pas une date valide.",
        "date_after" => "La date de {field} n'est pas valide.",
        "date_before" => "La date de {field} n'est pas valide.",
        "file_required" => "{field} est obligatoire.",
        "file_extension" => "L'extension du fichier {field} n'est pas autorisée.",
        "file_size" => "Le fichier {field} est trop volumineux.",
        "is_image" => "{field} doit être une image valide.",
    ];

    public function __construct($data)
    {
        $this->data = $data;
    }

    private function addErrorMessage($type, $others = [])
    {
        $fieldName = $this->currentAlias ? ucfirst($this->currentAlias) : ucfirst($this->currentField);
        $msg = str_replace('{field}', $fieldName, $this->responseMessages[$type]);
        foreach ($others as $key => $val) {
            $msg = str_replace('{' . $key . '}', $val, $msg);
        }
        $this->errorMessages[$this->currentField] = $msg;
    }

    private function exists()
    {
        $val = $this->data[$this->currentField] ?? null;

        if ($val === null || $val === '') {
            return false;
        }

        if (is_array($val) && isset($val['error']) && $val['error'] === UPLOAD_ERR_NO_FILE) {
            return false;
        }

        return true;
    }

    public function required()
    {
        if (!$this->exists()) {
            $this->addErrorMessage('required');
            $this->next = false;
        }

        return $this;
    }

    public function alpha($ignores)
    {
        if ($this->next && $this->exists() && !ctype_alpha(str_replace($ignores, '', $this->data[$this->currentField]))) {
            $this->addErrorMessage('alpha');
            $this->next = false;
        }
        return $this;
    }
    public function numeric()
    {
        if ($this->next && $this->exists() && !is_numeric($this->data[$this->currentField])) {
            $this->addErrorMessage('numeric');
            $this->next = false;
        }
        return $this;
    }

    public function email()
    {
        if ($this->next && $this->exists() && !filter_var($this->data[$this->currentField], FILTER_VALIDATE_EMAIL)) {
            $this->addErrorMessage('email');
            $this->next = false;
        }
        return $this;
    }

    public function max_len($size)
    {
        if ($this->next && $this->exists() && strlen($this->data[$this->currentField]) > $size) {
            $this->addErrorMessage('max_len');
            $this->next = false;
        }
        return $this;
    }

    public function min_len($size)
    {
        if ($this->next && $this->exists() && strlen($this->data[$this->currentField]) < $size) {
            $this->addErrorMessage('min_len');
            $this->next = false;
        }
        return $this;
    }

    public function max_val($val)
    {
        if ($this->next && $this->exists() && $this->data[$this->currentField] > $val) {
            $this->addErrorMessage('max_val');
            $this->next = false;
        }
        return $this;
    }

    public function min_val($val)
    {
        if ($this->next && $this->exists() && $this->data[$this->currentField] < $val) {
            $this->addErrorMessage('min_val');
            $this->next = false;
        }
        return $this;
    }

    public function enum($list)
    {
        if ($this->next && $this->exists() && !in_array($this->data[$this->currentField], $list)) {
            $this->addErrorMessage('enum');
            $this->next = false;
        }
        return $this;
    }

    public function equals($value)
    {
        if ($this->next && $this->exists() && $this->data[$this->currentField] !== $value) {
            $this->addErrorMessage('equals');
            $this->next = false;
        }
        return $this;
    }

    public function must_contain($chars)
    {
        if ($this->next && $this->exists() && !preg_match("/[" . $chars . "]/i", $this->data[$this->currentField])) {
            $this->addErrorMessage('must_contain');
            $this->next = false;
        }
        return $this;
    }

    public function field($name, $alias = null)
    {
        $this->currentField = $name;
        $this->currentAlias = $alias;
        $this->next = true;
        return $this;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function isValid()
    {
        return count($this->errorMessages) === 0;
    }

    public function date_after($date)
    {
        if ($this->next && $this->exists() && strtotime($date) >= strtotime($this->data[$this->currentField])) {
            $this->addErrorMessage('date_after');
            $this->next = false;
        }
        return $this;
    }

    public function date_before($date)
    {
        if ($this->next && $this->exists() && strtotime($date) <= strtotime($this->data[$this->currentField])) {
            $this->addErrorMessage('date_before');
            $this->next = false;
        }
        return $this;
    }
    public function date($format = 'Y-m-d')
    {
        if ($this->next && $this->exists()) {
            $dateTime = DateTime::createFromFormat($format, $this->data[$this->currentField]);
            if (!($dateTime && $dateTime->format($format) == $this->data[$this->currentField])) {
                $this->addErrorMessage('date');
                $this->next = false;
            }
        }
        return $this;
    }

    public function is_image()
    {
        if ($this->next && $this->exists() && !@getimagesize($this->data[$this->currentField]['tmp_name'])) {
            $this->addErrorMessage('is_image');
            $this->next = false;
            return $this;
        }
        return $this;
    }

    public function file_required()
    {
        if (!$this->exists()) {
            $this->addErrorMessage('file_required');
            $this->next = false;
            return $this;
        }
        return $this;
    }

    public function file_extension($extensions)
    {
        if ($this->next && $this->exists()) {
            $ext = strtolower(pathinfo($this->data[$this->currentField]['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $extensions)) {
                $this->addErrorMessage('file_extension');
                $this->next = false;
            }
        }
        return $this;
    }

    public function file_size($size)
    {
        if ($this->next && $this->exists() && $this->data[$this->currentField]['size'] > $size) {
            $this->addErrorMessage('file_size');
            $this->next = false;
        }
        return $this;
    }
}

