<?php

namespace App\Components\Blog;

use App\Entities\Post;
use Spacio\Framework\Components\Component;
use Spacio\Framework\Validation\ValidationException;
use Spacio\Framework\Validation\Validator;

class IndexComponent extends Component
{
    public array $posts = [];

    public ?string $title = null;

    public ?string $slug = null;

    public ?string $body = null;

    public array $errors = [];

    public function view(): string
    {
        return 'blog.index';
    }

    public function mount(array $props = [], array $data = []): void
    {
        parent::mount($props, $data);
        $this->refresh();
    }

    public function refresh(): void
    {
        $this->posts = Post::query()->get();
    }

    public function save(array $data = []): void
    {
        $this->errors = [];
        $this->title = $data['title'] ?? null;
        $this->slug = $data['slug'] ?? null;
        $this->body = $data['body'] ?? null;

        $validator = new Validator;

        try {
            $validated = $validator->validate(
                $data,
                [
                    'title' => 'required|string|min:3|max:160',
                    'slug' => 'required|string|min:3|max:160|unique:posts,slug',
                    'body' => 'nullable|string',
                ],
                [
                    'title.required' => 'Please enter a title.',
                    'slug.required' => 'Please enter a slug.',
                    'slug.unique' => 'Slug already exists.',
                ]
            );
        } catch (ValidationException $exception) {
            $this->errors = $exception->errors();

            return;
        }

        Post::create($validated);
        $this->title = null;
        $this->slug = null;
        $this->body = null;
        $this->refresh();
    }

    public function preview(array $data = []): void
    {
        $this->title = $data['title'] ?? $this->title;
        $this->slug = $data['slug'] ?? $this->slug;
        $this->body = $data['body'] ?? $this->body;
    }
}
