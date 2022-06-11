<?php

namespace Octopy\Impersonate\Support;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Stringable;

final class TextDisplay implements Stringable
{
    /**
     * @param  User $user
     */
    public function __construct(protected User $user)
    {
        //
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->displayTextByFields(true);
    }

    /**
     * @param  bool $single
     * @return string
     */
    public function displayTextByFields(bool $single = false) : string
    {
        $val = [];
        $tmp = $this->user;

        $fields = config('impersonate.display.fields', []);

        if ($single) {
            $fields = [$fields[0]];
        }

        foreach ($fields as $field) {
            if (! str_contains($field, '.')) {
                $val[] = $this->user->{$field};
            } else {
                // when the field is a relation, try to display the related model
                // e.g : 'comments.user.name'
                foreach (explode('.', $field) as $key) {
                    $tmp = $tmp instanceof Collection ? $tmp->get($key) : $tmp->$key;
                }

                $val[] = $tmp;
            }
        }

        return implode(config('impersonate.display.separator'), $val);
    }
}
