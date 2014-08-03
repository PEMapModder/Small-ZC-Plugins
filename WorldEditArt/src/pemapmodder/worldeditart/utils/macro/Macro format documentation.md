Macro Format Documentation
===
Macros (`*.mcr`) use the NBT format, prefixed with the compression format constant (or `0x00` if none).

A plain macro file should be like this:

```
00: the compression method (no compression)
0A: compound constant, used to identify that this is start of the main datum
  00 00: the main datum doesn't need a name, so zero length
  macro author -> an NBT string
    08: string constant, used to identify that this is start of a string
    00 06 "author": name of the datum: "author" (0006 characters)
    [actual author string, prefixed with a short of the length]
  macro description -> an NBT string
    08: string constant, used to identify that this is start of a string
    00 0B "description": name of the datum: "description" (000B characters)
    [actual description string, prefixed with a short of the length]
  macro operations -> an NBT enum of compounds
    09: enum constant, used to identify that this is start of an enum
    00 03 "ops": name of the datum: "ops" (0003 characters)
    0A: compound constant, used to identify that this enum is composed of compounds
    [number of operations as a 4-byte unsigned int]
    for each operation ->
      if the operation is waiting ->
        operation type -> an NBT byte
          01: byte constant, used to identfy that this is start of a byte
          00 04 "type": name of the datum: "type" (0004 characters)
          01: identifies that this operation is waiting
        wait time -> an NBT int
          03: int constant, used to identfy that this is start of an int
          00 05 "delta": name of the datum: "datum" (0005 characters)
          [number of ticks to wait as a 4-byte unsigned int]
      if the operation is block setting ->
        operation type -> an NBT byte
          01: byte constant, used to identfy that this is start of a byte
          00 04 "type": name of the datum: "type" (0004 characters)
          00: identifies that this operation is block setting
        delta vectors -> an NBT enum of longs about how far the block setting should be from the anchor
          09: enum constant, used to identify that this is start of an enum
          00 07 "vectors": name of the datum: "vectors" (0007 characters)
          04: long constant, used to identify that this enum is composed of longs
          00 00 00 03: number of vectors (0003 vectors, x, y and z)
          for each x/y/z -> an NBT long
            [number of blocks of the vector from the anchor as a 4-byte signed int]
        block ID -> an NBT byte
          01: byte constant, used to identfy that this is start of a byte
          00 07 "blockID": name of the datum: "blockID" (0007 characters)
          [block ID to set, as a byte]
        operation type -> an NBT byte
          01: byte constant, used to identfy that this is start of a byte
          00 0B "blockDamage": name of the datum: "blockDamage" (000B characters)
          [block damage to set, as a byte]
      00: end of this operation compound
00: end constant, used to identify that this is the end of the main datum
```
