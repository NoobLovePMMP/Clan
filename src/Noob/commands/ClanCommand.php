<?php

namespace Noob\commands;

/*
███╗   ██╗██╗  ██╗██╗   ██╗████████╗    ██████╗ ███████╗██╗   ██╗
████╗  ██║██║  ██║██║   ██║╚══██╔══╝    ██╔══██╗██╔════╝██║   ██║
██╔██╗ ██║███████║██║   ██║   ██║       ██║  ██║█████╗  ██║   ██║
██║╚██╗██║██╔══██║██║   ██║   ██║       ██║  ██║██╔══╝  ╚██╗ ██╔╝
██║ ╚████║██║  ██║╚██████╔╝   ██║       ██████╔╝███████╗ ╚████╔╝ 
╚═╝  ╚═══╝╚═╝  ╚═╝ ╚═════╝    ╚═╝       ╚═════╝ ╚══════╝  ╚═══╝  
        Copyright © 2024 - 2025 NoobMCGaming
*/    

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;
use Noob\{Clan};
use Noob\forms\ClanForm;
use pocketmine\Server;

class ClanCommand extends Command implements PluginOwned
{
    private Clan $plugin;
    public string $prefix = "§l§eＶie§cＤＡＲＫ";

    public function __construct(Clan $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("clan", "Tổ Đội", null, ["todoi"]);
        $this->setPermission("clan.cmd");
    }

    public function execute(CommandSender $player, string $label, array $args){
        if (!$player instanceof Player) {
            $this->getOwningPlugin()->getLogger()->notice("Xin hãy sử dụng lệnh trong trò chơi");
            return 1;
        }
        $form = new ClanForm;
        if(!$this->plugin->playerHasClan($player)){
            $form->createClan($player);
        }
        else{
            $form->clanMenu($player);
        }

    }

    public function getOwningPlugin(): Clan{
        return $this->plugin;
    }
}