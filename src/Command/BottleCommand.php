<?php

namespace iRainDrop\BottleXP\Command;

use iRainDrop\BottleXP\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as C;
use pocketmine\item\VanillaItems;

class BottleCommand extends Command implements PluginOwned
{
    private $plugin;

    public function getOwningPlugin(): Main
    { return $this->plugin; }

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct("bottle", "Stores your XP in a Bottle.", "", ["xp", "xpbottle"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        $player = $sender->getServer()->getPlayerByPrefix($sender->getName());
        $XP = $player->getXpManager()->getCurrentTotalXp();
        if($XP > 0){
            $this->plugin->createBottle($player->getXpManager(), $player, "give");
            $this->plugin->sendSound(Main::$config->getNested("Sounds.Creation"), $player);
        }
        else{
            $sender->sendMessage(Main::$config->get("No XP"));
        }
    }
}