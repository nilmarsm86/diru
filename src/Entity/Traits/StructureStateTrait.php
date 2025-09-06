<?php

namespace App\Entity\Traits;

use App\Entity\Enums\StructureState;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait StructureStateTrait
{
    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[Assert\Choice(choices: StructureState::CHOICES, message: 'Seleccione un estado válido.')]
    protected ?StructureState $enumState = null;

    #[ORM\Column(nullable: true)]
    private ?bool $hasReply = null;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $original = null;

    /**
     * @return StructureState|null
     */
    public function getState(): ?StructureState
    {
        return $this->enumState;
    }

    /**
     * @param StructureState $state
     * @return $this
     */
    public function setState(StructureState $state): static
    {
        $this->enumState = $state;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSaveState(): void
    {
        $this->state = $this->getState()?->value;
    }

    #[ORM\PostLoad]
    public function onLoadState(): void
    {
        $this->setState(StructureState::from($this->state));
    }

    /**
     * Is recent or not
     * @return bool
     */
    public function isRecent(): bool
    {
        return $this->enumState?->name === StructureState::Recent->name;
    }

    /**
     * Is existing without replicating or not
     * @return bool
     */
    public function isExistingWithoutReplicating(): bool
    {
        return $this->enumState?->name === StructureState::ExistingWithoutReplicating->name;
    }

    /**
     * Is existing replicated or not
     * @return bool
     */
    public function isExistingReplicated(): bool
    {
        return $this->enumState?->name === StructureState::ExistingReplicated->name;
    }

    /**
     * Is Replica or not
     * @return bool
     */
    public function isReplica(): bool
    {
        return $this->enumState?->name === StructureState::Replica->name;
    }

    /**
     * Recent
     * @return $this
     */
    public function recent(): static
    {
        $this->state = null;
        $this->setState(StructureState::Recent);
        return $this;
    }

    /**
     * ExistingWithoutReplicating
     * @return $this
     */
    public function existingWithoutReplicating(): static
    {
        $this->state = null;
        $this->setState(StructureState::ExistingWithoutReplicating);
        return $this;
    }

    /**
     * ExistingReplicated
     * @return $this
     */
    public function existingReplicated(): static
    {
        $this->state = null;
        $this->setState(StructureState::ExistingReplicated);
        return $this;
    }

    /**
     * Replica
     * @return $this
     */
    public function replica(): static
    {
        $this->state = null;
        $this->setState(StructureState::Replica);
        return $this;
    }

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

    public function isNewStructure(): bool
    {
        return $this->inNewBuilding() || $this->isNewInReply();
    }
}
