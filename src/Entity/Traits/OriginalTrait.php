<?php

namespace App\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait OriginalTrait
{
    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $original = null;

    public function isOriginal(): bool
    {
        return is_null($this->getOriginal());
    }

    public function getOriginal(): ?self
    {
        return $this->original;
    }

    public function setOriginal(?self $original): static
    {
        $this->original = $original;

        return $this;
    }

//    public function getOriginalItems(Collection $items): ArrayCollection
//    {
//        return $this->getItemsFilter($items, true);
//    }
//
//    public function getReplyItems(Collection $items): ArrayCollection
//    {
//        return $this->getItemsFilter($items, false);
//    }

    private function getItemsFilter(Collection $items, bool $condition): ArrayCollection
    {
        $replyItems = new ArrayCollection();
        foreach ($items as $item) {
            if ($item->isOriginal() === $condition) {
                $replyItems->add($item);
            }
        }

        return $replyItems;
    }

}