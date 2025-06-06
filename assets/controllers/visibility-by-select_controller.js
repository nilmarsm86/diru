import AbstractController from "./AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
export default class extends AbstractController {
    static targets = ['select', 'detail'];
    static values = {
        openData: String
    };

    connect() {
        this.showReason();
        this.selectTarget.addEventListener('change', this.showReason.bind(this));
    }

    showReason(event) {
        console.log(this.selectTarget.value);
        this.detailTarget.open = this.selectTarget.value === String(this.openDataValue);
    }
}
