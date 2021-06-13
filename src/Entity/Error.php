<?php
declare(strict_types=1);

namespace Vim\ErrorTracking\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vim\ErrorTracking\Repository\ErrorRepository;

/**
 * @ORM\Entity(repositoryClass=ErrorRepository::class)
 * @ORM\Table(
 *     indexes={
 *          @ORM\Index(columns={"hash"})
 *     })
 */
class Error
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private ?string $hash = null;

    /**
     * @ORM\Column(type="integer", options={"default":"0"}, nullable=true)
     */
    private ?int $count = null;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private ?string $level = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private ?string $env = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $message = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $process = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $date = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $code = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $file = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $line = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $namespace = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $trace = null;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private ?array $server = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getEnv(): ?string
    {
        return $this->env;
    }

    public function setEnv(?string $env): self
    {
        $this->env = $env;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getTrace(): ?string
    {
        return $this->trace;
    }

    public function setTrace(?string $trace): self
    {
        $this->trace = $trace;

        return $this;
    }

    public function getProcess(): ?string
    {
        return $this->process;
    }

    public function setProcess(?string $process): self
    {
        $this->process = $process;

        return $this;
    }

    public function getServer(): ?array
    {
        return $this->server;
    }

    public function setServer(?array $server): self
    {
        $this->server = $server;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(?\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    public function setLine(?int $line): self
    {
        $this->line = $line;

        return $this;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function setNamespace(?string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
