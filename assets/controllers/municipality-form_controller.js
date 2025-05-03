import LiveForm from "./live-form_controller.js";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends LiveForm {

    connect() {
        super.connect();

        window.addEventListener('type--entity-plus:update', (event) => {
            for(let item in event.detail.data){
                try{
                    this.component.set((item), event.detail.data[item]);
                }catch (e) {}
            }
            this.component.render();
        });
    }

}
