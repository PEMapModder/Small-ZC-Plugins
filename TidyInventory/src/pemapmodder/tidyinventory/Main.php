<?php

namespace pemapmodder\tidyinventory;

use pocketmine\inventory\Inventory;
use pocketmine\block\Block;
use pocketmine\item\Block as BlockItem;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	const INVENTORY_HORIZONTAL_SLOTS_COUNT = 9; // I am not sure about that. TODO check
	const M_REGULAR = 0;
	const M_BUILDING = 1;
	const M_FIGHTING = 2;
	const M_FARMING = 3;
	const M_TRANSPORT = 4;
	const T_WEAPON = 0;
	const T_TOOL = 1;
	const T_FOOD = 2;
	const T_BLOCKS_GENERIC = 3;
	const T_BLOCKS_DECORATIVE = 4;
	const T_BLOCKS_SPECIAL = 5;
	const T_TRANSPORTATION = 6;
	const T_MATERIALS = 7;
	const T_MISCELLANEOUS = 8;
	const T_EMPTY = 9;
	const T_PLANTING = 10;
	const T_UTIL = 11;
	private function tidyInventory(Inventory $inventory, $mode){
		$items = $inventory->getContents();
		// TODO blah blah blah
		switch($mode){
			case self::M_REGULAR:
				break;
			case self::M_BUILDING:
				break;
			case self::M_FIGHTING:
				break;
			case self::M_FARMING:
				break;
			case self::M_TRANSPORT:
				break;
		}
		$inventory->setContents($items);
		$inventory->sendContents($inventory->getViewers());
	}
	private function sortItemType(Item $item){
		if($item->isSword() or $item->getID() === Item::BOW or $item->getID() === Item::FLINT_AND_STEEL){
			return self::T_WEAPON;
		}
		if($item->isTool()){
			return self::T_TOOL;
		}
		if($item instanceof BlockItem){
			switch($item->getID()){
				case Block::AIR:
					return self::T_EMPTY;
				case Block::COBBLE:
				case Block::DIRT:
				case Block::WOODEN_PLANK:
				case Block::SAND:
				case Block::GRAVEL:
				case Block::TORCH:
					return self::T_BLOCKS_GENERIC;
				case Block::STONE:
				case Block::GRASS:
				case Block::WOOD:
				case Block::LEAVE:
				case Block::SPONGE:
				case Block::GLASS:
				case Block::LAPIS_BLOCK:
				case Block::SANDSTONE:
				case 27: // powered rail
				case Block::COBWEB:
				case Block::TALL_GRASS:
				case Block::DEAD_BUSH:
				case Block::WOOL:
				case Block::DANDELION:
				case Block::ROSE:
				case Block::GOLD_BLOCK:
				case Block::IRON_BLOCK:
				case Block::DIAMOND_BLOCK:
				case Block::COAL_BLOCK:
				case Block::EMERALD_BLOCK:
				case Block::DOUBLE_SLAB:
				case Block::SLAB:
				case Block::BRICKS:
				case Block::BOOKSHELF:
				case Block::MOSS_STONE:
				case Block::OBSIDIAN:
				case Block::ACACIA_WOOD_STAIRS:
				case Block::BIRCH_WOOD_STAIRS:
				case Block::BRICK_STAIRS:
				case Block::COBBLE_STAIRS:
				case Block::DARK_OAK_WOOD_STAIRS:
				case Block::JUNGLE_WOOD_STAIRS:
				case Block::NETHER_BRICKS_STAIRS:
				case Block::OAK_WOOD_STAIRS:
				case Block::QUARTZ_STAIRS:
				case Block::SANDSTONE_STAIRS:
				case Block::SPRUCE_WOOD_STAIRS:
				case Block::STONE_BRICK_STAIRS:
				case Block::WOOD_STAIRS:
				case Block::LADDER:
				case Block::WALL_SIGN:
				case Block::IRON_DOOR_BLOCK:
				case Block::SNOW_LAYER:
				case Block::SNOW_BLOCK:
				case Block::CLAY_BLOCK:
				case Block::FENCE:
				case Block::NETHERRACK:
				case Block::GLOWSTONE:
				case Block::JACK_O_LANTERN:
					return self::T_BLOCKS_DECORATIVE;
				case Block::BEDROCK:
				case Block::WATER:
				case Block::STILL_WATER:
				case Block::LAVA:
				case Block::STILL_LAVA:
				case Block::REDSTONE_ORE:
				case Block::EMERALD_ORE:
				case Block::DIAMOND_ORE:
				case Block::GLOWING_REDSTONE_ORE:
				case Block::GOLD_ORE:
				case Block::IRON_ORE:
				case Block::COAL_ORE:
				case Block::LIT_REDSTONE_ORE:
				case Block::LAPIS_ORE:
				case Block::BED_BLOCK:
				case Block::FIRE:
				case Block::WHEAT_BLOCK:
				case Block::FARMLAND:
				case Block::BURNING_FURNACE:
				case Block::SIGN_POST:
				case Block::DOOR_BLOCK:
				case Block::ICE:
				case Block::SUGARCANE_BLOCK:
				case Block::CAKE_BLOCK:
				case 95: // invisible bedrock
					return self::T_BLOCKS_SPECIAL;
				case Block::SAPLING:
				case Block::BROWN_MUSHROOM:
				case Block::RED_MUSHROOM:
				case Block::CACTUS:
				case Block::PUMPKIN:
					return self::T_PLANTING;
				case Block::TNT:
					return self::T_WEAPON;
				case Block::CHEST:
				case Block::CRAFTING_TABLE:
				case Block::FURNACE:
					return self::T_UTIL;
			}
		}
		switch($item->getID()){

		}
	}
}
