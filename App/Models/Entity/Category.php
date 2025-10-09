<?php

namespace App\Models\Entity;

/**
 * Represents a category in the blog system.
 * 
 * This class is used to define and manage categories for blog posts.
 */


class Category
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $slug = null;
    private ?int $post_id = null;
    private Post $post;
    public const COLUMNS = ['name', 'slug'];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getPostId(): ?int
    {
        return $this->post_id;
    }

    public function setPost(Post $post)
    {
        $this->post = $post;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
    public function setSlug(string $slug)
    {
        $this->slug = $slug;
    }
}
