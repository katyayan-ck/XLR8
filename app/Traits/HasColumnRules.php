<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasColumnRules
{
    /**
     * Define transformations for specific columns (e.g., uppercase, lowercase, custom functions).
     *
     * @var array
     */
    protected $transformations = [];

    /**
     * Define validation rules for specific columns (e.g., regex patterns).
     *
     * @var array
     */
    protected $validations = [];

    /**
     * Boot the trait by hooking into the model's saving event.
     */
    public static function bootHasColumnRules()
    {
        static::saving(function ($model) {
            // Apply transformations to specified columns
            foreach ($model->transformations as $column => $transformation) {
                if (isset($model->$column)) {
                    if (is_callable($transformation)) {
                        // Apply callable transformation (e.g., strtoupper or closure)
                        $model->$column = call_user_func($transformation, $model->$column);
                    } elseif (is_array($transformation) && isset($transformation['pattern'], $transformation['replacement'])) {
                        // Apply regex-based transformation
                        $model->$column = preg_replace(
                            $transformation['pattern'],
                            $transformation['replacement'],
                            $model->$column
                        );
                    }
                }
            }

            // Perform validations on specified columns
            if (!empty($model->validations)) {
                $validator = Validator::make($model->getAttributes(), $model->validations);
                if ($validator->fails()) {
                    // Throw a ValidationException if validation fails
                    throw new ValidationException($validator);
                }
            }
        });
    }
}