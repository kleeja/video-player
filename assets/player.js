/**
 * What the video and the audio player of the download page share, on top of the skins of Video.js:
 * - the volume of the visitor is kept from one file to the next, as videojs.org/docs/framework/html/guides/user-preferences
 * - the player is hidden when the browser can't play the file, the download button is still there
 * - the video box takes the shape of the video, so a portrait video isn't drawn small in a wide box
 * - the name of the file is shown in the media controls of the system (lock screen, notifications)
 * - the browser's own controls are shown when Video.js couldn't be loaded from the CDN
 */

const VOLUME_KEY = 'kleeja-player-volume';

for (const player of document.querySelectorAll('[data-kj-player] video-player, [data-kj-player] audio-player')) {
    const box = player.closest('[data-kj-player]');
    const media = player.querySelector('video, audio');

    if (!media) {
        continue;
    }

    hideWhenUnplayable(box, media);

    if (media.localName === 'video') {
        fitVideo(box, media);
    }

    // module scripts run in their order, so the module of Video.js before this one has defined the player by now,
    // unless it couldn't be loaded (the CDN is blocked or offline), then the file can still be played
    if (!customElements.get(player.localName)) {
        media.controls = true;
        continue;
    }

    rememberVolume(player.store);
    announceTitle(player, media);
}

// storage can be blocked (private windows, embedded pages), the player works without it
function readVolume() {
    try {
        const value = JSON.parse(localStorage.getItem(VOLUME_KEY) ?? 'null');

        return Number.isFinite(value?.volume) && typeof value?.muted === 'boolean' ? value : null;
    } catch {
        return null;
    }
}

function saveVolume(value) {
    try {
        localStorage.setItem(VOLUME_KEY, JSON.stringify(value));
    } catch {
        // no storage, nothing is kept
    }
}

// the actions of the store throw until it is attached to the media, so the saved volume waits for that,
// and nothing is saved before it either, the default volume would replace the saved one
function rememberVolume(store) {
    const saved = readVolume();

    if (saved) {
        const restore = () => {
            if (!store.target) {
                return false;
            }

            store.state.setVolume(saved.volume);

            // setVolume unmutes above zero, so the saved mute comes after it
            if (saved.muted) {
                store.state.setMuted(true);
            }

            return true;
        };

        if (!restore()) {
            const unsubscribe = store.subscribe(() => {
                if (restore()) {
                    unsubscribe();
                }
            });
        }
    }

    let last = saved ?? {};

    // it runs on every change of the state, so only a new volume or mute is saved
    store.subscribe(() => {
        if (!store.target) {
            return;
        }

        const { volume, muted } = store.state;

        if (volume !== last.volume || muted !== last.muted) {
            last = { volume, muted };
            saveVolume(last);
        }
    });
}

// the browser loads the metadata of the file first, and says there that it can't play it (or can't load it),
// the error may come before this module runs, so it is checked at once too
function hideWhenUnplayable(box, media) {
    const check = () => {
        if (media.error?.code === MediaError.MEDIA_ERR_SRC_NOT_SUPPORTED && media.played.length === 0) {
            box.hidden = true;
        }
    };

    media.addEventListener('error', check);
    check();
}

// the stylesheet draws a wide box until the video tells its own width and height
function fitVideo(box, media) {
    const fit = () => {
        if (media.videoWidth && media.videoHeight) {
            box.style.setProperty('--kj-video-ratio', String(media.videoWidth / media.videoHeight));
        }
    };

    media.addEventListener('loadedmetadata', fit);
    fit();
}

// the player that plays gives its title and poster to the system
function announceTitle(player, media) {
    const title = player.getAttribute('content-title');

    if (!title || !('mediaSession' in navigator) || typeof MediaMetadata !== 'function') {
        return;
    }

    media.addEventListener('play', () => {
        const poster = player.getAttribute('poster');

        navigator.mediaSession.metadata = new MediaMetadata({
            title,
            artwork: poster ? [{ src: new URL(poster, document.baseURI).href }] : [],
        });
    });
}
