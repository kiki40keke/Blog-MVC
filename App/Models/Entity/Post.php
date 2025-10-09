<?php

namespace App\Models\Entity;

use DateTime;
use App\Helpers\Text;
use App\Helpers\Upload;
use DateTimeImmutable;

class Post
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $slug = null;
    private ?string $content = null;
    private ?string  $created_at = null;
    private array $categories = [];
    private ?string $image = null;
    public const COLUMNS = ['name', 'slug', 'content', 'created_at', 'image'];


    public function getId()
    {
        return $this->id;
    }
    public function getSlug(): ?string
    {
        return $this->slug ?? '';
    }
    public function getName(): ?string
    {
        return $this->name ?? '';
    }
    public function getContent(): ?string
    {
        return $this->content ?? '';
    }

    public function getCreated_at(): ?string
    {
        return $this->created_at;
    }
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function setCreated_at(?string $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }






    public function getFormatedContent(): ?string
    {
        return nl2br(Text::e($this->content));
    }

    public function getFormatDate(string $format = 'd F Y')
    {
        if (empty($this->created_at)) {
            return '';
        }
        try {
            return (new DateTimeImmutable($this->created_at))->format($format);
        } catch (\Throwable) {
            return '';
        }
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories(array $categories): self
    {
        $this->categories = $categories;
        return $this;
    }

    public function getCategories_id()
    {
        $ids = [];
        foreach ($this->categories as $category) {
            $ids[] = $category->getId();
        }
        //dd($ids);

        return $ids;
    }

    public function getExcept(): ?string
    {
        if ($this->content === null) {
            return '';
        } else {
            return nl2br(htmlentities(Text::excerpt($this->content, 100)));
        }
    }
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
        $category->setPost($this);
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(array $image): self
    {
        if (!empty($image['tmp_name'])) {
            $this->image = Upload::renameFile($image, $this->getSlug(), "ImagePost");
        }
        return $this;
    }
}
