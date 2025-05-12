import AbstractController from "./AbstractController.js";
import {useContentLoader} from "../behaviors/content-loader/use-content-loader.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static values = {
        url: {type: String, default: ''},
        container: {type: String, default: ''}
    };

    connect() {
        useContentLoader(this, {
            url: this.urlValue,
            container: document.querySelector(this.containerValue),
            // eventLoadedName: this.eventLoadedNameValue,
        });

        this.element.addEventListener('click', this.refreshContent.bind(this));
    }

}
