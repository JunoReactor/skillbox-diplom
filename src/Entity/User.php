<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups("main")
     */
    private string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("main")
     */
    private string $firstName;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isActive = true;

    /**
     * @ORM\OneToMany(targetEntity=ApiToken::class, mappedBy="user")
     */
    private ArrayCollection $apiTokens;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="author")
     */
    private ArrayCollection $articles;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private string $subscription;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private \DateTimeInterface $subscription_date;

    public function __construct()
    {
        $this->apiTokens = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    /**
     * Get the user ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the user email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the user email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the username which is the email address
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * Get the user roles
     *
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Set the user roles
     *
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the hashed password
     *
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * Set the hashed password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get the salt used to hash the password (not needed when using bcrypt)
     *
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * Erase sensitive data on the user object
     *
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the user first name
     *
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Set the user first name
     *
     * @param string|null $firstName
     * @return $this
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Check if the user is active or not
     *
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * Set whether the user is active or not
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get the URL for the user avatar image based on their first name and Robohash API.
     *
     * @param string|null $size The size of the image in pixels. Optional.
     * @return string The URL of the user avatar image.
     */
    public function getAvatarUrl(string $size = null): string
    {
        $url = sprintf('https://robohash.org/%s.jpg?set=set3', mb_strtolower(str_replace(' ', '_', $this->firstName)));

        if ($size)
        {
            $url .= "&size={$size}x{$size}";
        }

        return $url;
    }

    /**
     * Get all of the API tokens associated with this user.
     *
     * @return Collection|ApiToken[]
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    /**
     * Add an API token to this user.
     *
     * @param ApiToken $apiToken The API token to add.
     * @return self
     */
    public function addApiToken(ApiToken $apiToken): self
    {
        if (!$this->apiTokens->contains($apiToken))
        {
            $this->apiTokens[] = $apiToken;
            $apiToken->setUser($this);
        }

        return $this;
    }

    /**
     * Remove an API token from this user.
     *
     * @param ApiToken $apiToken The API token to remove.
     * @return self
     */
    public function removeApiToken(ApiToken $apiToken): self
    {
        if ($this->apiTokens->contains($apiToken)) {
            $this->apiTokens->removeElement($apiToken);
            // set the owning side to null (unless already changed)
            if ($apiToken->getUser() === $this) {
                $apiToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Get all of the articles associated with this user.
     *
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * Add an article to this user's collection of articles.
     *
     * @param Article $article The article to add.
     * @return self
     */
    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setAuthor($this);
        }

        return $this;
    }

    /**
     * Remove an article from this user's collection of articles.
     *
     * @param Article $article The article to remove.
     * @return self
     */
    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getAuthor() === $this) {
                $article->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Get this user's subscription level.
     *
     * @return string|null The subscription level.
     */
    public function getSubscription(): ?string
    {
        return $this->subscription;
    }

    /**
     * Set this user's subscription level.
     *
     * @param string|null The subscription level.
     * @return self
     */
    public function setSubscription(string $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * Get this user's subscription date.
     *
     *@return \DateTimeInterface|null The subscription date.
     */
    public function getSubscriptionDate(): ?\DateTimeInterface
    {
        return $this->subscription_date;
    }

    /**
     * Set this user's subscription date.
     *
     *@param \DateTimeInterface|null The subscription date.
     *@return self
     */
    public function setSubscriptionDate(?\DateTimeInterface $subscription_date): self
    {
        $this->subscription_date = $subscription_date;

        return $this;
    }
}