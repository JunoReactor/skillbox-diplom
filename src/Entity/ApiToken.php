<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApiTokenRepository", repositoryClass=ApiTokenRepository::class)
 */
class ApiToken
{
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $expiresAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="apiTokens", cascade={"persist"})
     */
    private User $user;

    /**
     * ApiToken constructor.
     * @param $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->token = sha1(uniqid('token'));
        $this->expiresAt = new \DateTime('+1 day');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
    
    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function isExpired()
    {
        return $this->getExpiresAt() <= new \DateTime();
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
