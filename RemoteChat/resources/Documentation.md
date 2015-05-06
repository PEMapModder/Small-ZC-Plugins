RemoteChat Doucmentation
===
RemoteChat is a PocketMine-MP plugin developed by PEMapModder to faciliate chat between different servers.

Protocol Doucmentation
===
In the query supported by PocketMine, RemoteChat injects an extra data field called `pm_remotechat` into the response (long query only). It is a human-readable string representing the port where the listener is hosted on.

The listener opens a TCP server on the config-dependent port (default `44746`) to accept connections.

Upon client socket opened, the sender server should send a line (terminated by `\r\n`) about the information of this connection:

```
REMOTECHAT <version> <action> <hostname> <reply port>
```

The current version is `0`. The hostname can be an IP or a hostname, such as `127.0.0.1` or `example.com`.

Currently there is only one action: `PRIVMSG` - send a private message to a player on the target server.

The following lines (each terminated by a `\r\n` line break) are the action-specific arguments for the action.

For `PRIVMSG`:
* replyTo: when replying to this request, PRIVMSG to this recipient., max 255 characters
* recipient: the recipient of the request, max 255 characters
* message: the actual message to send, max 16383 characters
