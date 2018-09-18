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

// Remove second DEFAULT_BOT dir entry
$botdirs = array_unique($botdirs);

// Initialize filenames and foldertype
$filename = '';
$altfilename = '';
$foldertype = '';

// Message.
if (isset($update['message']) && $update['message']['chat']['type'] == 'private') {
    // Set foldertype to commands
    $foldertype = 'commands';

    // Check message text for a leading slash.
    if (substr($update['message']['text'], 0, 1) == '/') {
        // Get command name
        $filename = strtolower(str_replace('/', '', explode(' ', $update['message']['text'])[0]));

        // Check if name of a botdir is inside command
        foreach ($botdirs as $key => $dir) {
            // Filename starting with name of botdir?
            if (substr($filename, 0, strlen($dir)) == $dir) {
                // Set alternative filename, substract botdir name from command
                $altfilename = substr($filename, strlen($dir));
                // Make sure alternative filename exists inside and forward then
                if (is_file(__DIR__ . '/' . $dir . '/' . $foldertype . '/' . $altfilename . '.php')) {
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

// Callback query.
} else if (isset($update['callback_query'])) {
    // Set foldertype
    $foldertype = 'mods';

    // Init empty data array.
    $data = array();

    // Callback data found.
    if ($update['callback_query']['data']) {
        // Split callback data and assign to data array.
        $splitData = explode(':', $update['callback_query']['data']);
        $filename = $splitData[1];
    }

// Location.
} else if (isset($update['message']['location'])) {
    // Forward request to location bot and exit.
    include_once(__DIR__ . '/' . LOCATION_BOT . '/index.php');
    exit();
}

// Check files if filenames and foldertype are set
if (!empty($filename) && !empty($foldertype)) {
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