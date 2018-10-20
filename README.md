# PokemonBotBridge

BotBridge to link several PokemonBots under one Telegram chat contact. Developers are welcome to join https://t.me/PokemonBotSupport

# About

The PokemonBotBridge builds a passive bridge between several PokemonRaidBots and PokemonQuestBots. Therefore setup the PokemonBotBridge as mentioned below. For each bot then, create a subfolder and set up that bot in the subfolder as their README says, except for the bot creation via the Telegram BotFather, the `CONFIG_HASH`'s SHA512 value and the webhook - skip those part when setting up the bots itself!

# Git clone

`git clone https://github.com/florianbecker/PokemonBotBridge.git`

# Config

Copy config.php.example to config.php and edit (values explained further).

Set `DEFAULT_BOT` to the bot which shall receive the command or data when no other suitable bot was specified in the request.

Set `LOCATION_BOT` to the bot which shall receive messages with a shared location send to the bot / bot bridge.

# Bot token

Start chat with https://t.me/BotFather and create one bot token.

Bot Settings:

 - Enable Inline mode
 - Allow Groups
   - Group Privacy off

# Webhook

Set Telegram webhook via one of the bots webhook.html file, e.g. https://your-hostname/bridge/botdir/webhook.html

Make sure to point the webhook to the bridge and NOT to the bot itself in the subfolder! This is important as the PokemonBotBridge will forward each request to the corresponding bot in the right subfolder!

# Bots configuration

All the bots in the subfolder share the same webhook.

Use https://www.miniwebtool.com/sha512-hash-generator/ to create the hashed value of the bot token (preferably lowercase).

Edit then each bots config file and insert set the `CONFIG_HASH` to the hashed bot token and `BRIDGE_MODE` to `true`.

The bots configuration is now done.

# Usage

Each bot is now accessible its foldername plus the actual command.

Imagine we created the following folders for several PokemonRaidBots and set `DEFAULT_BOT` to `5ave`:
- `wall`
- `jersey`
- `5ave`
- `cpark`

To create a raid now in the 5th Avenue area, we simply can use the `/start` command as we defined that bot as the default bot.

If one now likes to create a raid in the Wallstreet district, the name of the subfolder from the bot `wall` plus the commandname `start` needs to be combined to `/wallstart` to start the raid creation.



