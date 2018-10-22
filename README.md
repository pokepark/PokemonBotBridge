# PokemonBotBridge

BotBridge to link several PokemonBots under one Telegram chat contact. Developers are welcome to join https://t.me/PokemonBotSupport

# About

The PokemonBotBridge builds a passive bridge between several PokemonRaidBots and PokemonQuestBots. Therefore setup the PokemonBotBridge as mentioned below. 

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

# Bot installation

For each bot under the bridge create a subfolder and set up that bot in the subfolder as their README says, except for the steps with the Telegram BotFather, the `CONFIG_HASH`'s SHA512 value and the webhook! Skip those part when setting up the bots itself!

Due to technical limitations and also for usability reasons please keep the names of the subfolders for each bot as short as possible. If the foldername exceeds a length of 8 characters, a hint will be logged in the logfile of the respective bot and no response will be given until the length issue is fixed!

#### Folder structure example

A little example how the folder structure with the PokemonBotBridge should look like. We are currently in the BotBridge directory:

    .                             # PokemonBotBridge folder
    ├── config.php                # BotBridge config
    ├── config.php.example        # BotBridge example config
    ├── index.php                 # BotBridge index.php
    ├── ...
    ├── 5ave                      # 5th Avenue bot folder
    │   ├── config.php            # 5th Avenue config
    │   ├── index.php             # 5th Avenue index.php
    │   └── ...
    ├── cpark                     # Central Park bot folder
    │   ├── config.php            # Central Park config
    │   ├── index.php             # Central Park index.php
    │   └── ...
    ├── jersey                    # Jersey City bot folder
    │   ├── config.php            # Jersey City config
    │   ├── index.php             # Jersey City index.php
    │   └── ...
    ├── wall                      # Wallstrett bot folder
    │   ├── config.php            # Wallstrett config
    │   ├── index.php             # Wallstrett index.php
    │   └── ...
    └── ...

# Bots configuration

All the bots in the subfolder share the same webhook which will be set in the next step.

Use https://www.miniwebtool.com/sha512-hash-generator/ to create the hashed value of the bot token (preferably lowercase).

Edit then each bots config file and set the `CONFIG_HASH` to the hashed bot token and `BRIDGE_MODE` to `true` - that's it!

# Webhook

Set Telegram webhook via one of the bots webhook.html file, e.g. https://your-hostname/bridge/botdir/webhook.html

Make sure to point the webhook to the bridge and NOT to the bot itself in the subfolder! This is important as the PokemonBotBridge will forward each request to the corresponding bot in the right subfolder.

# Usage

Each bot is now accessible via its foldername plus the actual command.

We created the folders for several PokemonRaidBots for Wallstreet (wallst), Jersey City (jersey), 5th Avenue (5ave) and Central Park (cpark) area and set the `DEFAULT_BOT` to `5ave` and the `LOCATION_BOT` to `cpark`:
- `wall`
- `jersey`
- `5ave`
- `cpark`

To create a raid in the 5th Avenue area, we simply can use the `/start` command as we defined that bot as the default bot. Alternatively the folder name `5ave` plus the command `start`, so `/5avestart`, to start the raid creation works too. If someone shares a location now with the bot, the Central Park area bot will answer since we set that bot to be the `LOCATION_BOT`.

To create a raid in the Wallstreet district, the name of the subfolder from the bot `wall` plus the commandname `start` simply need to be combined to `/wallstart` to start the raid creation. Same for the Jersey City area - folder name plus command name: `/jerseystart`

For every other command the same scheme applies: bot directory name plus command name

For example to use the delete command for the Central Park area bot, you easily send `/cparkdelete` to the bot.

Callback queries are altered automatically once the `BRIDGE_MODE` is set to `true` in each bots config file, so you don't need to care about that. All you need to know is the directory name of the bot and the command you'd like to sent to the bot.

That's it - enjoy the advantages of the PokemonBotBridge!
