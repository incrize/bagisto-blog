<?php
declare(strict_types=1);


namespace CSCart\Bagisto\Blog\Validators;

use CSCart\Bagisto\Blog\Enums\CategoryStatus;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

class BlogCategoryInputValidator extends Validator
{
    public function rules(): array
    {
        $isUpdate = $this->args->has('id');

        $uniqueSlug = Rule::unique('blog_categories', 'slug');

        if ($isUpdate) {
            $uniqueSlug->ignore($this->arg('id'));
        }

        return [
            'title'  => [$isUpdate ? 'filled' : 'required', 'string'],
            'status' => [$isUpdate ? null : 'required', Rule::enum(CategoryStatus::class)],
            'slug'   => [$isUpdate ? 'filled' : 'required', 'string', $uniqueSlug]
        ];
    }
}
