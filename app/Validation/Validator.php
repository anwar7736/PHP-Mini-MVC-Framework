<?php
namespace App\Validation;

class Validator
{
    protected array $data = [];
    protected array $rules = [];
    protected array $errors = [];
    protected array $validated = [];
    protected array $customMessages = [];
    protected $db = "";

    public function __construct($data, array $rules, array $messages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $messages;   // store custom messages
        
        $this->db = getDBConnection();
        $this->run();
    }

    public static function make($data, array $rules, array $messages = []): static
    {
        return new static($data, $rules, $messages);
    }

    protected function run()
    {
        foreach ($this->rules as $field => $ruleList) {
            $value = $this->data[$field] ?? null;

            // Normalize rule list
            $rules = is_array($ruleList) ? $ruleList : explode('|', $ruleList);

            // Handle empty rule list like [''] → treat as no rules
            if (count($rules) === 1 && trim($rules[0]) === '') {
                $rules = [];
            }

            // Check if field is nullable
            $isNullable = in_array('nullable', $rules, true);

            foreach ($rules as $rule) {
                // Skip all rules except required if nullable AND value is empty
                if ($isNullable && ($value === null || $value === '' || (is_array($value) && empty($value))) 
                    && !in_array($rule, ['required', 'nullable'])) {
                    continue;
                }

                $this->applyRule($field, $value, $rule);
            }
        }

        // Store validated fields if no errors
        if (empty($this->errors)) {
            foreach ($this->rules as $field => $_) {
                $this->validated[$field] = $this->data[$field] ?? null;
            }
        }
    }


    protected function message(string $field, string $rule, string $default): string
    {
        // Field.rule => custom message
        $key = $field . '.' . $rule;

        return $this->customMessages[$key] ?? $default;
    }

    protected function applyRule(string $field, $value, string $rule)
    {
        // Resolve rule name (min:6 → min)
        $ruleName = str_contains($rule, ':') ? explode(':', $rule)[0] : $rule;

        // === REQUIRED ===
        if ($rule === 'required' && empty($value) && $value !== '0') {
            return $this->addError($field, $this->message($field, 'required', "The $field field is required."));
        }

        // === STRING ===
        if ($rule === 'string' && !is_string($value)) {
            return $this->addError($field, $this->message($field, 'string', "The $field field must be a string."));
        }

        // === EMAIL ===
        if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->addError($field, $this->message($field, 'email', "The $field field must be a valid email."));
        }

        // === NUMERIC ===
        if ($rule === 'numeric' && !is_numeric($value)) {
            return $this->addError($field, $this->message($field, 'numeric', "The $field field must be numeric."));
        }

        // === INTEGER ===
        if ($rule === 'integer' && filter_var($value, FILTER_VALIDATE_INT) === false) {
            return $this->addError($field, $this->message($field, 'integer', "The $field field must be an integer."));
        }

        // === BOOLEAN ===
        if ($rule === 'boolean' && !in_array($value, [0, 1, true, false, '0', '1'], true)) {
            return $this->addError($field, $this->message($field, 'boolean', "The $field field must be true or false."));
        }

        // === MIN ===
        if (str_starts_with($rule, 'min:')) {
            $min = (int) explode(':', $rule)[1];
            if (strlen((string)$value) < $min) {
                return $this->addError($field, $this->message($field, 'min', "The $field field must be at least $min characters."));
            }
        }

        // === MAX ===
        if (str_starts_with($rule, 'max:')) {
            $max = (int) explode(':', $rule)[1];
            if (strlen((string)$value) > $max) {
                return $this->addError($field, $this->message($field, 'max', "The $field field must not exceed $max characters."));
            }
        }

        // === BETWEEN ===
        if (str_starts_with($rule, 'between:')) {
            [$min, $max] = array_map('intval', explode(',', explode(':', $rule)[1]));
            $len = strlen((string)$value);
            if ($len < $min || $len > $max) {
                return $this->addError($field, $this->message($field, 'between', "The $field field must be between $min and $max characters."));
            }
        }

        // === IN ===
        if (str_starts_with($rule, 'in:')) {
            $allowed = explode(',', explode(':', $rule)[1]);
            if (!in_array($value, $allowed)) {
                return $this->addError($field, $this->message($field, 'in', "The $field field must be one of: " . implode(', ', $allowed)));
            }
        }

        // === URL ===
        if ($rule === 'url' && !filter_var($value, FILTER_VALIDATE_URL)) {
            return $this->addError($field, $this->message($field, 'url', "The $field field must be a valid URL."));
        }

        // === DATE ===
        if ($rule === 'date' && strtotime($value) === false) {
            return $this->addError($field, $this->message($field, 'date', "The $field field must be a valid date."));
        }

        // === IMAGE ===
        if ($rule === 'image' && is_array($value)) {
            if (!isset($value['type']) || !str_starts_with($value['type'], 'image/')) {
                return $this->addError($field, $this->message($field, 'image', "The $field field must be an image file."));
            }
        }

        // === UNIQUE ===
        if (str_starts_with($rule, 'unique:')) 
        {
            $segments = explode(':', $rule)[1];
            $parts = explode(',', $segments);

            $table = $parts[0] ?? null;
            $column = $parts[1] ?? $field;
            $ignore = $parts[2] ?? null;
            $idColumn = $parts[3] ?? 'id';

            $query = "SELECT COUNT(*) AS total FROM `$table` WHERE `$column` = ?";
            $bindings = [$value];

            if (!empty($ignore)) {
                $query .= " AND `$idColumn` != ?";
                $bindings[] = $ignore;
            }

            $row = $this->db->query($query, $bindings)->find();
            $exists = isset($row->total) && (int)$row->total > 0;

            if ($exists) {
                return $this->addError($field, $this->message($field, 'unique', "The $field field has already been taken."));
            }
        }

        // === REGEX ===
        if (str_starts_with($rule, 'regex:')) 
        {
            $pattern = explode('regex:', $rule)[1];

            if (@preg_match($pattern, '') === false) {
                return $this->addError($field, $this->message($field, 'regex', "Invalid regex pattern for $field."));
            }

            if (!preg_match($pattern, $value)) {
                return $this->addError($field, $this->message($field, 'regex', "The $field field format is invalid."));
            }
        }

        // === MIMES ===
        if (str_starts_with($rule, 'mimes:')) 
        {
            if (!is_array($value) || empty($value['tmp_name'])) {
                return $this->addError($field, $this->message($field, 'mimes', "The $field field must be a valid file."));
            }

            $allowed = explode(',', explode(':', $rule)[1]);

            $extension = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowed)) {
                return $this->addError($field, $this->message($field, 'mimes', "The $field field must be a file of type: " . implode(', ', $allowed)));
            }
        }

        // === CONFIRMED ===
        if ($rule === 'confirmed') {
            $confirmationField = $field . '_confirmation';
            $confirmationValue = $this->data[$confirmationField] ?? null;

            if ($value !== $confirmationValue) {
                return $this->addError($field, $this->message($field, 'confirmed', "The $field field confirmation does not match."));
            }
        }
    }

    protected function addError(string $field, string $message)
    {
        if(!isset($this->errors[$field])){
            $this->errors[$field][] = $message;
        }
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated()
    {
        return $this->validated;
    }

    public function only(array|string $keys): array
    {
        return array_intersect_key($this->validated, array_flip((array) $keys));
    }

    public function except(array|string $keys): array
    {
        return array_diff_key($this->validated, array_flip((array) $keys));
    }

    public function all(): array
    {
        return $this->validated;
    }
}
