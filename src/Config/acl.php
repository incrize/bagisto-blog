<?php

return [
    [
        'key'   => 'blog',
        'name'  => 'blog::post.blog',
        'route' => 'blog.admin.all',
        'sort'  => 10,
    ],
    [
        'key'   => 'blog.blog-post:create',
        'name'  => 'blog::post.create',
        'route' => 'blog.admin.create',
        'sort'  => 1,
    ],
    [
        'key'   => 'blog.blog-post:read',
        'name'  => 'blog::post.read',
        'route' => 'blog.admin.read',
        'sort'  => 2,
    ],
    [
        'key'   => 'blog.blog-post:update',
        'name'  => 'blog::post.update',
        'route' => 'blog.admin.update',
        'sort'  => 3,
    ],
    [
        'key'   => 'blog.blog-post:delete',
        'name'  => 'blog::post.delete',
        'route' => 'blog.admin.delete',
        'sort'  => 4,
    ],
    [
        'key'   => 'blog.blog-post:manage',
        'name'  => 'blog::post.manage',
        'route' => 'blog.admin.manage',
        'sort'  => 5,
    ],
    [
        'key'   => 'blog.blog-category:manage',
        'name'  => 'blog::post.category-manage',
        'route' => 'blog.admin.category-manage',
        'sort'  => 6,
    ],
];
