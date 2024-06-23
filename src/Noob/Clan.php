<?php

namespace Noob;

/*
███╗   ██╗██╗  ██╗██╗   ██╗████████╗    ██████╗ ███████╗██╗   ██╗
████╗  ██║██║  ██║██║   ██║╚══██╔══╝    ██╔══██╗██╔════╝██║   ██║
██╔██╗ ██║███████║██║   ██║   ██║       ██║  ██║█████╗  ██║   ██║
██║╚██╗██║██╔══██║██║   ██║   ██║       ██║  ██║██╔══╝  ╚██╗ ██╔╝
██║ ╚████║██║  ██║╚██████╔╝   ██║       ██████╔╝███████╗ ╚████╔╝ 
╚═╝  ╚═══╝╚═╝  ╚═╝ ╚═════╝    ╚═╝       ╚═════╝ ╚══════╝  ╚═══╝  
        Copyright © 2024 - 2025 NoobMCGaming
*/                                               


use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Server;
use pocketmine\event\Listener as L;

use Noob\commands\ClanCommand;

class Clan extends PluginBase implements L{

    public $players;
    public $clan;
	public static $instance;

	public static function getInstance() : self {
		return self::$instance;
	}

	public function onEnable(): void{
        self::$instance = $this;
        $this->getServer()->getCommandMap()->register("/clan", new ClanCommand($this));
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->players = new Config($this->getDataFolder() . "players.yml", Config::YAML);
        $this->clan = new Config($this->getDataFolder() . "clan.yml", Config::YAML);
	}

    public function getPlayerManager(){
        return $this->players;
    }

    public function getClanManager(){
        return $this->clan;
    }

    public function onJoin(PlayerJoinEvent $ev){
        $player = $ev->getPlayer();
        if(!$this->getPlayerManager()->exists($player->getName())){
            $this->getPlayerManager()->set($player->getName(), "Không Có");
            $this->getPlayerManager()->save();
        }
    }

    public function playerHasClan(Player $player): bool{
        if($this->getPlayerManager()->get($player->getName()) != "Không Có") return true;
        else return false;
    }

    public function isDefaultName(string $clanName): bool{
        for($i = 0; $i < strlen($clanName); $i++){
            if((string)$clanName[$i] == "§"){
                return false;
            }
        }
        if(strlen($clanName) > 16){
            return false;
        }
        return true;
    }

    public function hasClanName(string $clanName): bool{
        if($this->getClanManager()->exists($clanName)) return true;
        return false;
    }

    public function createClanData(string $ownerName, string $clanName): void{
        $this->getClanManager()->set($clanName, [
            "Owner" => $ownerName,
            "Point" => 0,
            "Members" => $ownerName
        ]);
        $this->getClanManager()->save();
    }

    public function createClan(Player $player, string $clanName): void{
        $this->getPlayerManager()->set($player->getName(), $clanName);
        $this->getPlayerManager()->save();
        $this->createClanData($player->getName(), $clanName);
    }

    public function getClan(Player $player){
        return $this->getPlayerManager()->get($player->getName());
    }

    public function getClanByName(string $playerName){
        return $this->getPlayerManager()->get($playerName);
    }

    public function getOwnerOfClan(string $clanName){
        return $this->getClanManager()->getNested($clanName . ".Owner");
    }

    public function getMembersOfClan(string $clanName){
        return $this->getClanManager()->getNested($clanName . ".Members");
    }

    public function getPointOfClan(string $clanName){
        return $this->getClanManager()->getNested($clanName . ".Point");
    }

    public function getCountMemberOfClan(string $clanName){
        $members = $this->getMembersOfClan($clanName);
        $count = explode(", ", $members);
        return count($count);
    }

    public function isOwnerOfClan(Player $player, string $clanName): bool{
        $owner = $this->getOwnerOfClan($clanName);
        if($owner == $player->getName()) return true;
        return false;
    }

    public function donateClan(string $clanName, int $point){
        $this->getClanManager()->setNested($clanName. ".Point", $this->getClanManager()->getNested($clanName.".Point") + $point);
        $this->getClanManager()->save();
    }

    public function changeOwner(string $clanName, string $newOwner){
        $this->getClanManager()->setNested($clanName. ".Owner", $newOwner);
        $this->getClanManager()->save();
    }

    public function updateClan(string $clanName){
        $member = "";
        foreach ($this->getPlayerManager()->getAll() as $clanPlayer => $clanPlayerName){
            if($clanPlayerName == $clanName){
                $member .= $clanPlayer;
            }
        }
        $this->getClanManager()->setNested($clanName. ".Members", $member);
        $this->getClanManager()->save();
    }

    public function deleteClan(string $clanName){
        $this->getClanManager()->remove($clanName);
        $this->getClanManager()->save();
        foreach ($this->getPlayerManager()->getAll() as $clanPlayer => $clanPlayerName){
            if($clanPlayerName == $clanName){
                $this->getPlayerManager()->set($clanPlayer, "Không Có");
                $this->getPlayerManager()->save();
            }
        }
    }
}