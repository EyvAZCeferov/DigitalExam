<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RemoveNullValues
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     * @param Closure $next
     * @return string|null
     */
    public function handle(Request $request, Closure $next)
    {
        $data = $request->toArray();

        $this->removeNullValues($data);
        $this->removeNullValues($data); // second is for empty arrays

        $request->replace($data);

        return $next($request);
    }

    public function removeNullValues(array &$data): void
    {
        foreach ($data as $key=>&$value) {
            if (!is_array($value)) {
                if ($value === null) {
                    unset($data[$key]);
                }
            } elseif (count($value) === 0) {
                unset($data[$key]);
            } else {
                $this->removeNullValues($value);
            }
        }
    }
}
