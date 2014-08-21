WorldEditArt Desc & Doc
===
Welcome to the D&D of WorldEditArt (WEA). The following index may help you getting to the section you want immediately.

__Index__

1. [Player's Guide](#players_guide)
1. [Owner's Guide](#owners_guide)
1. [Developer's Guide](#developers_guide)

===

# Players Guide

## Concepts
Before we go on, there are several exclusive concepts and terms in WorldEditArt that you may never have heard about. This section will also cover concepts in the MCPC WorldEdit plugin that weren't supported in @shoghicp's WorldEditor plugin (and might be slightly different from the MCPC version too). This section will also cover concepts always present in world edit plugins to reinforce/confirm them.

### Anchors
Basically, an anchor is the position you selected. Using anchors allows you to be able to run commands as if you are at that position without actually going there. At least, you don't need to go there _again_ and to and fro.

You can select your anchor using the command `//anchor`. Details will be explained in the [commands](#commands) section.

### Macros
Macros are an exclusive feature in WorldEditArt. Basically, a macro is like a robot that watches what blocks you wait and do it again somewhere else when you want it to.

In Minecraft, building gets easier when a plugin does what you have done automatically again at another place. You can start recording a macro using the subcomand `//macro start`. Optional parameters like using anchors as the reference point (the point where the robot stands) can be found at that section.

After starting recording a macro, every block place/break is recorded. However, currently, block touches that have specific actions/consequences (such as wand selection, rejected by another plugin, etc.), as long as cancelled, will not be recorded by the macro. You can contact the possible related plugin developer whether their plugins cancel the event if you have any inquiries.

Sometimes, you may want to tell the robot to wait before continuing the next actions (otherwise, the robot will repeat all your actions without waiting). Maybe you want that so that the water has enough time to flow? You can do that by `//macro wait`. Again, details at that section.

If you have decided not to save the macro (maybe because it is a bad one?), run the command `//macro ng` to terminate the recording. If you want the macro to pause recording until you tell it to continue again, run `//macro pause`. Run `//macro resume` to resume. If you have completed the macro recording and want to end+save it, run `//macro save <name>`.

To run a macro, select your anchor or your current position as the reference point (details at that section, again) and use the `//macro run` command.

You may want to ask: why do I need that while I have copy&paste? The answer is, with macros, you can copy&paste the most irregular shape you want. In future updates, there may also be a multi-macro-database system to pick a macro from a remote database, so maybe multiple servers can run macros from a public MySQL database without permission to write to it.

### Selections
Different from conventional selections of other world editor plugins, WEA selections are not limited to cuboids.

Currently, there are three types of selections, as seen [here](https://github.com/PEMapModder/Small-ZC-Plugins/tree/master/WorldEditArt/src/pemapmodder/worldeditart/utils/spaces) (the `*****Space.php` files).
* For cuboids, you can select them using the command `//cuboid`, or using `//pos1` and `//pos2`. Future updates may allow direct selection of the cuboid using tools.
* For cylinders, you can select them using the command `//cylinder`.
* For spheres, you can select them using the command `//sphere`.

All selections support these operations:
* Set all blocks to a [block pattern](block-patterns)
* Replace specified type(s) of the blocks inside into a [block pattern](block-patterns)
* Set the marginary blocks to a [block pattern](block-patterns) (a.k.a. hollow setting)
* Replace specified type(s) of the marginary blocks into a [block pattern](block-patterns)

Note that you can only have one selection of whatever shape at a time. In later updates, you may be able to have multiple (optionally) named selections.

### Block Patterns
A block pattern is a list of blocks separated by commas. You can always refer to a block type using the following method:

```
[block name or block ID, spaces removed or replaced by underscores (_)](:[damage] (default to 0))
```

Note that the default is zero, not any. `id:*`, however, may be supported in later versions.

If you want to make it a weighted list such that certain blocks are more likely to be chosen, specify its percentage in front of the block. E.g. `50%glass,50%wood`



## Commands
Most obviously, the thing everyone asks for.

### Command Prefix
WEA commands can start with `/worldeditart `, `/wea `, `// ` or `//`. For example, the `set` command can be sent by `/worldeditart set`, `/wea set`, `// set` or `//set`.

### Help List
The help list command is special and different from normal help commands. It is dynamic, and it only shows the commands you can run at the moment. For example, it doesn't show players-only commands on console, nor does it show commands that require you to have a selection when you don't have one. **Don't report issues saying that your help page doesn't show some commands.**

### Table of Commands
The following table of commands specifies the commands, the link to the description and the permission nodes and requirements.

| Command | Description | Requirements to run the command |
| :---: | :---: | :--- |
| `//help` | [Help List](#help-list) | Has the permission `wea.cmd` |
