import AbstractController from "../../AbstractController.js";
import {useFilter} from "../../../behaviors/use-filter.js";

export const FILTER = "App\\Component\\Twig\\Card\\FilterDropDown_filter";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {
    static values = {
        queryName: String,
    }

    static targets = [ "option" ];

    connect() {
        useFilter(this);

        this.optionTargets.forEach((link) => {
            link.addEventListener('click', (event) => this.filter(event, FILTER, this.queryNameValue, link.dataset.value));
        });
    }

    // /**
    //  * Show amount elements to show on table
    //  * @param event change select event
    //  */
    // filter(event){
    //     event.preventDefault();
    //     event.stopImmediatePropagation();
    //
    //     let currentPath = new URL(document.location);
    //     currentPath.searchParams.set(this.queryNameValue, event.target.dataset.value);
    //     super.dispatch(FILTER, {detail:{url:currentPath}});
    // }

}
