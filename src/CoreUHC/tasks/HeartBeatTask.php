<?php
namespace CoreUHC\tasks;

use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

use pocketmine\Player;

use CoreUHC\Main;

use pocketmine\utils\TextFormat as TF;

use pocketmine\level\Position;

use pocketmine\math\Vector3;

class HeartBeatTask extends PluginTask{

private $plugin;
	
	public function __construct(Main $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->server = $plugin->getServer();
		$this->temp = null;
	}

	public function getPlugin(){
		return $this->plugin;
	}

	public function getServer(){
		return $this->server;
	}

	public function onRun($tick){
		foreach($this->getServer()->getOnlinePlayers() as $p){
			if($this->getPlugin()->teamsEnabled() && $this->getPlugin()->isInTeam($p)){
				$team = $this->getPlugin()->getTeam($p);
				$p->setNameTag(TF::GRAY."[".TF::AQUA.$team->getName().TF::GRAY."]".TF::GOLD.$p->getName()."\n".TF::RESET.TF::RED.$p->getHealth().TF::GRAY."/".TF::RED.$p->getMaxHealth());
			}else{
				$p->setNameTag(TF::GOLD.$p->getName()."\n".TF::RESET.TF::RED.$p->getHealth().TF::GRAY."/".TF::RED.$p->getMaxHealth());
			}
			if($this->getPlugin()->border !== null && $p->getLevel()->getName() === $this->getPlugin()->level->getName()){
				if($this->getPlugin()->border->insideBorder($p->getX(), $p->getZ())) continue;
            	$location = $this->getPlugin()->border->correctPosition($p->getLocation());
            	if($p->getGamemode() === Player::SURVIVAL){
            		$p->teleport($location);
            		$p->sendMessage(Main::PREFIX."You have reached the border!");
            	}
			}
        }
		if($this->getPlugin()->match === null){
			foreach($this->getServer()->getOnlinePlayers() as $p){
				$p->sendTip(Main::PREFIX.TF::GOLD."The match will start soon. Players online: ".TF::AQUA.count($this->getServer()->getOnlinePlayers()).TF::WHITE."/".TF::AQUA.$this->getServer()->getMaxPlayers());
				if($p->getFood() !== 20) $p->setFood(20);
				if($p->getHealth() !== $p->getMaxHealth()) $p->setHealth($p->getMaxHealth());
			}
		}
	}
}