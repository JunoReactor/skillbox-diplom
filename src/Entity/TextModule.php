<?php

namespace App\Entity;

use App\Repository\TextModuleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TextModuleRepository::class)
 */
class TextModule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="text")
     */
    private string $Content;

    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of name
     *
     * @return  string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of Content
     *
     * @return  string
     */
    public function getContent(): ?string
    {
        return $this->Content;
    }

    /**
     * Set the value of Content
     *
     * @param  string  $Content
     *
     * @return  self
     */
    public function setContent(string $Content): self
    {
        $this->Content = $Content;

        return $this;
    }
}
