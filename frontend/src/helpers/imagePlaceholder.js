import imagePlaceholder from '@/assets/image-placeholder.svg'
import userPlaceholder from '@/assets/user-placeholder.svg'
import logoPlaceholder from '@/assets/logo-placeholder.svg'

export { imagePlaceholder, userPlaceholder, logoPlaceholder }

/**
 * Swap every <img> that fails to load for a placeholder, app-wide.
 *
 * `error` events don't bubble, so the listener runs in the capture phase.
 * An image can pick a specific placeholder with `data-placeholder="..."`;
 * otherwise the generic one is used. `data-fallback-applied` stops a loop
 * if the placeholder itself can't load.
 */
export function registerImageFallback() {
    document.addEventListener('error', (event) => {
        const img = event.target
        if (!(img instanceof HTMLImageElement) || img.dataset.fallbackApplied) return

        img.dataset.fallbackApplied = 'true'
        img.src = img.dataset.placeholder || imagePlaceholder
    }, true)

    // A reused <img> whose src changes gets a fresh chance to load.
    document.addEventListener('load', (event) => {
        const img = event.target
        if (!(img instanceof HTMLImageElement)) return

        const placeholder = img.dataset.placeholder || imagePlaceholder
        if (!img.src.endsWith(placeholder)) delete img.dataset.fallbackApplied
    }, true)
}
