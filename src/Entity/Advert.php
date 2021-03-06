<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 */
class Advert
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    /**
     * @ORM\Column(type="float")
     */
    private $lat;

    /**
     * @ORM\Column(type="float")
     */
    private $lng;

    /**
     * @ORM\Column(type="time", length=255)
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $level;

    /**
     * @ORM\Column(type="date")
     */
    private $publicationdate ;

    /**
     * @ORM\Column(type="datetime")
     */
    private $appointmentdate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="advertscreated")
     */
    private $usercreate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="adverts")
     */
    private $participant;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="advert", orphanRemoval=true)
     */
    private $comments;


    public function __construct()
    {
      $this->publicationdate = new \Datetime();
      $this->participant = new ArrayCollection();
      $this->comments = new ArrayCollection();
    }

    public function getId()
{
    return $this->id;
}

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getPublicationdate (): ?\DateTimeInterface
    {
        return $this->publicationdate ;
    }

    public function setPublicationdate (\DateTimeInterface $publicationdate ): self
    {
        $this->publicationdate  = $publicationdate ;

        return $this;
    }

    public function getAppointmentdate(): ?\DateTimeInterface
    {
        return $this->appointmentdate;
    }

    public function setAppointmentdate(\DateTimeInterface $appointmentdate): self
    {
        $this->appointmentdate = $appointmentdate;

        return $this;
    }

    public function getUsercreate(): ?User
    {
        return $this->usercreate;
    }

    public function setUsercreate(?User $usercreate): self
    {
        $this->usercreate = $usercreate;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getParticipant(): Collection
    {
        return $this->participant;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participant->contains($participant)) {
            $this->participant[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        if ($this->participant->contains($participant)) {
            $this->participant->removeElement($participant);
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAdvert($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAdvert() === $this) {
                $comment->setAdvert(null);
            }
        }

        return $this;
    }

}
