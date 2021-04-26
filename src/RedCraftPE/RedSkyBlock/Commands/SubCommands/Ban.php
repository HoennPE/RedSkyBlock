<?php

namespace RedCraftPE\RedSkyBlock\Commands\SubCommands;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;

use RedCraftPE\RedSkyBlock\SkyBlock;
use RedCraftPE\RedSkyBlock\Commands\Island;

class Ban {

  private static $instance;

  public function __construct() {

    self::$instance = $this;
  }

  public function onBanCommand(CommandSender $sender, array $args): bool {

    if ($sender->hasPermission("skyblock.ban")) {

      if (count($args) < 2) {

        $sender->sendMessage(TextFormat::WHITE . "Usage: /is ban <player>");
        return true;
      } else {

        $senderName = strtolower($sender->getName());
        $skyblockArray = SkyBlock::getInstance()->skyblock->get("SkyBlock", []);
        $player = SkyBlock::getInstance()->getServer()->getPlayerExact(implode(" ", array_slice($args, 1)));
        if (!$player) {

          $sender->sendMessage(TextFormat::WHITE . implode(" ", array_slice($args, 1)) . TextFormat::RED . " does not exist or is not online.");
          return true;
        } else {

          if ($player->getName() === $sender->getName()) {

            $sender->sendMessage(TextFormat::RED . "You cannot ban yourself from your own island");
            return true;
          }
          if (array_key_exists($senderName, $skyblockArray)) {

            if (in_array($player->getName(), $skyblockArray[$senderName]["Banned"])) {

              $sender->sendMessage(TextFormat::WHITE . "{$player->getName()}" . TextFormat::RED . " is already banned from your island.");
              return true;
            } else {

              $skyblockArray[$senderName]["Banned"][] = $player->getName();
              if (in_array($player->getName(), $skyblockArray[$senderName]["Members"])) {

                unset($skyblockArray[$senderName]["Members"][array_search($player->getName(), $skyblockArray[$senderName]["Members"])]);
              }
              SkyBlock::getInstance()->skyblock->set("SkyBlock", $skyblockArray);
              SkyBlock::getInstance()->skyblock->save();
              $sender->sendMessage(TextFormat::WHITE . $player->getName() . TextFormat::GREEN . " is now banned from your island.");
              $player->sendMessage(TextFormat::WHITE . $sender->getName() . TextFormat::RED . " has banned you from their island.");
              return true;
            }
          } else {

            $sender->sendMessage(TextFormat::RED . "You do not have an island yet.");
            return true;
          }
        }
      }
    } else {

      $sender->sendMessage(TextFormat::RED . "You do not have the proper permissions to run this command.");
      return true;
    }
  }
}
