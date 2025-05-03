import AbstractController from "../../AbstractController.js";

export const NAVIGATE = "App\\Component\\Twig\\Table\\Navigation_change";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {
    static values = {
        queryName: String,
        page: Number,
    }

    static targets = ["pageLink"];

    connect() {
        let currentPath = new URL(document.location);
        if (currentPath.searchParams.has(this.queryNameValue)) {
            currentPath.searchParams.set(this.queryNameValue, this.pageValue);
            history.pushState({}, '', currentPath);
        }

        this.pageLinkTargets.forEach((pageLink) => {
            pageLink.addEventListener('click', this.onChange.bind(this));
        });
    }

    /**
     * Show amount elements to show on table
     * @param event change select event
     */
    onChange(event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        let currentPath = new URL(document.location);
        currentPath.searchParams.set(this.queryNameValue, event.currentTarget.dataset.number);
        super.dispatch(NAVIGATE, {detail: {url: currentPath}});
        // document.location = currentPath.toString();
    }

}
