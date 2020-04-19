<?php

namespace RkType\PSModuleEnhancer\Services\Reserved;

use Closure;
use Exception;
use Validate;

class ModuleEnhancerValidationService
{

    protected $errors = [];
    protected $custom_validation_rules = [];

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return ModuleEnhancerValidationService
     * @throws Exception
     */
    public function validate($data, $rules, $messages = [])
    {

        $this->errors = [];

        foreach ($rules as $field => $ruleList) {
            if (!is_array($ruleList)) {
                $ruleList = explode('|', $ruleList);
            }
            $value = array_key_exists($field, $data) ? $data[$field] : null;
            foreach ($ruleList as $single_rule) {
                if (!$this->execRule($single_rule, $value)) {
                    $message_key = "{$field}.{$single_rule}";
                    $this->errors[$field][$single_rule] = array_key_exists($message_key, $messages) ? $messages[$message_key] : $message_key;
                }
            }
        }

        return $this;
    }

    /**
     * @param $rule
     * @param $value
     * @return mixed
     * @throws Exception
     */
    protected function execRule($rule, $value)
    {
        if(array_key_exists($rule, $this->custom_validation_rules)) {
            return $this->custom_validation_rules[$rule]($value);

        }elseif($rule === 'required'){
            return $this->execRuleRequired($value);

        }elseif (method_exists(Validate::class, $rule)) {
            return call_user_func_array([Validate::class, $rule], [$value]);

        }

        throw new Exception("Class 'Validate' has no '{$rule}' rule");

    }

    /**
     * @param $value
     * @return bool
     */
    protected function execRuleRequired($value)
    {
        return trim($value) !== '' && !is_null($value);
    }

    public function fails()
    {
        return count($this->errors) > 0;
    }

    public function passes()
    {
        return !$this->fails();
    }

    public function getErrors()
    {
        $errors = [];

        foreach ($this->errors as $field => $field_errors) {
            foreach ($field_errors as $rule => $message) {
                $errors[] = $message;
                break;
            }
        }

        return $errors;
    }

    /**
     * @param array $rules
     */
    public function setCustomValidationRules($rules) {
        foreach ($rules as $rule_name => $closure) {
            $this->setCustomValidationRule($rule_name, $closure);
        }
    }

    /**
     * @param string $rule_name
     * @param Closure $closure
     */
    public function setCustomValidationRule($rule_name, $closure) {
        $this->custom_validation_rules[$rule_name] = $closure;
    }
}
