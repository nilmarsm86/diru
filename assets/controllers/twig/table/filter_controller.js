import AbstractController from "../../AbstractController.js";

export const BLUR = "App\\Component\\Twig\\Table\\Filter_blur";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {
    static values = {
        queryName: String,
    }

    static targets = [ "input" ];

    connect() {
        this.inputTarget.addEventListener('blur', this.onFilter.bind(this));
    }

    /**
     * Show amount elements to show on table
     * @param event change select event
     */
    onFilter(event){
        event.preventDefault();
        event.stopImmediatePropagation();

        let currentPath = new URL(document.location);
        currentPath.searchParams.set(this.queryNameValue, this.inputTarget.value);
        super.dispatch(BLUR, {detail:{url:currentPath}});
        // document.location = currentPath.toString();
    }

}
