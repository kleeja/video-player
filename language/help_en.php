<?php
//
// video_player, its guide on the help page of the control panel
// English
//
// The guide is built by the admin_help_guides hook in init.php, the texts here can have HTML.
//  - a list is numbered: VIDEO_PLAYER_HELP_TIP_1, VIDEO_PLAYER_HELP_TIP_2 ... it ends at the first missing number,
//    so an item can be added or removed without touching the code
//  - VIDEO_PLAYER_HELP_{LIST}_TITLE is the title of the list, a list without it takes the title that Kleeja gives its type
//  - questions and answers are VIDEO_PLAYER_HELP_FAQ_Q_1 and VIDEO_PLAYER_HELP_FAQ_A_1
//  - names in <strong> are written as the control panel shows them, its words are in lang/en/
//  - the extensions are those of VIDEO_PLAYER_TYPES in init.php, and the controls and keys those of the skins of Video.js
//

return [
    'VIDEO_PLAYER_HELP_TITLE' => 'Video & Audio Player',
    'VIDEO_PLAYER_HELP_INTRO' =>
        'Let visitors watch a video or listen to an audio file right on its download page, before they decide to download it. The player is built on Video.js, the open-source web video player, and needs no setup.',

    //
    // what the plugin does
    //
    'VIDEO_PLAYER_HELP_FEATURE_1' =>
        'Shows a player under the file details on the download page of video files (<code>mp4</code>, <code>m4v</code>, <code>webm</code>, <code>mov</code>, <code>mkv</code>, <code>ogv</code>, <code>3gp</code>) and audio files (<code>mp3</code>, <code>m4a</code>, <code>m4b</code>, <code>aac</code>, <code>wav</code>, <code>flac</code>, <code>ogg</code>, <code>oga</code>, <code>opus</code>, <code>weba</code>).',
    'VIDEO_PLAYER_HELP_FEATURE_2' =>
        'The video player has a seek bar, volume, playback speed, picture-in-picture and full screen. On touch screens, a double tap on either side skips back or ahead, and a double tap in the middle goes full screen.',
    'VIDEO_PLAYER_HELP_FEATURE_3' =>
        'The audio player is a compact bar with play, 10 seconds back and ahead, a seek bar, volume and playback speed.',
    'VIDEO_PLAYER_HELP_FEATURE_4' =>
        'Keyboard shortcuts once the player is clicked: <kbd>Space</kbd> or <kbd>K</kbd> plays and pauses, the arrow keys seek and change the volume, <kbd>M</kbd> mutes, <kbd>0</kbd> to <kbd>9</kbd> jump through the file, and in the video player <kbd>F</kbd> goes full screen and <kbd>I</kbd> starts picture-in-picture.',
    'VIDEO_PLAYER_HELP_FEATURE_5' =>
        'The video box takes the shape of the video, so a portrait video from a phone isn\'t drawn small in a wide box. The player remembers the volume of each visitor from one file to the next, and shows the name of the file in the media controls of their phone or computer.',
    'VIDEO_PLAYER_HELP_FEATURE_6' =>
        'Follows the language and the direction of the page, Video.js speaks Arabic, English and about 50 other languages, and takes the color, the font and the corners of your style. The download button of the page stays as it is.',

    //
    // how to use it
    //
    'VIDEO_PLAYER_HELP_STEP_TITLE' => 'Play your first video',
    'VIDEO_PLAYER_HELP_STEP_1' =>
        'Kleeja allows no video or audio extension out of the box. Open <strong>Users & Groups</strong>, select <strong>Files\' Extensions Settings</strong> on each group, and use <strong>Add a new extension</strong> for the ones you want, such as <code>mp4</code> and <code>mp3</code>.',
    'VIDEO_PLAYER_HELP_STEP_2' =>
        'Videos are large: on the same page, raise the <strong>Size</strong> of each new extension. It is in Kilobytes, so <code>512000</code> allows files up to 500 MB.',
    'VIDEO_PLAYER_HELP_STEP_3' =>
        'Open <strong>Settings</strong>, then <strong>Upload settings</strong>, and make sure that these extensions aren\'t listed in <strong>Live Extensions (No waiting page)</strong>. Those files skip the download page, so they skip the player too.',
    'VIDEO_PLAYER_HELP_STEP_4' =>
        'Upload a video, then open its download page. The player appears under the file details.',

    //
    // common questions
    //
    'VIDEO_PLAYER_HELP_FAQ_Q_1' => 'The player doesn\'t appear on the download page of a video. Why?',
    'VIDEO_PLAYER_HELP_FAQ_A_1' =>
        'The player appears only for the extensions listed above, and only on their download page. Check that the extension isn\'t in <strong>Live Extensions (No waiting page)</strong>, since those files never show that page. The player also hides itself when the browser can\'t play the file, see the next question.',
    'VIDEO_PLAYER_HELP_FAQ_Q_2' => 'Some videos play in one browser and not in another. Why?',
    'VIDEO_PLAYER_HELP_FAQ_A_2' =>
        'The browser plays the file itself, so it must know its format: Safari doesn\'t play <code>mkv</code>, and only Firefox still plays <code>ogv</code>. When the browser can\'t play a file, the player hides itself, and visitors still have the download button. MP4 files with H.264 video and AAC audio, and MP3 files, play in every modern browser.',
    'VIDEO_PLAYER_HELP_FAQ_Q_3' => 'The player shows the plain controls of the browser. Why?',
    'VIDEO_PLAYER_HELP_FAQ_A_3' =>
        'The browser couldn\'t load Video.js from jsDelivr: a firewall, a content blocker, or a network without internet access. The file still plays, with the controls that the browser gives every video and audio file.',
    'VIDEO_PLAYER_HELP_FAQ_Q_4' => 'Visitors can\'t jump ahead in a video, or it starts again from the beginning. Why?',
    'VIDEO_PLAYER_HELP_FAQ_A_4' =>
        'To jump to a part that isn\'t loaded yet, the browser asks the web server for that part alone (a range request). Apache and Nginx answer them for files in the upload folder, but a proxy or a CDN in front of your site may not.',
    'VIDEO_PLAYER_HELP_FAQ_Q_5' => 'Does playing a file in the player count as a download?',
    'VIDEO_PLAYER_HELP_FAQ_A_5' =>
        'No. The player reads the file straight from the upload folder, so it neither counts a download nor waits for the <strong>Waiting period</strong> of the group. The download button of the page still does both.',
    'VIDEO_PLAYER_HELP_FAQ_Q_6' =>
        'My files are kept on FTP servers by Multi-FTP Storage. Does the player work with them?',
    'VIDEO_PLAYER_HELP_FAQ_A_6' =>
        'Yes. Multi-FTP Storage gives the player the <strong>Public URL</strong> of each file on its server. That address must start with <code>https://</code> when your site does, because browsers don\'t play a file from an <code>http://</code> address on an <code>https://</code> page.',

    //
    // beside the guide
    //
    'VIDEO_PLAYER_HELP_TIP_1' =>
        'The player helps most on sites that share clips, lessons, podcasts or music: visitors check that a file is the one they want before they download it.',
    'VIDEO_PLAYER_HELP_TIP_2' =>
        'The player is loaded only on the download pages of video and audio files, and the browser reads only the beginning of the file until the visitor presses play, so your pages stay light.',

    'VIDEO_PLAYER_HELP_WARNING_1' =>
        'The player reads each file at its direct address in the upload folder. A web server rule that blocks direct access to that folder, such as <code>deny all;</code> or <code>internal;</code> on Nginx, blocks the player too.',
    'VIDEO_PLAYER_HELP_WARNING_2' =>
        'That direct address is in the page, so visitors can save the file from it without the <strong>Waiting period</strong> or the download counter. Don\'t rely on them to hold back video and audio files.',
];
