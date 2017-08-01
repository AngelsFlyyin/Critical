<?php

namespace Angel\Critical;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\utils\Random;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;


use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\entity\Effect;

use pocketmine\utils\TextFormat as TF;

use pocketmine\level\particle\CriticalParticle;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class Main extends PluginBase implements Listener{
    
    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->notice("Critical Plugin Suecessfully enabled by Angel @VortexZMcPe");
    }
    
    public function entitydamage(EntityDamageEvent $ev){
        $entity = $ev->getEntity();
        if($ev instanceof EntityDamageByEntityEvent){
            $damager = $ev->getDamager();
            if(($player = $entity) instanceof Player && $damager instanceof Player){
                if(!$damager->isOnGround() && !$damager->hasEffect(Effect::BLIND)){
                    if(!$this->isInSpawn($damager) && !$this->isInSpawn($player)){
                        $ev->setDamage($ev->getDamage(EntityDamageByEntityEvent::MODIFIER_BASE) * 1.5);
                        $damager->sendPopup(TF::RED."Critical Hit to player ".TF::GRAY.$player->getName());
                        $player->sendPopup(TF::RED."You were Criticaly hit by player".TF::GRAY.$damager->getName());
                        
                        $p = $damager; # am lazy
                        $particle = new CriticalParticle(new Vector3($p->x, $p->y + 1, $p->z));
				    	$random = new Random((int) (microtime(true) * 1000) + mt_rand());
				    	for($i = 0; $i < 60; ++$i){
				    	    $particle->setComponents(
						    $p->x + $random->nextSignedFloat() * $p->x,
						    $p->y + 1.5 + $random->nextSignedFloat() * $p->y + 1.5,
						    $p->z + $random->nextSignedFloat() * $p->z);
						    $p->getLevel()->addParticle($particle);
				    	}
                    }
                }
            }
        }
    }
    
    public function isInSpawn(Player $player){
        $v = new Vector3($player->getLevel()->getSpawnLocation()->getX(),$player->getPosition()->getY(),$player->getLevel()->getSpawnLocation()->getZ());
        $r = $this->getServer()->getSpawnRadius();
        if($player->getPosition()->distance($v) <= $r){
            return true;
        }
    }
}
