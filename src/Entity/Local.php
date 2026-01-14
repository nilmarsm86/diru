<?php

namespace App\Entity;

use App\Entity\Enums\LocalType;
use App\Entity\Enums\TechnicalStatus;
use App\Entity\Interfaces\MoneyInterface;
use App\Entity\Traits\NameToStringTrait;
use App\Entity\Traits\StructureStateTrait;
use App\Entity\Traits\TechnicalStatusTrait;
use App\Repository\LocalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LocalRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[DoctrineAssert\UniqueEntity(fields: ['name', 'subSystem'], message: 'Ya existe en el subsistema un local con este nombre.', errorPath: 'name', )]
#[DoctrineAssert\UniqueEntity(fields: ['number', 'subSystem'], message: 'Ya existe en el subsistema un local con este número.', errorPath: 'number')]
class Local implements MoneyInterface
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
    //    #[Assert\PositiveOrZero(message: 'El número del local debe ser mayor que 0.')]
    private ?string $number = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'El área está vacía.')]
    //    #[Assert\Positive(message: 'El área debe ser mayor que 0.')]
    //    #[Assert\Expression(
    //        "this.getFloor().getBuilding().getLandArea() < value",
    //        message: 'No debe ser mayor que el area de la obra.',
    //    )]
    private ?float $area = null;

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
        'this.validHeightInEmptyArea()',
        message: 'La altura de un área de vacio siempre debe ser 0.',
        negate: false
    )]
    #[Assert\Expression(
        'this.validHeightInOtherArea()',
        message: 'La altura de un local(AU) o área de elementos verticales(AEV) debe ser mayor que 0.',
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

    /** @var array<mixed> */
    private array $changesFromOriginal;

    public function __construct()
    {
        $this->impactHigherLevels = false;
        $this->localConstructiveAction = null;
        $this->changesFromOriginal = [];
    }

    private static function setDefaultConstructiveAction(EntityManagerInterface $entityManager, ?Local $local, string $constructiveActionName = 'Obra nueva', int $precio = 100): void
    {
        $constructiveAction = $entityManager->getRepository(ConstructiveAction::class)->findOneBy([
            'name' => $constructiveActionName,
        ]);

        $constructiveSystem = $entityManager->getRepository(ConstructiveSystem::class)->findOneBy([
            'name' => 'Ninguno',
        ]);
        $local?->setConstructiveAction($constructiveAction, $constructiveSystem, $precio);
    }

    public function validHeightInEmptyArea(): bool
    {
        return '0' === $this->enumType->value && $this->getHeight() > 0;
    }

    public function validHeightInOtherArea(): bool
    {
        return '0' !== $this->enumType->value && '' !== $this->enumType->value && 0.0 === $this->getHeight();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getArea(): ?float
    {
        if ($this->hasRemoveConstructiveAction()) {
            return 0;
        }

        return $this->area;
    }

    public function setArea(float $area): static
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

        if (LocalType::WallArea === $this->getType() && null === $this->getId()) {
            if (false === $this->getSubSystem()?->hasWalls()) {
                $this->setName('Área de elementos verticales');
            } else {
                $this->setName('Área de elementos verticales '.(int) $this->getSubSystem()?->getMaxLocalNumber() + 1);
            }

            if (is_null($this->getId())) {
                $this->setNumber((string) ((int) $this->getSubSystem()?->getMaxLocalNumber() + 1));
            }
        }

        if (LocalType::EmptyArea === $this->getType() /* || $this->getType() == LocalType::WallArea */) {
            $this->setHeight(0);
        }

        if (!$this->isOriginal() && is_null($this->getId())) {
            $this->setName($this->getName());
        }

        $this->setName(ucfirst($this->getName()));

        if ($this->hasRemoveConstructiveAction()) {
            $this->setArea(0);
        }
    }

    #[ORM\PostLoad]
    public function onLoad(): void
    {
        $type = (is_null($this->type)) ? '' : $this->type;
        $this->setType(LocalType::from($type));
        //        $this->setTechnicalStatus(TechnicalStatus::from($this->technicalStatus));
    }

    public function getVolume(): float|int
    {
        return (float) $this->getArea() * (float) $this->getHeight();
    }

    public static function createAutomaticWall(SubSystem $subSystem, float $area, int $number = 0, bool $reply = false, ?EntityManagerInterface $entityManager = null): self
    {
        $name = ($reply) ? 'Área de elementos verticales (R)' : 'Área de elementos verticales';
        $wall = self::createAutomatic(null, $subSystem, LocalType::WallArea, TechnicalStatus::Undefined, $name, $area, 2.40, $number, $entityManager);
        if ($reply) {
            $wall->setHasReply(false);
            $wall->setTechnicalStatus(TechnicalStatus::Good);

            if (!is_null($entityManager)) {
                // TODO: esto del sistema constructivo ver si realmente necesito repetirlo
                self::setDefaultConstructiveAction($entityManager, $wall, 'No es necesaria', 0);
                $wall->recent();
            }
        }

        return $wall;
    }

    public static function createAutomaticLocal(?Local $local, SubSystem $subSystem, float $area, int $number, bool $reply = false, ?EntityManagerInterface $entityManager = null): self
    {
        $technicalStatus = (true === $subSystem->inNewBuilding()) ? TechnicalStatus::Good : TechnicalStatus::Undefined;

        $local = self::createAutomatic($local, $subSystem, LocalType::Local, $technicalStatus, 'Local', $area, 2.40, $number, $entityManager);

        if ($reply) {
            $local->setHasReply(false);
            $local->setTechnicalStatus(TechnicalStatus::Good);
            // TODO: esto del estado y el sistema constructivo ver si realmente necesito repetirlo
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
        } else {
            //            if (true === $subSystem->inNewBuilding()) {
            //                $subSystem->recent();
            //            } else {
            //                $subSystem->existingWithoutReplicating();
            //            }
            (true === $subSystem->inNewBuilding()) ? $subSystem->recent() : $subSystem->existingWithoutReplicating();
        }

        return $local;
    }

    private static function createAutomatic(?Local $local, SubSystem $subSystem, LocalType $type, TechnicalStatus $technicalStatus, string $name, float $area, float $height, int $number, ?EntityManagerInterface $entityManager = null): self
    {
        if (is_null($local)) {
            $local = new Local();
            $local->setName($name);
            $local->setType($type);
            $local->setArea($area);
            $local->setHeight($height);
            $local->setNumber((string) $number);
            $local->setTechnicalStatus($technicalStatus);
        }

        $subSystem->addLocal($local);
        // TODO: esto del estado y el sistema constructivo ver si realmente necesito repetirlo
        if (true === $subSystem->inNewBuilding()) {
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

        return TechnicalStatus::Undefined !== $this->getTechnicalStatus();
    }

    public function reply(EntityManagerInterface $entityManager, ?SubSystem $parent = null): Floor|static
    {
        $replica = clone $this;
        $replica->setOriginal($this);
        $replica->setName($replica->getName().' (R)');
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

    public function getPrice(): int
    {
        if (is_null($this->getLocalConstructiveAction())) {
            return 0;
        }

        return $this->getLocalConstructiveAction()->getPrice();
    }

    public function getConstructiveAction(): ?ConstructiveAction
    {
        return $this->getLocalConstructiveAction()?->getConstructiveAction();
    }

    public function getConstructiveSystem(): ?ConstructiveSystem
    {
        return $this->getLocalConstructiveAction()?->getConstructiveSystem();
    }

    public function setConstructiveAction(?ConstructiveAction $constructiveAction, ?ConstructiveSystem $constructiveSystem, int $price = 0): static
    {
        if (is_null($this->getLocalConstructiveAction())) {
            $localConstructiveAction = new LocalConstructiveAction();
            $localConstructiveAction->setLocal($this);
            $localConstructiveAction->setPrice($price);
            $localConstructiveAction->setConstructiveSystem($constructiveSystem);
            $localConstructiveAction->setConstructiveAction($constructiveAction);

            $this->setLocalConstructiveAction($localConstructiveAction);
        }

        //        $this->getLocalConstructiveAction()->setConstructiveAction($constructiveAction);
        return $this;
    }

    public function inNewBuilding(): ?bool
    {
        return $this->getSubSystem()?->inNewBuilding();
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
        return (false === $this->hasReply()) && is_null($this->getOriginal()) && $this->isRecent();
    }

    public function hasChangesFromOriginal(): bool
    {
        $hasChanges = false;
        $original = $this->getOriginal();
        $this->changesFromOriginal = [];

        if (is_null($original)) {
            return false;
        }

        if ($original->getNumber() !== $this->getNumber()) {
            $this->changesFromOriginal[] = 'Cambio de número.';
            $hasChanges = true;
        }

        if ($original->getArea() !== $this->getArea()) {
            $this->changesFromOriginal[] = 'Cambio de área.';
            $hasChanges = true;
        }

        if ($original->getType() !== $this->getType()) {
            $this->changesFromOriginal[] = 'Cambio de tipo.';
            $hasChanges = true;
        }

        if ($original->getHeight() !== $this->getHeight()) {
            $this->changesFromOriginal[] = 'Cambio de altura.';
            $hasChanges = true;
        }

        if ($original->getVolume() !== $this->getVolume()) {
            $this->changesFromOriginal[] = 'Cambio de volumen.';
            $hasChanges = true;
        }

        if ($original->getTechnicalStatus() !== $this->getTechnicalStatus()) {
            $this->changesFromOriginal[] = 'Cambio de estado técnico.';
            $hasChanges = true;
        }

        if ($original->isImpactHigherLevels() !== $this->isImpactHigherLevels()) {
            $this->changesFromOriginal[] = 'Cambio en el impacto en niveles superiores.';
            $hasChanges = true;
        }

        if ($this->hasRemoveConstructiveAction()) {
            $this->changesFromOriginal[] = 'El local ha sido removido.';
            $hasChanges = true;
        }

        return $hasChanges;
    }

    /**
     * @return array<mixed>
     */
    public function getChangesFromOriginal(): array
    {
        return $this->changesFromOriginal;
    }

    public function getCurrency(): ?string
    {
        return $this->getSubSystem()?->getFloor()?->getBuilding()?->getProjectCurrency();
    }

    /*public function getFormatedPrice(): string
    {
        return (number_format(((float)$this->getPrice() / 100), 2)) . ' ' . $this->getCurrency();
    }*/

    public function isLocalType(): bool
    {
        return LocalType::Local === $this->getType();
    }

    public function isWallType(): bool
    {
        return LocalType::WallArea === $this->getType();
    }

    public function isEmptyType(): bool
    {
        return LocalType::EmptyArea === $this->getType();
    }

    public function classifiedAsUndefined(): bool
    {
        return TechnicalStatus::Undefined === $this->getTechnicalStatus();
    }

    public function hasTechnicalStatusUndefinedHelp(): bool
    {
        return !$this->isLocalType() && $this->classifiedAsUndefined();
    }

    public function showConstructiveActionInList(bool $reply): bool
    {
        return $reply || (true === $this->inNewBuilding());
    }

    public function hasBackgroundColorOfChange(): bool
    {
        return $this->isNewStructure() || $this->hasChangesFromOriginal() || $this->hasRemoveConstructiveAction() || $this->hasStructuralChange();
    }

    public function hasRemoveConstructiveAction(): bool
    {
        return in_array($this->getConstructiveAction()?->getName(), ConstructiveAction::REMOVE_ACTIONS, true);
    }

    public function hasStructuralChange(): bool
    {
        return in_array($this->getConstructiveAction()?->getName(), ConstructiveAction::STRUCTURAL_CHANGE_ACTIONS, true);
    }

    public function getConstructiveActionAmount(): float
    {
        if (!$this->hasLocalConstructiveAction()) {
            return 0;
        }

        $area = (float) $this->getArea();
        // TODO: poner la categoria de eliminacion a la de modificacion y no modificacion
        if (in_array($this->getLocalConstructiveAction()?->getConstructiveAction()?->getName(), ['Eliminación', 'Demolición'], true)) {
            $area = (float) $this->getOriginal()?->getArea();
        }

        return (int) $this->getLocalConstructiveAction()?->getPrice() * $area;
    }
}
