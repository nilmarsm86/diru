import AbstractController from "./AbstractController.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends AbstractController {

    static targets = ["field", "total"];
    connect(){
        this.summation();

        this.fieldTargets.forEach((field) => {
            field.addEventListener('input', (event) => {
                if(Number(field.value) < 0){
                    field.value = Number(field.value) * -1;
                }
                this.summation();
            });
        });

        window.addEventListener('live-form:submitEnd', (event)=>{
            this.summation();
        });
    }

    summation(){
        this.totalTarget.innerText = this.fieldTargets.reduce((accumulator, field) => accumulator + Number(field.value), 0);
    }



}
