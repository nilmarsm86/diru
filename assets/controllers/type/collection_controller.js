import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        index: Number,
        prototype: String,
    }

    addCollectionElement(event) {
        const code = this.prototypeValue.replace(/__name__/g, this.indexValue);
        const nodes = new DOMParser().parseFromString(code, 'text/html').body.childNodes;
        this.element.append(...nodes);
        this.indexValue++;
    }
}