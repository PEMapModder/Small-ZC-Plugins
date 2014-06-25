WorldEditArt
===
WorldEditArt is a professional world editor that is very convenient for building. It includes the following features:

## Convenient coordinate selection
You can define your own wand using `/wea wand <item>`, set to the current held item using `/wea wand hand` or check it via `/wea wand`. If you touch a block with a wand, your selected position will be the touched block. You can also select your current standing/flying position by `/wea sel here`.

##Cuboid/Cylinder/Sphere selection, replacing, editing and copying
A cuboid can be selected by two selected points. Select the first point, run `/wea sel 1`, select the second point, run `/wea sel 2`, then you have the cuboid block selected. You can change any of the points by selecting it again. You can select the second point first; it doesn't matter. It can also be selected by a command. First, select the starting point of the cuboid. Then run the command `/wea sel cub <diagonal>`. A line will grow for <diagonal> blocks towards the direction you are looking at, and the smallest right cuboid that can fit the line will be selected.

A cylinder needs to be selected by a command. First, select the base of the cylinder. Then, run `/wea sel cyl <height> <radius>` to select it. The cylinder will grow for <height> blocks towards the linear direction you are looking at (west, east, north, south, directly downwards and directly upwards) with a base radius of <radius> blocks.

A sphere also needs to be selected by a command. First, select the centre point of the sphere. Then, run `/wea sel sph <radius>` to select it. A sphere centered at your selected point with <radius> blocks of radius will be selected.

If you want to test how your selection is, use `/wea sel test <radius>`

## Macros
If you have not heard of macros, here it is.

A macro is like a robot that watches what you have done and do that again for you when you ask it to. When you start recording a macro, all blocks that you place, relative to your _anchor_, will be recorded. When you run the macro at another anchor, it can repeat all you have done before, relative to the new anchor. For example, if you place a block above your anchor, when you run the macro at another anchor, the same block will be placed above the anchor. Therefore, it is useful for running spleef manually, for example.

With this plugin, if you want to use macros, first you have to set an anchor by `/wea macro anchor` after a selecting a block. If you want your current location to be the anchor instead, use `/wea macro anchor me`. Then, you can start recording the macro using `/wea macro start <name>`. Stop and save the macro using `/wea wmacro stop`. The macro will be saved into plugins/WorldEditart/macros/<name>.mcr in a compact format. (which means you can read it or edit it without a hex editor unless you are an ASCII expert) If the macro already exists or another operator is recording it when you start, you will be required to choose another name. Later, (optionally after choosing another anchor) you can use `/wea macro run <name>` to run the macro again. Note that "later" refers to any time after the macro has been saved, which includes after a server restart.

Documentation
===
## .mcr File Format
.mcr is not to be confused with MCPC beta saving extension.

Macros are saved as **/plugins/WorldEditArt/macros/<macro name>.mcr**. The following is a documentation of these files:
```
long The number of block places/breaks in this macro
for each block place/break ->
    byte Type of action: 0 for place, 1 for break
    int The target block's x delta from the anchor
    short The target block's y delta from the anchor
    int The target block's z delta from the anchor
```
