<?php

namespace trix;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\entity\effect\EffectInstance;

class Main extends PluginBase implements Listener
{
    private int $absorptionRemovalDelay;
    private int $resistanceLevel;
    private int $resistanceDuration;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
        $this->absorptionRemovalDelay = $this->getConfig()->get('absorption-removal-delay', 5);
        $this->resistanceLevel = $this->getConfig()->get('resistance-level', 1);
        $this->resistanceDuration = $this->getConfig()->get('resistance-duration', 10);

    }
  
  public function onPlayerItemConsume(PlayerItemConsumeEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if($item->getTypeId() === ItemTypeIds::GOLDEN_APPLE or $item->getTypeId() === ItemTypeIds::ENCHANTED_GOLDEN_APPLE) {
            $this->removeAbsorptionEffectAfterDelay($player, $this->absorptionRemovalDelay);
            $this->addResistance($player);
        }
    }

    private function removeAbsorptionEffectAfterDelay(Player $player, int $delay): void {
       $this->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
            $player->getEffects()->remove(VanillaEffects::ABSORPTION());
        }), 20 * $delay);
    }

    private function addResistance(Player $player): void {
        $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), $this->resistanceDuration * 20, $this->resistanceLevel - 1));
    }
}
