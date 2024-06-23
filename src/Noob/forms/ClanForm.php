<?php

namespace Noob\forms;

/*
███╗   ██╗██╗  ██╗██╗   ██╗████████╗    ██████╗ ███████╗██╗   ██╗
████╗  ██║██║  ██║██║   ██║╚══██╔══╝    ██╔══██╗██╔════╝██║   ██║
██╔██╗ ██║███████║██║   ██║   ██║       ██║  ██║█████╗  ██║   ██║
██║╚██╗██║██╔══██║██║   ██║   ██║       ██║  ██║██╔══╝  ╚██╗ ██╔╝
██║ ╚████║██║  ██║╚██████╔╝   ██║       ██████╔╝███████╗ ╚████╔╝ 
╚═╝  ╚═══╝╚═╝  ╚═╝ ╚═════╝    ╚═╝       ╚═════╝ ╚══════╝  ╚═══╝  
        Copyright © 2024 - 2025 NoobMCGaming
*/   

use jojoe77777\FormAPI\ModalForm;
use pocketmine\{Server, player\Player};
use Noob\Clan;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use Noob\CoinAPI;

class ClanForm {

    public string $prefix = "§l§eＶie§cＤＡＲＫ";

    public function createClan(Player $player){
        $form = new CustomForm(function(Player $player, $data){
            if($data === null){
                return true;
            }
            if(!isset($data[0])){
                $player->sendMessage($this->prefix . "§6Vui Lòng Nhập Tên Clan Là Chữ !");
                return true;
            }
            if(!Clan::getInstance()->isDefaultName($data[0])){
                $player->sendMessage($this->prefix . "§6Vui Lòng Nhập Tên Clan Không Có Màu Và Ngắn Hơn 17 Kí Tự");
                return true;
            }
            if(Clan::getInstance()->hasClanName($data[0])){
                $player->sendMessage($this->prefix . "§6Clan Này Đã Tồn Tại !");
                return true;
            }
            Clan::getInstance()->createClan($player, $data[0]);
            $player->sendMessage($this->prefix . "§6Tạo Clan ". $data[0]." Thành Công !");
        });
        $form->setTitle("§l§3● §2Tạo Clan §3●");
        $form->addInput("§l§c● §eNhập Tên Clan:", "Free Fire");
        $form->sendToPlayer($player);
    }

    public function clanMenu(Player $player){
        $clan = Clan::getInstance()->getClan($player);
        $owner = Clan::getInstance()->getOwnerOfClan($clan);
        $members = Clan::getInstance()->getMembersOfClan($clan);
        $point = Clan::getInstance()->getPointOfClan($clan);
        $count = Clan::getInstance()->getCountMemberOfClan($clan);
        $form = new SimpleForm(function(Player $player, $data) use ($clan){
            if($data === null){
                return true;
            }
            switch($data){
                case 0:
                    if(Clan::getInstance()->isOwnerOfClan($player, $clan)){
                        $this->settingMember($player, $clan);
                    }
                    else{
                        $player->sendMessage($this->prefix . "§6Bạn Cần Là Chủ Clan !");
                    }
                    break;
                case 1:
                    $this->donateClan($player, $clan);
                    break;
                case 2:
                    if(Clan::getInstance()->isOwnerOfClan($player, $clan)){
                        $this->changeOwner($player, $clan);
                    }
                    else{
                        $player->sendMessage($this->prefix . "§6Bạn Cần Là Chủ Clan !");
                    }
                    break;
                case 3:
                    if(!Clan::getInstance()->isOwnerOfClan($player, $clan)){
                        $this->leaveClan($player, $clan);
                    }
                    else{
                        $player->sendMessage($this->prefix . "§6Bạn Cần Là Thành Viên Chứ Không Phải Chủ !");
                    }
                case 4:
                    if(Clan::getInstance()->isOwnerOfClan($player, $clan)){
                        $this->deleteClan($player, $clan);
                    }
                    else{
                        $player->sendMessage($this->prefix . "§6Bạn Cần Là Chủ Clan !");
                    }
                    break;
            }
        });
        $form->setTitle("§l§3● §2Tổ Đội §3●");
        $form->setContent("§l§a● §fChào Mừng Bạn Đến Với ". $clan ."\n\n§l§a● §fChủ Tổ Đội: ". $owner ."\n§l§a● §fThành Viên: ". $members ."\n§l§a● §fĐiểm: ". $point ."\nSố Lượng: ". $count ."/10");
        $form->addButton("§l§3● §2Quản Lí Thành Viên §3●", 1, "https://cdn-icons-png.flaticon.com/128/738/738853.png");
        $form->addButton("§l§3● §2Ủng Hộ Clan §3●", 1, "https://cdn-icons-png.flaticon.com/128/10880/10880476.png");
        $form->addButton("§l§3● §2Chuyển Nhượng Chủ §3●", 1, "https://cdn-icons-png.flaticon.com/128/15362/15362954.png");
        $form->addButton("§l§3● §2Rời Khỏi Clan §3●", 1, "https://cdn-icons-png.flaticon.com/128/3094/3094700.png");
        $form->addButton("§l§3● §2Xóa Clan §3●", 1, "https://cdn-icons-png.flaticon.com/128/484/484560.png");
        $form->sendToPlayer($player);
    }

    public function settingMember(Player $player, string $clanName){
        $form = new CustomForm(function(Player $player, $data) use ($clanName){
            if($data === null){
                return true;
            }
            if(!isset($data[0])){
                $player->sendMessage($this->prefix . "§6Vui Lòng Nhập Tên Người Chơi");
                return true;
            }
            $player2 = Server::getInstance()->getPlayerByPrefix($data[0]);
            if($player2 === null){
                $player->sendMessage($this->prefix . "§6Người Chơi Không Trực Tuyến");
                return true;
            }
            if($data[1] === 0){
                $count = Clan::getInstance()->getCountMemberOfClan($clanName);
                if($count < 10){
                    $this->yesorno($player, $player2, $clanName);
                }
                else{
                    $player->sendMessage($this->prefix . "§6Rất Tiếc Clan Đã Đủ Người !");
                }
            }
            if($data[1] === 1){
                $player2->sendMessage($this->prefix . "§6Rất Tiếc Bạn Đã Bị Đuổi Khỏi Clan !");
                $this->leaveClan($player, $clanName);
            }
        });
        $form->setTitle("§l§3● §2Quản Lý Thành Viên §3●");
        $form->addInput("§l§c● §eNhập Tên Người Chơi:", "...");
        $form->addDropdown("§l§c● §eChọn Việc Làm:", ["Thêm Vào Clan", "Đá Khỏi Clan"], 0);
        $form->sendToPlayer($player);
    }

    public function yesorno(Player $player, Player $player2, string $clanName){
        $form = new ModalForm(function(Player $player, $data) use ($clanName, $player2){
            if($data === null){
                return true;
            }
            if($data === true){
                if($player->isOnline()){
                    $player->sendMessage($this->prefix . "§6Người Chơi ". $player2->getName() . " Đã Chấp Nhận Tham Gia Clan");
                }
                Clan::getInstance()->getPlayerManager()->set($player2->getName(), $clanName);
                Clan::getInstance()->getPlayerManager()->save();
                $player2->sendMessage($this->prefix . "§6Bạn Đã Tham Gia Vào Clan !");
                Clan::getInstance()->updateClan($clanName);
            }
            if($data === false){
                if($player->isOnline()){
                    $player->sendMessage($this->prefix . "§6Người Chơi ". $player2->getName() . " Đã Từ Chối Tham Gia Clan");
                }
                $player2->sendMessage($this->prefix . "§6Bạn Đã Từ Chối Tham Gia Vào Clan !");
            }
        });
        $form->setTitle("§l§3● §2Lời Mời Tham Gia §3●");
        $form->setContent("§l§c● §eBạn Đã Được Mời Tham Gia Vào ". $clanName .", Bạn Có Muốn Tham Gia Không ?");
        $form->setButton1("Tham Gia");
        $form->setButton2("Từ Chối !");
        if($player2->isOnline()) $form->sendToPlayer($player2);
    }

    public function donateClan(Player $player, string $clanName){
        $form = new CustomForm(function(Player $player, $data) use ($clanName){
            if($data === null){
                return true;
            }
            if(!is_numeric($data[0])){
                $player->sendMessage($this->prefix . "§6Vui Lòng Nhập Số Điểm Muốn Ủng Hộ !");
                return true;
            }
            if((int)$data[0] < 1){
                $player->sendMessage($this->prefix . "§6Vui Lòng Nhập Số Điểm Lớn Hơn 0");
                return true;
            }
            if((int)$data[0] > 100){
                $player->sendMessage($this->prefix . "§6Vui Lòng Nhập Số Điểm Nhỏ Hơn 100");
                return true;
            }
            if(CoinAPI::getInstance()->myCoin($player) >= (int)$data[0] * 50000){
                CoinAPI::getInstance()->reduceCoin($player, (int)$data[0] * 50000);
                Clan::getInstance()->donateClan($clanName, (int)$data[0]);
                $player->sendMessage($this->prefix . "§6Bạn Đã Donate Cho Clan ". $data[0]);
            }
            else{
                $player->sendMessage($this->prefix . "§6Bạn Không Đủ Coin !");
                return true;
            }
        });
        $form->setTitle("§l§3● §2Ủng Hộ §3●");
        $form->addInput("§l§c● §eNhập Số Điểm Muốn Donate ( 50k Coin = 1 Điểm )", "10");
        $form->sendToPlayer($player);
    }

    public function changeOwner(Player $player, string $clanName){
        $form = new CustomForm(function(Player $player, $data) use ($clanName){
            if($data === null){
                return true;
            }
            if(!isset($data[0])){
                $player->sendMessage($this->prefix . "§6Vui Lòng Nhập Tên Thành Viên !");
                return true;
            }
            Clan::getInstance()->changeOwner($clanName, $data[0]);
        });
        $form->setTitle("§l§3● §2Chuyển Nhượng §3●");
        $form->addInput("§l§c● §eNhập Tên Thành Viên:", "Ai biết clan bạn có ai mà viết ví dụ");
        $form->sendToPlayer($player);
    }

    public function leaveClan(Player $player, string $clanName){
        Clan::getInstance()->getPlayerManager()->set($player->getName(), "Không Có");
        Clan::getInstance()->getPlayerManager()->save();
        $player->sendMessage($this->prefix . "§6Bạn Đã Rời Khỏi Clan Thành Công");
        Clan::getInstance()->updateClan($clanName);
    }

    public function deleteClan(Player $player, string $clanName){
        Clan::getInstance()->deleteClan($clanName);
        $player->sendMessage($this->prefix . "§6Xóa Clan Thành Công");
    }
}