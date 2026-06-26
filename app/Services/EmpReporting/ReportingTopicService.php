<?php

namespace App\Services\EmpReporting;

use App\Models\Utilities\EmpReporting\ReportingTopic;
use Illuminate\Support\Facades\Validator;

class ReportingTopicService
{
    public function create(array $data)
    {
        return ReportingTopic::create($data);
    }

    public function validateAttributes(string $topicCode, array $attributes): array
    {
        $topic = ReportingTopic::where('code', $topicCode)->firstOrFail();
        $schema = $topic->required_attributes ?? [];

        if (empty($schema)) return $attributes;

        $rules = $this->buildValidationRules($schema);
        $validator = Validator::make($attributes, $rules);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $attributes;
    }

    private function buildValidationRules(array $schema): array
    {
        $rules = [];
        foreach ($schema as $field => $config) {
            $rule = [];
            if (!empty($config['required'])) $rule[] = 'required';
            if (isset($config['type'])) {
                $rule[] = match($config['type']) {
                    'integer' => 'integer',
                    'decimal' => 'numeric',
                    default   => 'string'
                };
            }
            if (isset($config['min'])) $rule[] = 'min:' . $config['min'];
            if (isset($config['max'])) $rule[] = 'max:' . $config['max'];

            $rules[$field] = implode('|', $rule);
        }
        return $rules;
    }
}