NailedKeyboard
===
This is an ease-of-access plugin that allows players who cannot type chat due to their keyboard issues to use an in-game virtual keyboard.

### How to use
Players can tap signs instead of keyboard keys to send chat.

To make a sign a NailedKeyboard sign, the first line of the sign must be exactly "NailedKeyboard". The third line of the sign is the type of the sign (what type of things the sign will do), the second line is the arguments (detailed things that control what the sign does) and the fourth line is ignored (it can be anything).

There are several sign types. Refer to the following table.

| Sign type (third line) | Arguments (second line) | Description |
| :--: | :--: | :--- |
| HOME | ignored (anything) | The pointer will go to the start of the text, just like you tap the "home" button in your computer's keyboard |
| END | ignored (annything) | The pointer will go to the end of the text, just like you tap the "end" button in your computer's keyboard |
| LEFT | ignored (anything) | The pointer will go 1 character left, just like you tap the left button in your computer's keyboard |
| RIGHT | ignored (anything) | The pointer will go 1 character right, just like you tap the left button in your computer's keyboard |
| RESET | ignored (anything) | Delete the whole text, like you just logged in and typed nothing |
| SUBMIT or SEND or ENTER | ignored (anything) | Send the text out as chat, or send a command if it starts with a slash (/) |
| BACKSPACE | ignored (anything) | Delete the character before the pointer, like the normal backspace button |
| DELETE | ignored (anything) | Delete the character before the pointer, like the normal delete button |
| VIEW | ignored (anything) | Simply view what your line is and where your pointer is at |
| anything else or empty | the text to type | Insert the words in the second line to the place at the pointer

Note that the third line is case-insensitive, which means you can make the words all in caps or otherwise.

===

Also, Unicode (chinese, japanese, arabian, etc. non-ASCII characters) is only supported on servers with the multibyte extension installed. If it isn't, it should show a warning when the server starts. Then there might be some bugs when the player clicks the left/right buttons.
