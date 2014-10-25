NailedKeyboard
===
This is an ease-of-access plugin that allows players who cannot type chat due to their keyboard issues to use **an in-game virtual keyboard**.

## How to use
Players can tap signs instead of keyboard keys to send chat.

To make a sign a NailedKeyboard sign, **the first line of the sign must be exactly `NailedKeyboard`**. The **third line** of the sign is the **type of the sign** (what type of things the sign will do), the **second line** is the **arguments** (detailed things that control what the sign does) and the fourth line is ignored (it can be anything).

There are several sign types. Refer to the following table.

| Sign type (third line) | Arguments (second line) | Description |
| :--: | :--: | :--- |
| HOME | ignored (anything) | The pointer will go to the start of the text, just like you tap the `Home` button in your computer's keyboard |
| END | ignored (annything) | The pointer will go to the end of the text, just like you tap the `End` button in your computer's keyboard |
| LEFT | ignored (anything) | The pointer will go 1 character left, just like you tap the left button in your computer's keyboard |
| RIGHT | ignored (anything) | The pointer will go 1 character right, just like you tap the left button in your computer's keyboard |
| RESET | ignored (anything) | Delete the whole text, like you just logged in and typed nothing |
| SUBMIT or SEND or ENTER | ignored (anything) | Send the text out as chat, or run a command if it starts with a slash (`/`) (as if you pressed the `Enter` key |
| BACKSPACE | ignored (anything) | Delete the character before the pointer, like the normal `Backspace` button |
| DELETE | ignored (anything) | Delete the character before the pointer, like the normal `Delete` button |
| VIEW | ignored (anything) | Simply view what your line is and where your pointer is at |
| anything else or empty | the text to type | Insert the words in the second line to the place at the pointer

Note that the third line is case-insensitive, which means you can make the words all in caps or otherwise.

===

Also, Unicode (chinese, japanese, arabian, etc. non-ASCII characters) is only supported on servers with the _multibyte_ PHP extension installed. If it isn't, it should show a warning when the server starts. Then there might be some bugs when the player clicks the left/right buttons.

##Reporting bugs
Please report bugs [here](https://github.com/PEMapModder/Small-ZC-Plugins/issues)!

Rules for reporting bugs:
* Confirm that issues are not to be duplicated before creating one.
* Do not make custom tags except [RepoName]. For other tags, I will add them via [labels](https://github.com/PEMapModder/Small-ZC-Plugins/labels) myself
* Do not make generic titles like "Help", "Question", etc.
* Paste crash dumps in pastebins like http://pastebin.com
* Try to confirm that the issue is related to this plugin.
* If possible, find out the crash dump on http://crash.pocketmine.net
* Only use the issues to report bugs or request features. Support requests should be done on the PocketMine forums plugin release thread or contacting me via PM on PocketMine forums, or finding me (@PEMapModder) on Twitter or asking on freenode IRC channel `#pmplugins`.
* Please provide a professional report for bugs. By "professional", I mean:
  * The bug should be **reproducible**.
  * The title should be as brief (short and clean but specific) as possible. Details should be provided in the issue body.
* For feature requests,
  * The feature must not be duplicated.
  * Confirm, if possible, that the feature is possible.
* Do not use the issue conversation as a forum.
* If you offend these rules, your issue may be edited, closed, locked, or even your account may be blocked from interacting with the repositories of @PEMapModder.

Screenshots
===
* ![](https://github.com/PEMapModder/Small-ZC-Plugins/raw/master/NailedKeyboard/bin/demo-1.png)
* ![](https://github.com/PEMapModder/Small-ZC-Plugins/raw/master/NailedKeyboard/bin/demo-2.png)
* ![](https://github.com/PEMapModder/Small-ZC-Plugins/raw/master/NailedKeyboard/bin/full-signs-2.png)
* ![](https://github.com/PEMapModder/Small-ZC-Plugins/raw/master/NailedKeyboard/bin/signs-left.png)
* ![](https://github.com/PEMapModder/Small-ZC-Plugins/raw/master/NailedKeyboard/bin/full-signs-1.png)
