AuthTools
===

This plugin includes:
- [ ] `DirectAuth` - the authentication method by typing password into chat directly
  - [ ] kick on consecutive failiures
  - [ ] disallow sending password in chat if he is already authenticated (by `$event->setMessage(""); $event->setCancelled();`)
- [ ] `/changepw` command - the command for changing password
- [ ] `AuthSite` - host website in the same environment that supports auth passwords
