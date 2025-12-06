<?php
namespace App\Http;

use App\Validation\Validator;

class Request 
{
    protected $inputs = [];
    protected $files = [];

    public function __construct()
    {
        // 1. Load GET and POST
        $this->inputs = array_merge($_GET, $_POST);

        // 2. Load JSON body (API request)
        $json = file_get_contents('php://input');
        if (!empty($json)) {
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $this->inputs = array_merge($this->inputs, $decoded);
            }
        }

        // 3. Load FILES separately
        $this->files = $_FILES ?? [];
    }

    /** Get a single input */
    public function input(string $name, $default = null)
    {
        return $this->inputs[$name] ?? $default;
    }

    /** Only selected keys */
    public function only(array|string $keys): array
    {
        $keys = (array)$keys;
        return array_intersect_key($this->inputs, array_flip($keys));
    }

    /** Except keys */
    public function except(array|string $keys): array
    {
        $keys = (array)$keys;
        return array_diff_key($this->inputs, array_flip($keys));
    }

    /** Get file object */
    public function file(string $name)
    {
        return $this->files[$name] ?? null;
    }

    /** Check file */
    public function hasFile(string $name): bool
    {
        return isset($this->files[$name]) && $this->files[$name]['error'] === UPLOAD_ERR_OK;
    }

    /** Add/merge new data */
    public function merge(array $data)
    {
        $this->inputs = array_merge($this->inputs, $data);
    }
    
    public function user(): array 
    {
        return user();
    }

public function validate(array $rules)
    {
        $validator = new Validator();

        $result = $validator->validate($this->all(), $rules);

        if ($this->hasErrors($result)) {
            // Stop request and show error
            die(json_encode([
                'success' => false,
                'errors' => $result
            ]));
        }

        return $result;
    }

    public function all()
    {
        return $_REQUEST;
    }

    private function hasErrors($result): bool
    {
        return isset($result['errors']) || !empty($result[ array_key_first($result) ]);
    }
    
}