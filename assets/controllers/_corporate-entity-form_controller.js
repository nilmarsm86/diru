import LiveForm from "./live-form_controller.js";
import {getComponent} from '@symfony/ux-live-component';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/

export default class extends LiveForm {

    connect() {
        super.connect();

        window.addEventListener('type--entity-plus:update', (event) => {
            for (let item in event.detail.data) {
                try {
                    this.component.set((item), event.detail.data[item]);
                } catch (e) {
                }
            }
            this.component.render();
        });
    }

    async initialize() {
        super.initialize();

        this.component = await getComponent(this.element);
        this.component.on('render:finished', (component) => {
            const selects = this.element.querySelectorAll('select[data-type--entity-plus-target=select]');
            selects.forEach((select) => {
                const firstOption = select.options[0];
                for (let i = 0; i < select.options.length; i++) {
                    let item = select.options[i];
                    if(firstOption !== item){
                        if(firstOption.value === item.value){
                            select.options.remove(0);
                        }
                    }
                }
            });
        });
    }

}
