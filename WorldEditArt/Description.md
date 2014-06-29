WorldEditArt
===
WorldEditArt is a professional world editor that is very convenient for building. It includes the following features:

## Convenient coordinate selection
You can define your own wand using `/wea wand <item>`, set to the current held item using `/wea wand hand` or check it via `/wea wand`. If you touch a block with a wand, your selected position will be the touched block. You can also select your current standing/flying position by `/wea sel here`.

##Cuboid/Cylinder/Sphere selection, replacing, editing and copying
A cuboid can be selected by two selected points. Select the first point, run `/wea sel 1`, select the second point, run `/wea sel 2`, then you have the cuboid block selected. You can change any of the points by selecting it again. You can select the second point first; it doesn't matter. It can also be selected by a command. First, select the starting point of the cuboid. Then run the command `/wea sel cub <diagonal>`. A line will grow for <diagonal> blocks towards the direction you are looking at, and the smallest right cuboid that can fit the line will be selected.

A cylinder needs to be selected by a command. First, select the base of the cylinder. Then, run `/wea sel cyl <height> <radius>` to select it. The cylinder will grow for <height> blocks towards the linear direction you are looking at (west, east, north, south, directly downwards and directly upwards) with a base radius of <radius> blocks.

A sphere also needs to be selected by a command. First, select the centre point of the sphere. Then, run `/wea sel sph <radius>` to select it. A sphere centered at your selected point with <radius> blocks of radius will be selected.

If you want to test how your selection is, use `/wea test <block>`

### Copying/cutting selections
After selecting a space, you may want to copy it or move it to another place. This is when you want to use the clipboard. After making a space selection, run the command `/wea copy` or `/wea cut`. Your clipboard, which will be reset every time you rejoin, will contain a copy of the current blocks at that space. Then you can do anything to that area, but these actions won't affect your clipboard. Select a point and run the command `/wea paste` to paste the copied content.

If you want to copy with the anchor at a specific point, use `/wea cut|copy -a` instead. It will copy with the anchor at your selected point instead of your current location.

### Global clipboard
Global clipboards are saved after the server restarts, and can be used by all users with the permission to run the paste command.

Global clipboards can be copied to as the same way as normal clipboards except that you have to put `-g <name>` at the end of command. Examples:

```
/wea copy -g mycopy
/wea cut -a -g copy2
/wea copy -g -a pasteII
```

To paste them, use `/wea paste -g <name>`.

## Macros
If you have not heard of macros, here it is.

A macro is like a robot that watches what you have done and do that again for you when you ask it to. When you start recording a macro, all blocks that you place, relative to your _anchor_, will be recorded. When you run the macro at another anchor, it can repeat all you have done before, relative to the new anchor. For example, if you place a block above your anchor, when you run the macro at another anchor, the same block will be placed above the anchor. Therefore, it is useful for running spleef manually, for example.

With this plugin, if you want to use macros, first you have to set an anchor by `/wea anchor` after a selecting a block. If you want your current location to be the anchor instead, use `/wea anchor me`. Then, you can start recording the macro using `/wea macro start <name>`. Stop and save the macro using `/wea macro stop`. The macro will be saved into plugins/WorldEditArt/macros/<name>.mcr in a compact binary format. (which means you cannot read it or edit it without a hex editor unless you are an ASCII expert) If the macro already exists or another operator is recording it when you start, you will be required to choose another name. Later, (optionally after choosing another anchor) you can use `/wea macro run <name>` to run the macro again. Note that "later" refers to any time after the macro has been saved, which includes after a server restart.

### Why would I want to use macros?
Sometimes, you don't always want to copy full selections. You may just want to copy a few blocks, but these blocks are not together. For example, you built a Herobrine statue. You want to copy another next to it, but if you use the cuboid clipboard, the clipboard will also copy some blocks at the ceiling. This is when you want to use macros, where only the blocks you build (the blocks recorded in the macro) will be copied.

Documentation
===
## .mcr File Format
.mcr is not to be confused with MCPC beta saving extension.

Macros are saved as **/plugins/WorldEditArt/macros/<macro name>.mcr**. They are saved in the GZIP compression format. The following is a documentation of the decompressed version of these files:
```
byte Length of the author's name
string The author's name
long The number of block places/breaks in this macro
-> for each block place/break:
    byte Block ID
    byte Placed block damage
    long The target block's x delta from the anchor
    short The target block's y delta from the anchor
    long The target block's z delta from the anchor
```

## Global Clipboard Clip File Format
Saved in the GZIP format, the decompressed version is like this:

```
byte Length of the author name
string The author's name
long the number of boards in this clip
-> for each copied block:
    long X
    short Y
    long Z
    byte Block ID
    byte Block damage
```
