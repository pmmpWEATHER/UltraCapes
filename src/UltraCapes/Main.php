<?php

/**
* Copyright (c) 2019 SuperStulle007
* Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

declare(strict_types=1);

namespace UltraCapes;

use pocketmine\plugin\PluginBase;
use pocketmine\entity\Skin;
use pocketmine\utils\TextFormat as C;
use pocketmine\command\{
	Command, CommandSender
};
use pocketmine\event\Listener;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use jojoe77777\FormAPI;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\event\player\{
	PlayerJoinEvent, PlayerQuitEvent, PlayerChangeSkinEvent
};

class Main extends PluginBase implements Listener {

    protected $skin = [];
    public $skins;
    /** @var string */
    public $noperm = C::AQUA . "§f[§bServer§f] §cDir fehlen Berechtigungen für diesen Befehl!";

    /**
     * @return void
     */

    public function checkDepends(){
        $this->formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        if(is_null($this->formapi)){
            $this->getLogger()->info("§4FormAPI must be installed!");
            $this->getPluginLoader()->disablePlugin($this);
            }
        }

    public function onEnable() {
        $this->saveResource("capes.yml");
        $this->cfg = new Config($this->getDataFolder() . "capes.yml", Config::YAML);
        foreach ($this->cfg->get("capes") as $cape) {
            $this->saveResource("$cape.png");
        }
    }

	public function onJoin(PlayerJoinEvent $eve) {
		$player = $eve->getPlayer();
		$this->skin[$player->getName()] = $player->getSkin();
	}

	public function onQuit(PlayerQuitEvent $eve) {
		$player = $eve->getPlayer();
		unset($this->skin[$player->getName()]);
	}

	public function onChangeSkin(PlayerChangeSkinEvent $eve) {
		$player = $eve->getPlayer();
		$this->skin[$player->getName()] = $player->getSkin();
	}
	
       public function createCape($capeName) {
            $path = $this->getDataFolder()."{$capeName}.png";

            $img = @imagecreatefrompng($path);

            $bytes = '';

            $l = (int) @getimagesize($path)[1];

            for ($y = 0; $y < $l; $y++) {

                for ($x = 0; $x < 64; $x++) {

                    $rgba = @imagecolorat($img, $x, $y);

                    $a = ((~((int)($rgba >> 24))) << 1) & 0xff;

                    $r = ($rgba >> 16) & 0xff;

                    $g = ($rgba >> 8) & 0xff;

                    $b = $rgba & 0xff;

                    $bytes .= chr($r) . chr($g) . chr($b) . chr($a);

                }

            }

        @imagedestroy($img);
        return $bytes;
    }
        
    public function onCommand(CommandSender $player, Command $command, string $label, array $args): bool {
        $this->cfg = new Config($this->getDataFolder() . "capes.yml", Config::YAML);
       $cape = $this->cfg->get("capes");
        switch (strtolower($command->getName())) {
            case "cape":
                if ($player instanceof Player) {
                    if (!isset($args[0])) {
                            $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, $data){
            $result = $data;
            if ($result == null) {
            }
            switch ($result) {
                    case 0:
                        $command = "cape abort";
								$this->getServer()->getCommandMap()->dispatch($player, $command);
                        break;
                    case 1:
                    $command = "cape remove";
								$this->getServer()->getCommandMap()->dispatch($player, $command);
						break;
						           case 2:
                    $command = "cape blue_creeper";
								$this->getServer()->getCommandMap()->dispatch($player, $command);
                        break;
                                   case 3:
                    $command = "cape enderman";
								$this->getServer()->getCommandMap()->dispatch($player, $command);
                        break;
                                   case 4:
                    $command = "cape energy";
								$this->getServer()->getCommandMap()->dispatch($player, $command);
                        break;
                   case 5:
                    $command = "cape fire";
								$this->getServer()->getCommandMap()->dispatch($player, $command);
						break;
             }
             });
        $form->setTitle("§bUltraCapes Menu");
        $form->setContent("§f>> Here you can choose a Cape!");
        $form->addButton("§4Abort", 0);
        $form->addButton("§0Remove a Cape", 1);
        $form->addButton("§eBlue-Creeper-Cape > $30", 2);
        $form->addButton("§eEndermancape > $100", 3);
        $form->addButton("§eEnergycape > $200", 4);
        $form->addButton("§eFirecape > $250", 5);
        $form->sendToPlayer($player);
        }
        return true;
                    }
                    $arg = array_shift($args);
                    switch ($arg) {
                        case "abort":
                            return true;
                            break;
                        case "remove":
		unset($this->skin[$player->getName()]);
                            $player->sendMessage("§f[§bServer§f] §aSkin resetted!");
                            return true;
                        case "blue_creeper":
				 $m = EconomyAPI::getInstance()->myMoney($sender);
	
				if($player->hasPermission("blue_creeper.cape") === false) {
				  if($m >=30){
						      EconomyAPI::getInstance()->reduceMoney($player, 30);
				    $player->sendMessage("§f[§bBedWars Shop§f] §aBlue creeper cape purchased!");
				   $player->addPermission("blue_creeper.cape");
				$oldSkin = $player->getSkin();
				$capeData = $this->createCape("Blue_Creeper");
				$setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
				$player->setSkin($setCape);
					$player->sendSkin();
					$player->sendMessage("§f[§bServer§f] §aBlue Creeper Cape activated!");
						 } else {
					  $player->sendMessage("§f[§bServer§f] §aBlue Creeper Cape failed cause you have no money!");
				  }
				} else {
				  $oldSkin = $player->getSkin();
				$capeData = $this->createCape("Blue_Creeper");
				$setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
				$player->setSkin($setCape);
					$player->sendSkin();
					$player->sendMessage("§f[§bServer§f] §aBlue_Creeper Creeper Cape activated!");
				}
                            return true;
                        case "enderman":
					    $m = EconomyAPI::getInstance()->myMoney($player);
				
				if($player->hasPermission("enderman.cape") === false) {
				  if($m >=100){
						      EconomyAPI::getInstance()->reduceMoney($player, 100);
				    $player->sendMessage("§f[§bBedWars Shop§f] §aEnderman cape purchased!");
				    $player->hasPermission("enderman.cape");
				$oldSkin = $player->getSkin();
				$capeData = $this->createCape("Enderman");
				$setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
				$player->setSkin($setCape);
					$player->sendSkin();
					$player->sendMessage("§f[§bServer§f] §aEnderman activated!");
						 } else {
					  $player->sendMessage("§f[§bServer§f] §aEnderman Creeper Cape failed cause you have no money!");
				  }
				} else {
				  $oldSkin = $player->getSkin();
				$capeData = $this->createCape("Enderman");
				$setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
				$player->setSkin($setCape);
					$player->sendSkin();
					$player->sendMessage("§f[§bServer§f] §aEnderman Cape activated!");
				}
				    return true;
                        case "energy":
                            	    $m = EconomyAPI::getInstance()->myMoney($player);
				if($player->hasPermission("energy.cape") === false) {
				  if($m >=150){
						      EconomyAPI::getInstance()->reduceMoney($player, 150);
				    $player->sendMessage("§f[§bBedWars Shop§f] §aEnergy cape purchased!");
				    $player->addPermission("energy.cape");
				$oldSkin = $player->getSkin();
				$capeData = $this->createCape("Energy");
				$setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
				$player->setSkin($setCape);
					$player->sendSkin();
					$player->sendMessage("§f[§bServer§f] §aEnergy Cape activated!");
						 } else {
					  $player->sendMessage("§f[§bServer§f] §cEnergy Cape failed cause you have no money!");
				  }
				} else {
				  $oldSkin = $player->getSkin();
				$capeData = $this->createCape("Energy");
				$setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
				$player->setSkin($setCape);
					$player->sendSkin();
					$player->sendMessage("§f[§bServer§f] §aEnergy Cape activated!");
				}
                return true;
                        case "fire":
                           	    $m = EconomyAPI::getInstance()->myMoney($player);
				if($player->hasPermission("fire.cape") === false) {
				  if($m >=250){
						      EconomyAPI::getInstance()->reduceMoney($player, 250);
				    $player->sendMessage("§f[§bBedWars Shop§f] §aFire creeper cape purchased!");
				    $player->addPermission("fire.cape");
				$oldSkin = $player->getSkin();
				$capeData = $this->createCape("Fire");
				$setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
				$player->setSkin($setCape);
					$player->sendSkin();
					$player->sendMessage("§f[§bServer§f] §aFire Cape activated!");
						 } else {
					  $player->sendMessage("§f[§bServer§f] §aFire Cape failed cause you have no money!");
				  }
				} else {
				  $oldSkin = $player->getSkin();
				$capeData = $this->createCape("Fire");
				$setCape = new Skin($oldSkin->getSkinId(), $oldSkin->getSkinData(), $capeData, $oldSkin->getGeometryName(), $oldSkin->getGeometryData());
				$player->setSkin($setCape);
					$player->sendSkin();
					$player->sendMessage("§f[§bServer§f] §aFire Cape activated!");
				}
                return true;
                         
  }
        }
  return true;
}
}
