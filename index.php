<?php 
// Config.
require_once(__DIR__ . '/config.php');

// Tell telegram 'OK'
http_response_code(200);

// Get content from POST data.
$content = file_get_contents('php://input');
    
// Decode the json string.
$update = json_decode($content, true);

// Get bot directories
$botdirs = str_replace(__DIR__ . '/','',glob(__DIR__ . '/*', GLOB_ONLYDIR));

// Add DEFAULT_BOT dir as first entry
array_unshift($botdirs, DEFAULT_BOT);

// Remove EXCLUDE_DIRS from bot dirs
if(defined('EXCLUDE_DIRS') && !empty(EXCLUDE_DIRS)) {
    $excludedirs = explode(',', EXCLUDE_DIRS);
    // Remove dir
    foreach($excludedirs as $exdir) {
        if(($key = array_search($exdir, $botdirs)) !== false) {
            unset($botdirs[$key]);
        }
    }
}

// Remove second DEFAULT_BOT dir entry
$botdirs = array_unique($botdirs);

// Reset keys of bot dirs
$botdirs = array_values($botdirs);

// Count bot dirs
$botdirs_count = count($botdirs);

// Initialize filenames and foldertype
$filename = '';
$altfilename = '';
$foldertype = '';

// Callback query.
if (isset($update['callback_query'])) {
    // Set foldertype
    $foldertype = 'mods';

    // Init empty data array.
    $data = []; 

    // Callback data found.
    if ($update['callback_query']['data']) {
        // Split bot folder name away from actual data.
        $botnameData = explode(':', $update['callback_query']['data'], 2);
        $botname = $botnameData[0];
        $thedata = $botnameData[1];

        // Split callback data and assign to data array.
        $splitData = explode(':', $thedata);
        $filename = $splitData[1];
    }
        
    // Check if filename exists
    if(is_file(__DIR__ . '/' . $botname . '/' . $foldertype . '/' . $filename . '.php')) {
        include_once(__DIR__ . '/' . $botname . '/index.php');
        exit();
    }

// Location.
} else if (isset($update['message']['location'])) {
    // Forward request to location bot and exit.
    include_once(__DIR__ . '/' . LOCATION_BOT . '/index.php');
    exit();

// Message.
} else if (isset($update['message']) && $update['message']['chat']['type'] == 'private') {
    // Set foldertype to commands
    $foldertype = 'commands';

    // Check message text for a leading slash.
    if (substr($update['message']['text'], 0, 1) == '/') {
        // Get command name
        $filename = strtolower(str_replace('/', '', explode(' ', $update['message']['text'])[0]));

        // Check if name of a botdir is inside command
        foreach ($botdirs as $key => $dir) {
            // Filename starting with name of botdir?
            if (substr($filename, 0, strlen($dir)) === $dir) {
                // Set alternative filename, substract botdir name from command
                $altfilename = substr($filename, strlen($dir));
                // Make sure alternative filename exists inside and forward then
                if (is_file(__DIR__ . '/' . $dir . '/' . $foldertype . '/' . $altfilename . '.php')) {
                    include_once(__DIR__ . '/' . $dir . '/index.php');
                    exit();
                // Check if a core command was requested and forward then
                } else if (is_file(__DIR__ . '/' . $dir . '/core/' . $foldertype . '/' . $altfilename . '.php')) {
                    include_once(__DIR__ . '/' . $dir . '/index.php');
                    exit();
                } 
            }
        }
        // If filename is equal to a botdir, forward to that bot
        if (in_array($filename, $botdirs)) {
            include_once(__DIR__ . '/' . $filename . '/index.php');
            exit();
        }
    }

// Inline query.
} else if (isset($update['inline_query'])) {
    $count = substr_count($update['inline_query']['query'], ':');
    if (substr_count($update['inline_query']['query'], ':') == 1) {
        // Split bot folder name away from actual data.
        $botnameData = explode(':', $update['inline_query']['query'], 2);
        $botname = $botnameData[0];
        $thedata = '';
        // Do we have any data yet?
        if(strlen(explode(':', $update['inline_query']['query'])[1]) != 0) {
            $thedata = $botnameData[1];
        }
    } else {
        $botname = DEFAULT_BOT;
        $thedata = '';
    }
        
    // Check if filename exists
    if(is_file(__DIR__ . '/' . $botname . '/index.php')) {
        include_once(__DIR__ . '/' . $botname . '/index.php');
        exit();
    }

// Channel post / Supergroup message.
} else if ((isset($update['channel_post']['text']) && $update['channel_post']['chat']['type'] == "channel") || (isset($update['message']['text']) && $update['message']['chat']['type'] == "supergroup")) {
    // Get Bot_ID 
    $bot_id = '0';
    if(isset($update['channel_post']['text'])) {
        $id_pos = strrpos($update['channel_post']['text'], '-ID = ');
        $bot_id = ($id_pos === false) ? ('0') : (substr($update['channel_post']['text'], ($id_pos - 1), 1));
        $bot_id = strtoupper($bot_id);
    } else if ($update['message']['chat']['type'] == "supergroup") {
        $id_pos = strrpos($update['message']['text'], '-ID = ');
        $bot_id = ($id_pos === false) ? ('0') : (substr($update['message']['text'], ($id_pos - 1), 1));
        $bot_id = strtoupper($bot_id);
    }

    // Make sure bot_id was received.
    if($bot_id != '0') {
        // Search BOT_ID in config files.
        $search = 'BOT_ID';
        // Go thru every bots' config.
        foreach ($botdirs as $key => $dir) {
            // Make sure config file exists.
            if(is_file(__DIR__ . '/' . $dir . '/config.php')) {
                // Read config file.
                $lines = file(__DIR__ . '/' . $dir . '/config.php');
                foreach($lines as $line) {
                    // Check if the line contains the search term.
                    if(strpos($line, $search) !== false) { 
                        // Get BOT_ID via string manipulation.
                        // Example: $line = define('BOT_ID','A');
                        // explode(',', $line, 2)[1]  will split at , into 2 pieces to get you: 'A');
                        // explode("'", INNER-EXPLODE)[1]  will split at ' and so you get the ID: A
                        // strtoupper will make sure we compare uppercase to uppercase
                        // substr will get only the first character as it's in the bots handled too.
                        $config_bot_id = explode("'", explode(',', $line, 2)[1])[1];
                        $config_bot_id = substr(strtoupper($config_bot_id), 0, 1);

                        // Compare bot_id and config_bot_id.
                        if($bot_id === $config_bot_id) {
                            // Check if filename exists
                            if(is_file(__DIR__ . '/' . $dir . '/index.php')) {
                                include_once(__DIR__ . '/' . $dir . '/index.php');
                                exit();
                            }
                        }
                    }
                }
            }
        }
    }
}

// Check files if filenames and foldertype are set
// Compare count of subfolders, as we can only search for filename if we have 2 folders or less
// First the default bot folder and then the other bot folder will be checked this way
// If we have more than 2 folders, searching for filename won't work and therefore be skipped
if ($botdirs_count <= 2 && !empty($filename) && !empty($foldertype)) {
    // Check if file exists inside any of the botdirs
    foreach ($botdirs as $key => $dir) {
        // Check if filename exists
        if(is_file(__DIR__ . '/' . $dir . '/' . $foldertype . '/' . $filename . '.php')) {
            include_once(__DIR__ . '/' . $dir . '/index.php');
            exit();
        }
    }
}

// Fallback - Forward request to default bot and exit.
include_once(__DIR__ . '/' . DEFAULT_BOT . '/index.php');
exit();
