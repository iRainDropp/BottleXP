<?php

declare(strict_types=1);

namespace iRainDrop\XPBottle;

use iRainDrop\XPBottle\Command\BottleCommand;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as C;

class Main extends PluginBase{

    public static $config;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new PlayerListener($this), $this);
        $this->getServer()->getCommandMap()->register("bottle", new BottleCommand ($this));
        $this->saveresource("config.yml");
        self::$config = new Config($this->getDataFolder() . "config.yml");
    }

    public function sendSound(string $sound, Player $player){
        $pk = new PlaySoundPacket;
        $pk->soundName = $sound;
        $pk->volume = 5;
        $pk->pitch = 1;
        $pk->x = $player->getPosition()->getX();
        $pk->y = $player->getPosition()->getY();
        $pk->z = $player->getPosition()->getZ();
        $player->getNetworkSession()->sendDataPacket($pk);
    }

    public function createBottle($xp, Player $player, $type){
        $item = VanillaItems::EXPERIENCE_BOTTLE();
        $item->getNamedTag()->setInt("XP", $xp->getCurrentTotalXp());
        $item->setCustomName(str_replace("{XP}", strval($xp->getCurrentTotalXp()), C::RESET . C::WHITE . Main::$config->get("Custom Name")));
        $lore = str_replace("{XP}", strval($xp->getCurrentTotalXp()), Main::$config->get("Lore"));
        $item->setLore(str_replace("{player}", $player->getName(), $lore));
        $player->getXpManager()->setCurrentTotalXp(0);
        if($type == "give"){
            $player->getInventory()->addItem($item);
        }
        if($type == "drop"){
            $x = $player->getLocation()->getX();
            $y = $player->getLocation()->getY();
            $z = $player->getLocation()->getZ();
            $level = $player->getWorld();
            $vector3 = new Vector3($x, $y, $z);
            $level->dropItem($vector3, $item);
        }
    }
}
