<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    use TimestampableEntity;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("main")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("main")
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Gedmo\Slug(fields={"title"})
     * @Groups("main")
     */
    private string $slug;

    /**
     * @ORM\Column(type="string", length=100)
     * @Groups("main")
     */
    private string $description;

    /**
     * @ORM\Column(type="text")
     * @Groups("main")
     */
    private string $body;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("main")
     */
    private \DateTimeInterface $publishedAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("main")
     */
    private string $keywords;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("main")
     */
    private int $voteCount;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("main")
     */
    private string $imageFilename;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="article", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private ArrayCollection $comments;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="articles")
     */
    private ArrayCollection $tags;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
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
     * Get the value of title
     *
     * @return  string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string  $title
     *
     * @return  self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the value of slug
     *
     * @return  string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @param  string  $slug
     *
     * @return  self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of description
     *
     * @return  string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string|null  $description
     *
     * @return  self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of body
     *
     * @return  string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @param  string|null  $body
     *
     * @return  self
     */
    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of publishedAt
     *
     * @return  \DateTimeInterface|null
     */
    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    /**
     * Set the value of publishedAt
     *
     * @param  \DateTimeInterface|null  $publishedAt
     *
     * @return  self
     */
    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get the value of voteCount
     *
     * @return  int|null
     */
    public function getVoteCount(): ?int
    {
        return $this->voteCount;
    }

    /**
     * Set the value of voteCount
     *
     * @param  int|null  $voteCount
     *
     * @return  self
     */
    public function setVoteCount(?int $voteCount): self
    {
        $this->voteCount = $voteCount;

        return $this;
    }

    /**
     * Increase vote count by one.
     *
     */
    public function voteUp()
    {
        $this->voteCount++;
        return $this;
    }

    /**
    Decrease vote count by one.
     */

    public function voteDown()
    {
        $this->voteCount--;
        return $this;
    }

    /**
    Get the value of imageFilename.

    @return string|null.
     */

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    /**
    Set the value of imageFilename.

    @param string|null  $imageFilename.

    @return self.
     */

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    /**
    Get the path to the image.

     */

    public function getImagePath()
    {
        return 'images/' . $this->getImageFilename();
    }

    /**
    Get the author avatar path.

     */

    public function getAuthorAvatarPath()
    {
        return sprintf(
            'https://robohash.org/%s.png?set=set3',
            str_replace(' ', '_', $this->getAuthor())
        );
    }

    /**
    Get the value of keywords.

     */

    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
    Set the value of keywords.

    @param mixed  $keywords.

    @return self.
     */

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
    Get comments associated with this article.

    @return Collection|Comment[].
     */

    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * Add a comment to the article
     *
     * @param Comment $comment The comment to add
     *
     * @return self
     */
    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setArticle($this);
        }

        return $this;
    }

    /**
     * Remove a comment from the article
     *
     * @param Comment $comment The comment to remove
     *
     * @return self
     */
    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);

            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of tags
     *
     * @return Collection|Tag[]
     */
    public function getTags(): Collection|array
    {
        return $this->tags;
    }

    /**
     * Add a tag to the article
     *
     * @param Tag $tag The tag to add
     *
     * @return self
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addArticle($this);
        }

        return $this;
    }

    /**
     * Remove a tag from the article
     *
     * @param Tag $tag The tag to remove
     *
     * @return self
     */
    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);

            if ($tag->getArticles()->contains($this)) {
                $tag->removeArticle($this);
            }
        }

        return $this;
    }

    /**
     * Get the author of the article.
     *
     * @return User|null The author of the article.
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Set the author of the article.
     *
     * @param User|null $author The author of the article.
     *
     * @return self
     */
    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the isPublished of the article.
     *
     * @return bool|null
     */
    public function isPublished(){
        return null !== $this->getPublishedAt();
    }
}