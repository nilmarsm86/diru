import {Controller} from '@hotwired/stimulus';

export default class AbstractController extends Controller {
    /**
     * Dispatch event info
     *
     * @param eventName
     * @param options
     * @returns {*}
     */
    dispatch(eventName, options) {
        const event = super.dispatch(eventName, options);
        console.groupCollapsed(`Trigger ${event.type}`);
        console.log(event.detail);
        console.groupEnd();
        return event;
    }

    /**
     * Add a listener
     * @param target
     * @param type
     * @param listener
     * @param options
     * @param identifier
     * @return array
     */
    addListener(target, type, listener, options = {}, identifier = null) {
        let ident = identifier ? identifier.replaceAll('/', '--') : this.identifier;
        let normalEvent = target.addEventListener(type, listener, options);
        let stimulusEvent = target.addEventListener(ident + ':' + type, listener, options);
        return [normalEvent, stimulusEvent];
    }

    /**
     * Add a listener
     * @param target
     * @param type
     * @param listener
     * @param options
     */
    removeListener(target, type, listener, options = {}, identifier = null) {
        let ident = identifier ? identifier.replaceAll('/', '--') : this.identifier;
        target.removeEventListener(type, listener, options);
        target.removeEventListener(ident + ':' + type, listener, options);
    }

    /**
     * Get a controller
     * @param element
     * @param identifier
     * @return {*}
     */
    getController(element, identifier) {
        let ident = identifier.replaceAll('/', '--');
        return this.application.getControllerForElementAndIdentifier(element, ident);
    }
}