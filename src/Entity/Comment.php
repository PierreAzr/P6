<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Advert", inversedBy="children")
     */
    private $advert;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $lvl;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="parent" , orphanRemoval=true )
     */
    private $children;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Comment", inversedBy="children")
    */
    private $parent;


    public function __construct()
    {
        $this->children = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): self
    {
        $this->lvl = $lvl;

        return $this;
    }

    public function getParent() {
          return $this->parent;
      }

      public function getChildren() {
          return $this->children;
      }

     public function removeChildren(Comment $child){
       $this->children->removeElement($child);
     }

      public function addChild(Comment $child) {
         $this->children[] = $child;
         $child->setParent($this);
      }

      public function setParent(Comment $parent) {
         $this->parent = $parent;
      }

}
