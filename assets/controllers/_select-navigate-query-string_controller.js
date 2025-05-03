import { Controller } from '@hotwired/stimulus';
import { getComponent } from '@symfony/ux-live-component';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static values = {
        queryName: String,
    }

    select = null;

    connect() {
        this.select = this.element.querySelector('select');
        // this.select.addEventListener('change', this.onChange.bind(this));
    }

    async initialize() {
        this.component = await getComponent(this.element);
    }

    /**
     * Cambiar la cantidad de elementos a mostrar por pagina
     * @param event change select event
     */
    onChange(event){
        // let currentPath = new URL(document.location);
        // currentPath.searchParams.set(this.queryNameValue, event.currentTarget.value);
        // super.dispatch('select-navigate-query-string:onChange', {detail:{url:currentPath}});
        // document.location = currentPath.toString();
    }

}