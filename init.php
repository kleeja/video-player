<?php
// Kleeja Plugin
// video_player
// Version: 2.0
// Developer: Kleeja team

// Prevent illegal run
if (!defined('IN_PLUGINS_SYSTEM')) {
    exit();
}

// the plugin can be loaded twice in one request, so its constants are defined once
defined('VIDEO_PLAYER_VERSION') || define('VIDEO_PLAYER_VERSION', '2.1');

// Video.js 10 from jsDelivr, all its files from one version: videojs.org/docs/framework/html/guides/cdn
defined('VIDEO_PLAYER_CDN') || define('VIDEO_PLAYER_CDN', 'https://cdn.jsdelivr.net/npm/@videojs/cdn@10.0.1/');

// the chunk that registers <media-i18n>, the element that translates the player to the language of the page;
// video.js imports it, audio.js doesn't, so the audio page loads it itself.
// Its name changes with every version of Video.js: take the new one from the first import of video.js
defined('VIDEO_PLAYER_CDN_I18N') || define('VIDEO_PLAYER_CDN_I18N', 'i18n-BfZDJOyu.js');

// the extensions that the player plays, with their media type, which also tells the video player from the audio one
// browsers play them in their own <video> and <audio>, but some containers aren't played everywhere
// (Safari doesn't play MKV, only Firefox still plays Theora OGV), so assets/player.js hides the player
// when the browser can't play the file, and the visitor still has the download button
defined('VIDEO_PLAYER_TYPES') ||
    define('VIDEO_PLAYER_TYPES', [
        'mp4' => 'video/mp4',
        'm4v' => 'video/mp4',
        'webm' => 'video/webm',
        'mov' => 'video/quicktime',
        'mkv' => 'video/matroska',
        'ogv' => 'video/ogg',
        '3gp' => 'video/3gpp',
        'mp3' => 'audio/mpeg',
        'm4a' => 'audio/mp4',
        'm4b' => 'audio/mp4',
        'aac' => 'audio/aac',
        'wav' => 'audio/wav',
        'flac' => 'audio/flac',
        'ogg' => 'audio/ogg',
        'oga' => 'audio/ogg',
        'opus' => 'audio/ogg',
        'weba' => 'audio/webm',
    ]);

// Plugin Basic Information
$kleeja_plugin['video_player']['information'] = [
    // The casual name of this plugin, anything can a human being understands
    'plugin_title' => [
        'en' => 'Video & Audio Player',
        'ar' => 'مشغل فيديو وصوت',
    ],
    // Who wrote this plugin?
    'plugin_developer' => 'Kleeja.net',
    // This plugin version
    'plugin_version' => VIDEO_PLAYER_VERSION,
    // Explain what is this plugin, why should I use it?
    'plugin_description' => [
        'en' => 'Play video and audio files in their download page, with the Video.js player',
        'ar' => 'تشغيل ملفات الفيديو والصوت في صفحة تحميلها، بمشغل Video.js',
    ],
    // Min version of Kleeja that's requiered to run this plugin
    'plugin_kleeja_version_min' => '3.2.0',
    // Max version of Kleeja that support this plugin, use 0 for unlimited
    'plugin_kleeja_version_max' => '4.9',
    // Should this plugin run before others?, 0 is normal, and higher number has high priority
    'plugin_priority' => 10,
];

//after installation message, you can remove it, it's not required
$kleeja_plugin['video_player']['first_run']['ar'] = '
شكراً لاستخدامك هذه الإضافة قم بمراسلتنا بالأخطاء عند ظهورها على البريد: <br>
info@kleeja.net
';

$kleeja_plugin['video_player']['first_run']['en'] = '
Thanks for using this plugin, to report bugs contact us:
<br>
info@kleeja.net
';

// Plugin Installation function
$kleeja_plugin['video_player']['install'] = function ($plg_id) {
    // nothing to install, the player keeps no settings
};

//Plugin update function, called if plugin is already installed but version is different than current
$kleeja_plugin['video_player']['update'] = function ($old_version, $new_version) {
    // the download template is compiled with the player in it, so the copies with the old player are deleted,
    // without delete_cache(): plugins are still loading here, and it runs a hook
    if (version_compare($old_version, '2.0', '<')) {
        foreach (glob(PATH . 'cache/tpl_download*.php') ?: [] as $file) {
            @unlink($file);
        }
    }
};

// Plugin Uninstallation, function to be called at unistalling
$kleeja_plugin['video_player']['uninstall'] = function ($plg_id) {
    // nothing to delete
};

// Plugin functions
$kleeja_plugin['video_player']['functions'] = [
    // the player in the download template, shown by b4_showsty_downlaod_id_filename
    // its tags are those of videojs.org: <media-i18n> follows the language of the page,
    // the player owns the state, the skin draws the controls, and the browser's <video> or <audio> plays the file
    'style_parse_func' => function ($args) {
        if ($args['template_name'] !== 'download') {
            return;
        }

        $html =
            $args['html'] .
            '
<IF NAME="show_video_player_code">
<div class="kj-player kj-player-video row justify-content-center" data-kj-player>
    <div class="col-lg-10 col-xl-9">
        <media-i18n>
            <video-player content-title="{video_player_title}"<IF NAME="video_thumb"> poster="{video_thumb}"</IF>>
                <video-skin class="kj-player-skin">
                    <video src="{video_path}<UNLESS NAME="video_thumb">#t=0.1</UNLESS>" preload="metadata" playsinline></video>
                </video-skin>
            </video-player>
        </media-i18n>
    </div>
</div>
</IF>
<IF NAME="show_audio_player_code">
<div class="kj-player kj-player-audio row justify-content-center" data-kj-player>
    <div class="col-lg-10 col-xl-9">
        <media-i18n>
            <audio-player content-title="{video_player_title}">
                <audio-skin class="kj-player-skin">
                    <audio src="{video_path}" preload="metadata"></audio>
                </audio-skin>
            </audio-player>
        </media-i18n>
    </div>
</div>
</IF>';

        return compact('html');
    },

    // decide the player of the file, before the download page is shown
    'b4_showsty_downlaod_id_filename' => function ($args) {
        global $config;

        $show_video_player_code = $show_audio_player_code = false;
        $video_path = $video_thumb = $video_mime_type = '';
        $video_player_title = $args['name'] ?? '';
        $file_info = $args['file_info'] ?? [];
        $type = strtolower((string) ($file_info['type'] ?? ''));

        if (isset(VIDEO_PLAYER_TYPES[$type])) {
            $video_mime_type = VIDEO_PLAYER_TYPES[$type];
            $show_audio_player_code = strpos($video_mime_type, 'audio/') === 0;
            $show_video_player_code = !$show_audio_player_code;

            // a full link, each part encoded, the page can be served at a pretty url
            $video_path =
                $config['siteurl'] .
                implode('/', array_map('rawurlencode', explode('/', trim($file_info['folder'], '/') . '/' . $file_info['name'])));

            // kj_ftp gives the link of the files it keeps on a ftp server here, and video_thumb is the poster of the video
            extract(runHook('plugin:video_player:do_display', get_defined_vars()));

            $video_path = htmlspecialchars($video_path, ENT_QUOTES);
            $video_thumb = htmlspecialchars($video_thumb, ENT_QUOTES);
        }

        // the header adds the player's files only to the pages that show it
        $video_player_kind = $show_video_player_code ? 'video' : ($show_audio_player_code ? 'audio' : '');

        return compact(
            'show_video_player_code',
            'show_audio_player_code',
            'video_path',
            'video_thumb',
            'video_mime_type',
            'video_player_title',
            'video_player_kind'
        );
    },

    // the files of the player, only in the page that shows it: the stylesheet, the module of Video.js
    // that registers the elements of that player, then assets/player.js, the modules run in this order after the page is parsed
    'Saaheader_links_func' => function ($args) {
        global $config;

        $kind = $GLOBALS['video_player_kind'] ?? '';

        if (!defined('IN_DOWNLOAD') || ($kind !== 'video' && $kind !== 'audio')) {
            return;
        }

        $assets = $config['siteurl'] . KLEEJA_PLUGINS_FOLDER . '/video_player/assets/';
        $modules = $kind === 'audio' ? [VIDEO_PLAYER_CDN . VIDEO_PLAYER_CDN_I18N] : [];
        $modules[] = VIDEO_PLAYER_CDN . $kind . '.js';
        $modules[] = $assets . 'player.js?v=' . VIDEO_PLAYER_VERSION;

        $extra = $args['extra'] . '<link rel="stylesheet" href="' . $assets . 'player.css?v=' . VIDEO_PLAYER_VERSION . '">' . "\n";

        foreach ($modules as $module) {
            $extra .= '<script type="module" src="' . $module . '"></script>' . "\n";
        }

        return compact('extra');
    },

    // the guide of the plugin on the help page of the control panel, its words are in language/help_{code}.php
    'admin_help_guides' => function ($args) {
        $help_guides = $args['help_guides'];
        $words = video_player_help_words();

        // the name of the plugin is the key, so Kleeja knows that the plugin has its guide, and shows its icon.
        // The plugin has no page in the control panel, so the guide has no 'page' and no 'link'
        $help_guides['video_player'] = [
            'group' => 'plugins',
            'title' => $words['VIDEO_PLAYER_HELP_TITLE'],
            'intro' => $words['VIDEO_PLAYER_HELP_INTRO'],
            // tips and warnings are shown beside the others on wide screens
            'sections' => [
                video_player_help_section($words, 'features', 'FEATURE'),
                video_player_help_section($words, 'steps', 'STEP'),
                video_player_help_section($words, 'faq', 'FAQ'),
                video_player_help_section($words, 'tips', 'TIP'),
                video_player_help_section($words, 'warnings', 'WARNING'),
            ],
        ];

        return compact('help_guides');
    },
];

/**
 * special functions
 */

if (!function_exists('video_player_help_words')) {
    /**
     * the words of the guide, in the language of the control panel, the missing ones in English
     * @return array
     */
    function video_player_help_words()
    {
        global $config;

        $words = (array) require __DIR__ . '/language/help_en.php';
        $language = preg_replace('/[^a-z0-9_-]/i', '', (string) ($config['language'] ?? ''));
        $translation = __DIR__ . "/language/help_{$language}.php";

        if ($language !== '' && $language !== 'en' && file_exists($translation)) {
            // in its own line, Prettier drops the brackets of (require $translation) + $words
            $translated = require $translation;
            $words = (array) $translated + $words;
        }

        return $words;
    }

    /**
     * a section of the guide from its numbered words, like VIDEO_PLAYER_HELP_TIP_1, .._TIP_2 ..
     * its title, when it has its own, is VIDEO_PLAYER_HELP_TIP_TITLE
     * @param  array  $words
     * @param  string $type  how Kleeja shows it: features, steps, tips, warnings or faq
     * @param  string $name  the name of its words, VIDEO_PLAYER_HELP_{name}_1
     * @return array
     */
    function video_player_help_section($words, $type, $name)
    {
        $prefix = 'VIDEO_PLAYER_HELP_' . $name;
        $section = ['type' => $type, 'title' => $words[$prefix . '_TITLE'] ?? '', 'items' => []];

        for ($n = 1; isset($words[$prefix . ($type === 'faq' ? '_Q_' : '_') . $n]); $n++) {
            $section['items'][] =
                $type === 'faq'
                    ? ['q' => $words[$prefix . '_Q_' . $n], 'a' => $words[$prefix . '_A_' . $n] ?? '']
                    : $words[$prefix . '_' . $n];
        }

        return $section;
    }
}
