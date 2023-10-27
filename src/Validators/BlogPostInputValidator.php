<?php
declare(strict_types=1);


namespace CSCart\Bagisto\Blog\Validators;


use CSCart\Bagisto\Blog\Enums\CategoryStatus;
use CSCart\Bagisto\Blog\Models\Category;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

final class BlogPostInputValidator extends Validator
{
    public function rules(): array
    {
        $isUpdate = $this->args->has('id');

        return [
            'title'              => [$isUpdate ? 'filled' : 'required', 'string'],
            'content'            => [$isUpdate ? 'filled' : 'required', 'string'],
            'publishedAt'        => ['nullable', 'date'],
            'categories'         => [$isUpdate ? null : 'required'],
            'categories.connect' => [Rule::exists(Category::class, 'id')->where('status', CategoryStatus::ACTIVE->value)],
            'categories.sync'    => [Rule::exists(Category::class, 'id')->where('status', CategoryStatus::ACTIVE->value)],
        ];
    }
}
