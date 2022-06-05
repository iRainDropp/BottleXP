<?php

namespace iRainDrop\BottleXP;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ExperienceBottle;
use pocketmine\player\Player;

class PlayerListener implements Listener
{
    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function ThrowBottle(PlayerItemUseEvent $e){
        $item = $e->getItem();
        $id = $item->getId();
        if($item instanceof ExperienceBottle && $item->getNamedTag()->getTag("XP") !== null){
            $amount = $item->getNamedTag()->getInt("XP");
            $e->getPlayer()->getXpManager()->addXp($amount);
            $count = $item->setCount($item->getCount() - 1);
            $e->getPlayer()->getInventory()->setItemInHand($count);
            $this->plugin->sendSound(Main::$config->getNested("Sounds.Use"), $e->getPlayer());
            $e->cancel();
        }
    }

    public function playerDeath(PlayerDeathEvent $e){
        $player = $e->getPlayer();
        $damager = $player->getLastDamageCause()->getEntity();
        $xp = $player->getXpManager();
        if($xp->getCurrentTotalXp() > 0 && Main::$config->get("Drop on Death") == true){
            if($damager instanceof Player){
                $this->plugin->createBottle($xp, $damager, "drop");
                $e->setXpDropAmount(0);
            }
        }
    }
}