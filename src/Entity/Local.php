<?php

namespace App\Entity;

use App\Entity\Enums\TechnicalStatus;
use App\Entity\Enums\LocalType;
use App\Entity\Enums\StructureState;
use App\Entity\Traits\HasReplyTrait;
use App\Entity\Traits\NameToStringTrait;
use App\Entity\Traits\OriginalTrait;
use App\Entity\Traits\StructureStateTrait;
use App\Entity\Traits\TechnicalStatusTrait;
use App\Repository\LocalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

#[ORM\Entity(repositoryClass: LocalRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'subSystem'], message: 'Ya existe en el subsistema un local con este nombre.', errorPath: 'name',)]
#[DoctrineAssert\UniqueEntity(fields: ['number', 'subSystem'], message: 'Ya existe en el subsistema un local con este número.', errorPath: 'number')]
class Local
{
    use NameToStringTrait;
    use StructureStateTrait;
    use TechnicalStatusTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El número del local está vacío.')]
    #[Assert\PositiveOrZero(message: 'El número del local debe ser mayor que 0.')]
    private ?int $number = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El área está vacía.')]
    #[Assert\Positive(message: 'El área debe ser mayor que 0.')]
//    #[Assert\Expression(
//        "this.getFloor().getBuilding().getLandArea() < value",
//        message: 'No debe ser mayor que el area de la obra.',
//    )]
    private ?int $area = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[Assert\Choice(
        choices: LocalType::CHOICES,
        message: 'Seleccione un tipo de local.'
    )]
    public LocalType $enumType;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La altura está vacía.')]
    #[Assert\Expression(
        "this.validHeightInEmptyArea()",
        message: 'La altura de un área de vacio siempre debe ser 0.',
        negate: false
    )]
    #[Assert\Expression(
        "this.validHeightInOtherArea()",
        message: 'La altura de un local o área de muro debe ser mayor que 0.',
        negate: false
    )]
    private ?float $height = null;

    #[ORM\ManyToOne(inversedBy: 'locals')]
    #[ORM\JoinColumn(nullable: false)]
//    #[Assert\Valid]
//    #[Assert\NotBlank(message: 'Establezca el subsistema.')]
    private ?SubSystem $subSystem = null;

    #[ORM\Column]
    private ?bool $impactHigherLevels = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\OneToOne(inversedBy: 'local', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
    private ?LocalConstructiveAction $localConstructiveAction = null;

    public function __construct()
    {
        $this->impactHigherLevels = false;
        $this->localConstructiveAction = null;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param Local|null $local
     * @return void
     */
    private static function setDefaultConstructiveAction(EntityManagerInterface $entityManager, ?Local $local): void
    {
        $constructiveAction = $entityManager->getRepository(ConstructiveAction::class)->findOneBy([
            'name' => 'Obra nueva'
        ]);
        $local->setConstructiveAction($constructiveAction);
    }

    public function validHeightInEmptyArea(): bool
    {
        return $this->enumType->value == '0' && $this->getHeight() > 0;
    }

    public function validHeightInOtherArea(): bool
    {
        return $this->enumType->value != '0' && $this->enumType->value != '' && $this->getHeight() == 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(int $area): static
    {
        $this->area = $area;

        return $this;
    }

    public function getType(): LocalType
    {
        return $this->enumType;
    }

    public function setType(LocalType $enumType): static
    {
        $this->type = '';
        $this->enumType = $enumType;
        return $this;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

//    public function getFormatedHeight(): ?float
//    {
//        return number_format(((float) $this->getHeight()), 2);
//    }

    public function setHeight(float $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getSubSystem(): ?SubSystem
    {
        return $this->subSystem;
    }

    public function setSubSystem(?SubSystem $subSystem): static
    {
        $this->subSystem = $subSystem;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onSave(): void
    {
        $this->type = $this->getType()->value;
//        $this->technicalStatus = $this->getTechnicalStatus()->value;

        if ($this->getType() == LocalType::WallArea && is_null($this->getId())) {
            if (!$this->getSubSystem()->hasWalls()) {
                $this->setName('Área de muro');
            } else {
                $this->setName('Área de muro ' . $this->getSubSystem()->getMaxLocalNumber() + 1);
            }

            if (is_null($this->getId())) {
                $this->setNumber($this->getSubSystem()->getMaxLocalNumber() + 1);
            }
        }

        if ($this->getType() == LocalType::EmptyArea /*|| $this->getType() == LocalType::WallArea*/) {
            $this->setHeight(0);
        }

        if (!$this->isOriginal() && is_null($this->getId())) {
            $this->setName($this->getName());
        }

        $this->setName(ucfirst($this->getName()));
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $this->setType(LocalType::from($this->type));
//        $this->setTechnicalStatus(TechnicalStatus::from($this->technicalStatus));
    }

    public function getVolume(): float|int
    {
        return $this->getArea() * $this->getHeight();
    }

    public static function createAutomaticWall(SubSystem $subSystem, int $area, int $number = 0, bool $reply = false, EntityManagerInterface $entityManager = null): self
    {
        $name = ($reply) ? 'Área de muro (R)' : 'Área de muro';
        $wall = self::createAutomatic(null, $subSystem, LocalType::WallArea, TechnicalStatus::Undefined, $name, $area, 1, $number, $entityManager);
        if ($reply) {
            $wall->setHasReply(false);
            $wall->setTechnicalStatus(TechnicalStatus::Good);

            if (!is_null($entityManager)) {
                self::setDefaultConstructiveAction($entityManager, $wall);
                $wall->recent();
            }
        }

        return $wall;
    }

    public static function createAutomaticLocal(?Local $local, SubSystem $subSystem, int $area, int $number, bool $reply = false, EntityManagerInterface $entityManager = null): static
    {
        $technicalStatus = ($subSystem->inNewBuilding()) ? TechnicalStatus::Good : TechnicalStatus::Undefined;

        $local = self::createAutomatic($local, $subSystem, LocalType::Local, $technicalStatus, 'Local', $area, 2.40, $number, $entityManager);

        if ($reply) {
            $local->setHasReply(false);
            $local->setTechnicalStatus(TechnicalStatus::Good);

            if (!is_null($entityManager)) {
                self::setDefaultConstructiveAction($entityManager, $local);
//                if ($subSystem->isRecent()) {
//                    $local->recent();
//                } else {
//                    $local->replica();
//                }
//                if($subSystem->isReplica() and !is_null($subSystem->getOriginal())){
//                    $local->replica();
//                } else {
//                    $local->recent();
//                }
                $local->recent();
            }
        }else{
            if($subSystem->inNewBuilding()){
                $subSystem->recent();
            }else{
                $subSystem->existingWithoutReplicating();
            }
        }

        return $local;
    }

    private static function createAutomatic(?Local $local, SubSystem $subSystem, LocalType $type, TechnicalStatus $technicalStatus, string $name, int $area, float $height, int $number, EntityManagerInterface $entityManager = null): self
    {
        if (is_null($local)) {
            $local = new Local();
            $local->setName($name);
            $local->setType($type);
            $local->setArea($area);
            $local->setHeight($height);
            $local->setNumber($number);
            $local->setTechnicalStatus($technicalStatus);
        }

        $subSystem->addLocal($local);

        if ($subSystem->inNewBuilding()) {
            $local->recent();
            if (!is_null($entityManager) && is_null($local->getLocalConstructiveAction())) {
                self::setDefaultConstructiveAction($entityManager, $local);
            }
        } else {
//            if($subSystem->isRecent()){
//                $local->recent();
//            }else{
            $local->existingWithoutReplicating();
//            }
        }

        return $local;
    }

    public function isClassified(): bool
    {
        if (!$this->isLocalType()) {
            return true;
        }
        return $this->getTechnicalStatus() !== TechnicalStatus::Undefined;
    }

    public function reply(EntityManagerInterface $entityManager, object $parent = null): Floor|static
    {
        $replica = clone $this;
        $replica->setOriginal($this);
        $replica->setName($replica->getName() . ' (R)');
        $replica->setSubSystem($parent);
        $replica->setHasReply(false);
        $replica->replica();

        $entityManager->persist($replica);

        $this->setHasReply(true);
        $this->existingReplicated();

        $entityManager->persist($this);

        return $replica;
    }

    public function isImpactHigherLevels(): ?bool
    {
        return $this->impactHigherLevels;
    }

    public function setImpactHigherLevels(bool $impactHigherLevels): static
    {
        $this->impactHigherLevels = $impactHigherLevels;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->getLocalConstructiveAction()?->getPrice();
    }

    public function getConstructiveAction(): ?ConstructiveAction
    {
        return $this->getLocalConstructiveAction()?->getConstructiveAction();
    }

    public function setConstructiveAction(?ConstructiveAction $constructiveAction): static
    {
        if (is_null($this->getLocalConstructiveAction())) {
            $localConstructiveAction = new LocalConstructiveAction();
            $localConstructiveAction->setLocal($this);
            $localConstructiveAction->setPrice(0);
        }

        $this->getLocalConstructiveAction()->setConstructiveAction($constructiveAction);

        return $this;
    }

    public function inNewBuilding(): ?bool
    {
        return $this->getSubSystem()->inNewBuilding();
    }

//    public function hasReply(): ?bool
//    {
//        if(!$this->inNewBuilding() && !$this->isOriginal()){
//            return false;
//        }
//        return $this->getSubSystem()->hasReply();
//    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getLocalConstructiveAction(): ?LocalConstructiveAction
    {
        return $this->localConstructiveAction;
    }

    public function hasLocalConstructiveAction(): bool
    {
        return !is_null($this->getLocalConstructiveAction());
    }

    public function setLocalConstructiveAction(?LocalConstructiveAction $localConstructiveAction): static
    {
        $this->localConstructiveAction = $localConstructiveAction;

        return $this;
    }

    public function isNewInReply(): bool
    {
        return ($this->hasReply() === false) && is_null($this->getOriginal()) && $this->isRecent();
    }

    public function changeFromOriginal(): bool
    {
        return false;
    }

    public function getCurrency(): ?string
    {
        return $this->getSubSystem()->getFloor()->getBuilding()->getProjectCurrency();
    }

    public function getFormatedPrice(): string
    {
        return (number_format(((float)$this->getPrice() / 100), 2)) . ' ' . $this->getCurrency();
    }

    public function isLocalType(): bool
    {
        return $this->getType() === LocalType::Local;
    }

    public function isWallType(): bool
    {
        return $this->getType() === LocalType::WallArea;
    }

    public function isEmptyType(): bool
    {
        return $this->getType() === LocalType::EmptyArea;
    }

    public function classifiedAsUndefined(): bool
    {
        return $this->getTechnicalStatus() === TechnicalStatus::Undefined;
    }

    public function hasTechnicalStatusUndefinedHelp(): bool
    {
        return !$this->isLocalType() && $this->classifiedAsUndefined();
    }

    public function showConstructiveActionInList(bool $reply): bool
    {
        return $reply || $this->inNewBuilding();
    }



}
