<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $Image = null;

    #[ORM\ManyToMany(targetEntity: VideoGames::class, inversedBy: 'categories')]
    private Collection $videoGame;

    public function __construct()
    {
        $this->videoGame = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(string $Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    /**
     * @return Collection<int, VideoGames>
     */
    public function getVideoGame(): Collection
    {
        return $this->videoGame;
    }

    public function addVideoGame(VideoGames $videoGame): self
    {
        if (!$this->videoGame->contains($videoGame)) {
            $this->videoGame->add($videoGame);
        }

        return $this;
    }

    public function removeVideoGame(VideoGames $videoGame): self
    {
        $this->videoGame->removeElement($videoGame);

        return $this;
    }
}
