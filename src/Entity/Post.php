<?php

namespace App\Entity;

use App\Dto\Post as PostDto;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Keyword::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $keyword;

    /**
     * @ORM\Column(type="integer")
     */
    private $telegram_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $telegram_chat_id;

    /**
     * @ORM\ManyToOne(targetEntity=PostText::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $text;

    /**
     * @ORM\Column(type="array")
     */
    private $usernames = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $chat_name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getKeyword(): ?Keyword
    {
        return $this->keyword;
    }

    public function setKeyword(?Keyword $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getTelegramId(): ?int
    {
        return $this->telegram_id;
    }

    public function setTelegramId(int $telegram_id): self
    {
        $this->telegram_id = $telegram_id;

        return $this;
    }

    public static function create(PostDto $postDto): Post
    {
        $post = new Post();
        $post->setUser($postDto->getUser());
        $post->setKeyword($postDto->getKeyword());
        $post->setTelegramId($postDto->getTelegramId());
        $post->setDate($postDto->getDate());
        $post->setTelegramChatId($postDto->getTelegramChatId());
        $post->setText($postDto->getPostText());
        $post->setUsernames($postDto->getUsernames());
        $post->setChatName($postDto->getChatName());

        return $post;
    }

    public function getTelegramChatId(): ?int
    {
        return $this->telegram_chat_id;
    }

    public function setTelegramChatId(int $telegram_chat_id): self
    {
        $this->telegram_chat_id = $telegram_chat_id;

        return $this;
    }

    public function getText(): ?PostText
    {
        return $this->text;
    }

    public function setText(?PostText $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getUsernames(): ?array
    {
        return $this->usernames;
    }

    public function setUsernames(array $usernames): self
    {
        $this->usernames = $usernames;

        return $this;
    }

    public function getChatName(): ?string
    {
        return $this->chat_name;
    }

    public function setChatName(?string $chat_name): self
    {
        $this->chat_name = $chat_name;

        return $this;
    }
}
