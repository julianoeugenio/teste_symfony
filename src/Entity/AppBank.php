<?php

namespace App\Entity;

use App\Repository\AppBankRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppBankRepository::class)]
class AppBank
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 10)]
    private $number;

    #[ORM\Column(type: 'string', length: 45)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'app_bank_id', targetEntity: AppBankAccount::class, orphanRemoval: true)]
    private $app_bank_id;

    public function __construct()
    {
        $this->app_bank_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
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

    /**
     * @return Collection<int, AppBankAccount>
     */
    public function getAppBankId(): Collection
    {
        return $this->app_bank_id;
    }

    public function addAppBankId(AppBankAccount $appBankId): self
    {
        if (!$this->app_bank_id->contains($appBankId)) {
            $this->app_bank_id[] = $appBankId;
            $appBankId->setAppBankId($this);
        }

        return $this;
    }

    public function removeAppBankId(AppBankAccount $appBankId): self
    {
        if ($this->app_bank_id->removeElement($appBankId)) {
            // set the owning side to null (unless already changed)
            if ($appBankId->getAppBankId() === $this) {
                $appBankId->setAppBankId(null);
            }
        }

        return $this;
    }
}
