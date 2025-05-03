import AbstractController from "../../AbstractController.js";
import {useFilter} from "../../../behaviors/use-filter.js";

export const CHANGE = "App\\Component\\Twig\\Table\\Amount_change";
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {
    static values = {
        queryName: String,
    }

    static targets = [ "select" ];

    connect() {
        useFilter(this);
        this.element.addEventListener('change', (event) => this.filter(event, CHANGE, this.queryNameValue, this.selectTarget.value));
    }

    // /**
    //  * Show amount elements to show on table
    //  * @param event change select event
    //  */
    // onChange(event){
    //     event.preventDefault();
    //     event.stopImmediatePropagation();
    //
    //     let currentPath = new URL(document.location);
    //     currentPath.searchParams.set(this.queryNameValue, this.selectTarget.value);
    //     super.dispatch(CHANGE, {detail:{url:currentPath}});
    //     // document.location = currentPath.toString();
    // }

}
