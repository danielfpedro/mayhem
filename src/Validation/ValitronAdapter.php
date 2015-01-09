<?php

namespace Mayhem\Validation;

/**
 * Class class is used and adapt Mayhem validalidation declare style to Valitron lib.
 */
class ValitronAdapter 
{
	
	public static function AdaptRules($validations, $validator, $type)
	{
		foreach ($validations as $field => $rules) {
			foreach ($rules as $rule) {
				if (is_array($rule)) {
					// Just do something if type is the same than the 'on' key of the rule, or if the 'on' key doesn't exists
					$on = (array_key_exists('on', $rule)) ? $rule['on'] : null;
					if ($on == $type || is_null($on)) {
						$args = []; // Empty array of args
						$args[] = $rule[0]; // First arg, rule name
						unset($rule[0]);
						$args[] = $field; // Second arg, field name
						if (array_key_exists('message', $rule)) {
							$message = $rule['message'];
						}
						unset($rule['message']);
						foreach ($rule as $ruleArgs) {
							$args[] = $ruleArgs; // Third arg and subsequents, rule args
						}
						if (!isset($message)) {
							call_user_func_array([$validator, 'rule'], $args);
						} else {
							call_user_func_array([$validator, 'rule'], $args)->message($message);
						}
					}
				} else {
					$validator->rule($rule, $field);
				}
			}
		}

		return $validator;
	}
}