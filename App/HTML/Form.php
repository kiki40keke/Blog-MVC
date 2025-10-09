<?php

namespace App\HTML;

class Form
{
    private object $data;
    private array $errors;


    public function __construct(object $data, array $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }

    private function getField(string $name): string
    {
        return 'field' . $name;
    }

    private function getValue(string $name)
    {

        $value = '';
        if (is_array($this->data)) {
            return $this->data[$name] ?? '';
        }

        $method = 'get' . ucfirst($name);
        if (method_exists($this->data, $method)) {
            $value = $this->data->$method();
        }
        return $value;
    }



    private function getErrors(string $name): array
    {
        $field = $this->getField($name);

        $isvalid = '';
        $feedback = '';
        if (isset($this->errors[$name])) {
            $isvalid = 'is-invalid';
            $feedback = '<div id="' . $field . 'Feedback" class="invalid-feedback">' .
                implode('<br', $this->errors[$name]) .
                '</div>';
        }
        return [$isvalid, $feedback];
    }




    public function input(string $name, string $label, string $type): string
    {
        $field = $this->getField($name);

        $value = $this->getValue($name);
        [$isvalid, $feedback] = $this->getErrors($name);
        return <<<HTML
        <div class="mb-3">
            <label for="{$field}"  class="form-label">$label</label>
            <div class="input-group">
      <input type="{$type}" class="form-control {$isvalid}" name="{$name}" value="{$value}" id="{$field}" aria-describedby="inputGroupPrepend3 {$field}Feedback">
      {$feedback}
    </div>

    </div>
HTML;
    }

    public function textArea(string $name, string $label): string
    {
        $field = $this->getField($name);

        $value = $this->getValue($name);
        [$isvalid, $feedback] = $this->getErrors($name);
        return <<<HTML
         <div class="mb-3">
        <label for="{$field}" class="form-label">{$label}</label>
        <textarea class="form-control {$isvalid}" name="{$name}" id="{$field}" placeholder="le contenu est requis">{$value}</textarea>
        {$feedback}
    </div>
HTML;
    }
    public function button(string $label): string
    {
        return '<button type="submit" class="btn btn-primary">' . $label . '</button>';
    }
    public function selectMultiple(string $name, string $label, array $options = []): string
    {
        // Récupère les valeurs déjà sélectionnées depuis l'objet / data
        $field = $this->getField($name);
        $selectedValues =  [];

        $selectedValues = (array) ($this->getValue($name) ?? []);

        $optionsHtml = [];
        foreach ($options as $k => $v) {
            $key = htmlspecialchars((string)$k, ENT_QUOTES, 'UTF-8');
            $val = htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

            $selected = in_array((string)$k, array_map('strval', $selectedValues), true) ? ' selected' : '';
            $optionsHtml[] = "<option value=\"{$key}\"{$selected}>{$val}</option>";
        }
        $optionHtml = implode('', $optionsHtml);
        [$isvalid, $feedback] = $this->getErrors($name);


        return <<<HTML
<div class="mb-3">
  <label class="form-label" for="{$field}">{$label}</label>
  <select class="form-select {$isvalid}" size="7" id="{$field}" name="{$name}[]" multiple>
    {$optionHtml}
  </select>
  {$feedback}
</div>
HTML;
    }
}
