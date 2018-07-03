<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="email", message="Email déjà pris")
 * @UniqueEntity(fields="username", message="Username déjà pris")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     */
    private $username;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $roles;

    /**
     * @ORM\Column(type="boolean")
     */
    private $agree;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $plainpassword;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Advert", mappedBy="usercreate")
     */
    private $advertscreated;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Advert", mappedBy="participant")
     */
    private $adverts;

    public function __construct() {
    $this->roles = array('ROLE_USER');
    $this->advertscreated = new ArrayCollection();
    $this->adverts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
    public function setUsername(string $username)
    {
        $this->username = $username;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
    /**
     * Retourne les rôles de l'user
     */
    public function getRoles(): array
    {
      $roles = $this->roles;
        //$roles = json_decode($this->roles, TRUE);
        // Afin d'être sûr qu'un user a toujours au moins 1 rôle
        // if (empty($roles)) {
        //     $roles[] = 'ROLE_USER';
        // }
        return array_unique($roles);
    }
    public function setRoles(array $roles)
    {
        $this->roles = $roles;
        //$this->roles = json_encode($roles);
    }
    /**
     * Retour le salt qui a servi à coder le mot de passe
     *
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        // See "Do you need to use a Salt?" at https://symfony.com/doc/current/cookbook/security/entity_provider.html
        // we're using bcrypt in security.yml to encode the password, so
        // the salt value is built-in and you don't have to generate one
        return null;
    }
    /**
     * Removes sensitive data from the user.
     *
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
        // Nous n'avons pas besoin de cette methode car nous n'utilions pas de plainPassword
        // Mais elle est obligatoire car comprise dans l'interface UserInterface
        // $this->plainPassword = null;
    }
    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->password, $this->roles]);
    }
    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [$this->id, $this->username, $this->password, $this->roles] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getAgree(): ?bool
    {
        return $this->agree;
    }

    public function setAgree(bool $agree): self
    {
        $this->agree = $agree;

        return $this;
    }

    public function getPlainpassword(): ?string
    {
        return $this->plainpassword;
    }

    public function setPlainpassword(?string $plainpassword): self
    {
        $this->plainpassword = $plainpassword;

        return $this;
    }

/**
 * @ORM\PrePersist
 * @ORM\PreUpdate
 */
public function setPlainpasswordValue()
{

    $this->plainpassword = null;
}

/**
 * @return Collection|Advert[]
 */
public function getAdvertscreated(): Collection
{
    return $this->advertscreated;
}

public function addAdvertscreated(Advert $advertscreated): self
{
    if (!$this->advertscreated->contains($advertscreated)) {
        $this->advertscreated[] = $advertscreated;
        $advertscreated->setUsercreate($this);
    }

    return $this;
}

public function removeAdvertscreated(Advert $advertscreated): self
{
    if ($this->advertscreated->contains($advertscreated)) {
        $this->advertscreated->removeElement($advertscreated);
        // set the owning side to null (unless already changed)
        if ($advertscreated->getUsercreate() === $this) {
            $advertscreated->setUsercreate(null);
        }
    }

    return $this;
}

/**
 * @return Collection|Advert[]
 */
public function getAdverts(): Collection
{
    return $this->adverts;
}

public function addAdvert(Advert $advert): self
{
    if (!$this->adverts->contains($advert)) {
        $this->adverts[] = $advert;
        $advert->addParticipant($this);
    }

    return $this;
}

public function removeAdvert(Advert $advert): self
{
    if ($this->adverts->contains($advert)) {
        $this->adverts->removeElement($advert);
        $advert->removeParticipant($this);
    }

    return $this;
}

}
