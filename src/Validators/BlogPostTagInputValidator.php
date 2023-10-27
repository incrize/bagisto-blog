<?php
declare(strict_types=1);


namespace CSCart\Bagisto\Blog\Validators;

use Nuwave\Lighthouse\Validation\Validator;

class BlogPostTagInputValidator extends Validator
{
    public function rules(): array
    {
        $isUpdate = $this->args->has('id');

        return [
            'title' => [$isUpdate ? 'filled' : 'required', 'string'],
        ];
    }
}
