<?php

namespace App\Entity\Traits;

use App\Entity\Local;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait OriginalTrait
{
    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $original = null;

    public function isOriginal(): bool
    {
        //TODO: ver la forma de poder decir que un local nuevo en la replica no es original
//        if($this instanceof Local){
//            if(is_null($this->getOriginal())){
//                if(is_null($this->getSubSystem()->getOriginal())){
//                    if(is_null($this->getSubSystem()->getFloor()->getOriginal())){
//
//                    }
//                }
//            }
//            $this->getSubSystem()->getFloor()->isOriginal();
//        }



        return (is_null($this->getOriginal()) && ($this->hasReply === true || is_null($this->hasReply)));

//        return (is_null($this->getOriginal()) && is_null($this->hasReply())) || (is_null($this->getOriginal()) && $this->hasReply() === true);
//        if(is_null($this->getOriginal())){
//            if(){
//
//            }else{
//
//            }
//        }
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