import AbstractController from "../../AbstractController.js";

export const FILTER = "App\\Component\\Twig\\Card\\Refresh_filter";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    connect() {
        this.element.addEventListener('click', this.refresh.bind(this));
    }

    /**
     * Show amount elements to show on table
     * @param event change select event
     */
    refresh(event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        let currentPath = new URL(document.location);
        currentPath.search = '';
        super.dispatch(FILTER, {detail: {url: currentPath}});
    }

}
