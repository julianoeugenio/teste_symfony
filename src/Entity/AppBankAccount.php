<?php

namespace App\Entity;

use App\Repository\AppBankAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppBankAccountRepository::class)]
class AppBankAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 70)]
    private $account_name;

    #[ORM\Column(type: 'string', length: 5)]
    private $agency;

    #[ORM\Column(type: 'string', length: 1)]
    private $agency_digit;

    #[ORM\Column(type: 'string', length: 13)]
    private $account_number;

    #[ORM\Column(type: 'string', length: 1)]
    private $account_digit;

    #[ORM\Column(type: 'string', length: 2)]
    private $account_type;

    #[ORM\ManyToOne(targetEntity: AppUser::class, inversedBy: 'app_user_id')]
    #[ORM\JoinColumn(nullable: false)]
    private $app_user_id;

    #[ORM\ManyToOne(targetEntity: AppBank::class, inversedBy: 'app_bank_id')]
    #[ORM\JoinColumn(nullable: false)]
    private $app_bank_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountName(): ?string
    {
        return $this->account_name;
    }

    public function setAccountName(string $account_name): self
    {
        $this->account_name = $account_name;

        return $this;
    }

    public function getAgency(): ?string
    {
        return $this->agency;
    }

    public function setAgency(string $agency): self
    {
        $this->agency = $agency;

        return $this;
    }

    public function getAgencyDigit(): ?string
    {
        return $this->agency_digit;
    }

    public function setAgencyDigit(string $agency_digit): self
    {
        $this->agency_digit = $agency_digit;

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->account_number;
    }

    public function setAccountNumber(string $account_number): self
    {
        $this->account_number = $account_number;

        return $this;
    }

    public function getAccountDigit(): ?string
    {
        return $this->account_digit;
    }

    public function setAccountDigit(string $account_digit): self
    {
        $this->account_digit = $account_digit;

        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->account_type;
    }

    public function setAccountType(string $account_type): self
    {
        $this->account_type = $account_type;

        return $this;
    }

    public function getAppUserId(): ?AppUser
    {
        return $this->app_user_id;
    }

    public function setAppUserId(?AppUser $app_user_id): self
    {
        $this->app_user_id = $app_user_id;

        return $this;
    }

    public function getAppBankId(): ?AppBank
    {
        return $this->app_bank_id;
    }

    public function setAppBankId(?AppBank $app_bank_id): self
    {
        $this->app_bank_id = $app_bank_id;

        return $this;
    }
}
