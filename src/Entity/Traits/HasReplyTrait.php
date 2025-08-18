<?php

namespace App\Entity\Traits;

use App\Entity\Local;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

trait HasReplyTrait
{
    #[ORM\Column(nullable: true)]
    private ?bool $hasReply = null;

    public function hasReply(): ?bool
    {
        return $this->hasReply;
    }

    public function setHasReply(bool $hasReply): static
    {
        $this->hasReply = $hasReply;

        return $this;
    }

    private function replySons(EntityManagerInterface $entityManager, Collection $items, object $parent = null)
    {
        foreach ($items as $item) {
            $item->reply($entityManager, $parent);
        }
    }
}