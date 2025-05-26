import {Controller} from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends Controller {

    static targets = ["field", "total"];
    connect(){
        this.summation();

        this.fieldTargets.forEach((field) => {
            field.addEventListener('input', (event) => {
                this.summation();
            });
        });
    }

    summation(){
        this.totalTarget.innerText = this.fieldTargets.reduce((accumulator, field) => accumulator + Number(field.value), 0);
    }



}
