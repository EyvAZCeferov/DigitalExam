<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class LocalizedObject implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $value = json_decode($value);

        if (is_array($value) && !count($value)) {
            $data = [];

            foreach (config('app.locales') as $locale) {
                $data[$locale] = "";
            }

            return json_decode(json_encode($data));
        }

        foreach (config('app.locales') as $locale) {
            if (!property_exists($value, $locale)) {
                $value->$locale = "";
            }
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (is_array($value)) {
            foreach (config('app.locales') as $locale) {
                if (!array_key_exists($locale, $value)) {
                    $value[$locale] = "";
                }
            }

            return json_encode($value);
        } else {
            foreach (config('app.locales') as $locale) {
                if (!property_exists($value, $locale)) {
                    $value->$locale = "";
                }
            }

            return json_encode($value);
        }
    }
}
