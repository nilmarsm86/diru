import AbstractController from "../controllers/AbstractController.js";
import {useFilter} from "../behaviors/use-filter.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {
    static values = {
        queryName: String,
        data: String,
    }

    connect() {
        useFilter(this);

        this.element.addEventListener('click', (event) => this.filter(event, 'filter', this.queryNameValue, this.dataValue));
    }

}
