import AbstractController from "./AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static targets = ["field"];

    connect() {
        this.setCode();
    }

    setCode(event) {
        let inputGroupText = this.fieldTarget.parentElement.querySelector('.input-group-text');
        if (inputGroupText.innerText === 'US$') {
            inputGroupText.innerText = 'USD';
        }
    }

}
