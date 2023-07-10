<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ApiTokenRepository::class)
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
     * Get the value of token
     *
     * @return  string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * Get the value of expiresAt
     *
     * @return  \DateTimeInterface|null
     */
    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    /**
     * Get the value of user
     *
     * @return  User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Check if the token is expired.
     */
    public function isExpired()
    {
        return $this->getExpiresAt() <= new \DateTime();
    }

    /**
     * Set the value of user
     *
     * @param  User|null  $user
     *
     * @return  self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
