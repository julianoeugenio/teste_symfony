<?php

namespace App\Entity;

use App\Repository\AppUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppUserRepository::class)]
class AppUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'string', length: 11)]
    private $cpf;

    #[ORM\Column(type: 'string', length: 60)]
    private $email;

    #[ORM\OneToMany(mappedBy: 'app_user_id', targetEntity: AppBankAccount::class, orphanRemoval: true)]
    private $app_user_id;

    public function __construct()
    {
        $this->app_user_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, AppBankAccount>
     */
    public function getAppUserId(): Collection
    {
        return $this->app_user_id;
    }

    public function addAppUserId(AppBankAccount $appUserId): self
    {
        if (!$this->app_user_id->contains($appUserId)) {
            $this->app_user_id[] = $appUserId;
            $appUserId->setAppUserId($this);
        }

        return $this;
    }

    public function removeAppUserId(AppBankAccount $appUserId): self
    {
        if ($this->app_user_id->removeElement($appUserId)) {
            // set the owning side to null (unless already changed)
            if ($appUserId->getAppUserId() === $this) {
                $appUserId->setAppUserId(null);
            }
        }

        return $this;
    }
}
