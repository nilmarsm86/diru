import AbstractController from "./AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static targets = ["select", "field"];
    connect(){
        this.setCode();
        this.selectTarget.addEventListener('change', this.setCode.bind(this));
    }

    setCode(event){
        let code = this.selectTarget.selectedOptions.item(0).dataset.code ?? 'CUP';

        this.fieldTargets.forEach((field) => {
            let inputGroupText = field.parentElement.querySelector('.input-group-text');
            inputGroupText.innerText = code;
        });
    }

}
